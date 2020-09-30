<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreMeasurementRequest;
use App\Services\PETService;
use App\Station;
use DateTimeInterface;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Routing\Controller;
use App\Measurement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;

class MeasurementController extends Controller
{
    /**
     * Checks request for validity and stores the input data if it is.
     *
     * Returns a JSON message with outcome of validation.
     *
     * @param StoreMeasurementRequest $request
     * @return false|Response|string
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(StoreMeasurementRequest $request)
    {

        $validated = $request->validated();

        if ($this->requestCameBeforeTimeout($validated)) {

            $msg = 'Already added measurement less than ' . env('REQUEST_TIMEOUT_IN_SECONDS') . ' seconds ago.';

            return new Response(['error' => $msg],Response::HTTP_PRECONDITION_FAILED);

        }

        $evaluated = $this->evaluateInput($validated);

        $measurement = Measurement::create($evaluated);

        return json_encode(['measurement.created' => 'Measurement created with id ' . $measurement->id]);

    }

    /**
     * @param PETService $petservice
     * @param null $startDate
     * @param null $endDate
     * @param string $stations
     * @param null $grouping
     * @param string $aggregation
     * @param string $columns
     * @param string $order
     * @return Application|ResponseFactory|Response
     * @throws Exception
     */
    public function getJSON(PETService $petservice,
                            $startDate = null,
                            $endDate = null,
                            $stations = 'all',
                            $grouping = null,
                            $aggregation = 'avg',
                            $columns = 'all',
                            $order = 'desc') {

        // Define standard variables

        $includePET = false;

        // Collection that will contain all the values to insert into the SQL query
        $bindings = collect();

        // All of the known measurement columns
        // PET isn't considered a valid column, but will be add in the output
        $columnsWhitelist = collect([
            'th_temp',
            'th_hum',
            'th_dew',
            'th_heatindex',
            'thb_temp',
            'thb_hum',
            'thb_dew',
            'thb_press',
            'thb_seapress',
            'wind_wind',
            'wind_avgwind',
            'wind_dir',
            'wind_chill',
            'rain_rate',
            'rain_total',
            'uv_index',
            'sol_rad',
            'sol_evo',
            'sun_total',
        ]);
        // All of the known weather stations
        $allAvailableStations = Station::all();
        $stationsWhitelist = collect([]);
        foreach ($allAvailableStations as $station) {
            $stationsWhitelist->add($station->code);
        }

        // All of the columns necessary to calculate the PET value
        $columnsNecessaryForPET = collect([
            'th_temp',
            'sol_rad',
            'th_hum',
            'wind_avgwind'
        ]);

        // Get and filter measurements based on start and end times
        $timeFormat = DateTimeInterface::RFC3339_EXTENDED;
        if($startDate!=null and $startDate!="null") {
            $betweenStart = Carbon::createFromFormat($timeFormat, $startDate);
        } else {
            $betweenStart = Carbon::createFromTimestamp(0);
        }
        if($endDate!=null and $endDate!="null") {
            $betweenEnd= Carbon::createFromFormat($timeFormat, $endDate);
        } else {
            $betweenEnd = Carbon::now();
        }

        // Add first part of select statement to query
        if ($grouping === 'yearly') {
            $timeSelection = 'YEAR(`created_at`)';
        }
        else if ($grouping === 'monthly') {
            $timeSelection = 'YEAR(`created_at`), MONTH(`created_at`)';
        }
        else if ($grouping === 'weekly') {
            $timeSelection = "YEAR(`created_at`), WEEK(`created_at`)";
        }
        else if ($grouping === 'daily') {
            $timeSelection = 'YEAR(`created_at`), MONTH(`created_at`), DAY(`created_at`)';
        }
        else if ($grouping === 'hourly') {
            $timeSelection = 'YEAR(`created_at`), MONTH(`created_at`), DAY(`created_at`), HOUR(`created_at`)';
        }
        else {
            $grouping = null;
            $aggregation = null;
            $timeSelection = '`created_at`';
        }
        $query = 'SELECT `station_name`, ' . $timeSelection;

        // Add columns to query
        $columns = collect(explode(',', $columns));
        // If PET is included then set the flag and remove the value from the collection
        if (in_array('PET', $columns->toArray())) {
            $includePET = true;
            // Remove PET from columns because it isn't a value in the database
            $columns = $columns->reject(function ($value, $key) {
                return $value === 'PET';
            });
            // Add necessary columns for calculation
            $columns = $columns->merge($columnsNecessaryForPET);
        }
        // If all is included then set columns to all of the allowed columns
        if (in_array('all', $columns->toArray())) {
            $columns = collect($columnsWhitelist);
            $includePET = true;
        }
        // Remove any disallowed columns
        $columns = $columns->intersect($columnsWhitelist);
        // Check if columns is empty
        if (count($columns) < 1) {
            $query .= ' ';
        }
        else {
            $query .= ', ';
        }
        // Add the columns to the query
        foreach ($columns as $column) {
            if ($aggregation === 'avg') {
                $columnValue = $columns->last() === $column ? 'AVG(`' . $column . '`) ' : 'AVG(`' . $column . '`), ';
            }
            else if ($aggregation === 'min') {
                $columnValue = $columns->last() === $column ? 'MIN(`' . $column . '`) ' : 'MIN(`' . $column . '`), ';
            }
            else if ($aggregation === 'max') {
                $columnValue = $columns->last() === $column ? 'MAX(`' . $column . '`) ' : 'MAX(`' . $column . '`), ';
            }
            else {
                if ($grouping !== null) {
                    $columnValue = $columns->last() === $column ? 'AVG(`' . $column . '`) ' : 'AVG(`' . $column . '`), ';
                }
                else {
                    $columnValue = $columns->last() === $column ? '`' . $column . '` ' : '`' . $column . '`, ';
                }
            }
            $query .= $columnValue;
        }

         // Add second part of select statement to query
        $query .= 'FROM `measurements` WHERE ';

        // Add date condition
        if ($betweenStart !== null and $betweenEnd !== null) {
            $bindings->add($betweenStart->format($timeFormat));
            $bindings->add($betweenEnd->format($timeFormat));
            $query .= '`created_at` BETWEEN DATE(?) AND DATE(?) AND ';
        }

        // Process stations parameter
        $stations = collect(explode(',', $stations));
        if (in_array('all', $stations->toArray())) {
            $stations = $stationsWhitelist;
        }
        // Remove any disallowed stations
        $stations = $stations->intersect($stationsWhitelist);
        // Add stations to bindings
        $bindings = $bindings->merge($stations);

        // Add station condition
        $query .= '`station_name` IN (';
        foreach ($stations as $station) {
            $query .= $stations->last() === $station ? '?' : '?, ';
        }
        $query .= ') ';

        if ($grouping !== null) {
            $query .= 'GROUP BY `station_name`, ' . $timeSelection;
        }

        // Add order by created_at to query
        if ($order === 'asc') {
            $order = 'ASC';
        }
        else {
            $order = 'DESC';
        }
        $query .= 'ORDER BY ';
        $timeSelectionCollection = collect(explode(', ', $timeSelection));
        foreach ($timeSelectionCollection as $value) {
            $query .= $timeSelectionCollection->last() === $value ? $value . ' ' . $order . ' ' : $value . ' ' . $order . ', ';
        }

        // Get measurements
        $measurements = collect(DB::select($query, $bindings->toArray()));

        // Rename measurement keys
        foreach ($measurements as $measurementKey => $measurement) {

            // Convert Measurement object into array
            $measurement = collect($measurement);

            foreach ($measurement as $key => $value) {

                // Remove all unnecessary parts of the key
                $newKey = str_replace('YEAR(`created_at`', 'year', $key);
                $newKey = str_replace('MONTH(`created_at`', 'month', $newKey);
                $newKey = str_replace('WEEK(`created_at`', 'week', $newKey);
                $newKey = str_replace('DAY(`created_at`', 'day', $newKey);
                $newKey = str_replace('HOUR(`created_at`', 'hour', $newKey);
                $newKey = str_replace('AVG', '', $newKey);
                $newKey = str_replace('MIN', '', $newKey);
                $newKey = str_replace('MAX', '', $newKey);
                $newKey = str_replace('(', '', $newKey);
                $newKey = str_replace(')', '', $newKey);
                $newKey = str_replace('`', '', $newKey);

                // Set new key
                $measurement->put($newKey, $value);

                // Unset old key
                if ($key !== $newKey) {
                    $measurement->offsetUnset($key);
                }
            }

            // Update measurements
            $measurements[$measurementKey] = $measurement;
        }

        // Define database timezone, which should be utc
        $databaseTimeZone = new DateTimeZone('utc');

        foreach ($measurements as $measurement) {

            // Construct date time object from string
            if ($measurement->offsetExists('created_at')) {
                $createdAtDateTime = new DateTime($measurement['created_at'], $databaseTimeZone);
                $createdAtUTC = $createdAtDateTime->format($timeFormat);
            }
            else {
                $created_at = '';
                $created_at .= $measurement['year'] . '-';
                // if key is not defined use the average value
                $created_at .= $measurement->offsetExists('month') ? $measurement['month'] . '-' : '6-';
                $created_at .= $measurement->offsetExists('day') ? $measurement['day'] . ' ' : '15 ';
                $created_at .= $measurement->offsetExists('hour') ? $measurement['hour'] . ':00:00' : '12:00:00';
                $createdAtDateTime = new DateTime($created_at, $databaseTimeZone);
                $createdAtUTC = $createdAtDateTime->format($timeFormat);
            }

            // Get station object
            $station = Station::find($measurement['station_name']);

            // if PET is included, add the PET value
            if ($includePET) {

                // Get necessary values
                $airTemperature = $measurement['th_temp'];
                $solarRadiation = $measurement['sol_rad'];
                $humidity = $measurement['th_hum'];
                $windspeed = $measurement['wind_avgwind'];
                $latitude = $station->latitude;
                $longitude = $station->longitude;

                // Calculate and add the PET value
                $pet = $petservice->computePETFromMeasurement(  $createdAtUTC,
                                                                $airTemperature,
                                                                $solarRadiation,
                                                                $humidity,
                                                                $windspeed,
                                                                $latitude,
                                                                $longitude);
                $measurement->put('Physiologically Equivalent Temperature [°C]', $pet);
            }

            // Convert time from UTC to the timezone from the station
            $stationTimeZone = new DateTimeZone($station->timezone);
            $createdAtDateTime->setTimezone($stationTimeZone);

            // Add new time to measurement
            //If ungrouped replace the created_at value
            if ($measurement->offsetExists('created_at')) {
                $measurement['created_at'] = $createdAtDateTime->format($timeFormat);
            }

            // If grouped replace the time categories if they exist
            if ($measurement->offsetExists('year')) {
                $measurement['year'] = intval($createdAtDateTime->format('Y'));
            }
            if ($measurement->offsetExists('month')) {
                $measurement['month'] = intval($createdAtDateTime->format('m'));
            }
            if ($measurement->offsetExists('day')) {
                $measurement['day'] = intval($createdAtDateTime->format('d'));
            }
            if ($measurement->offsetExists('hour')) {
                $measurement['hour'] = intval($createdAtDateTime->format('H'));
            }
        }

        // Return output
        return response(json_encode([
            'measurements' => $measurements,
            'columns' => $columns,
            'grouped' => $grouping === null ? 'not' : $grouping,
            'aggregated' => $aggregation === null ? 'not' : $aggregation
        ]))->header('Content-type', 'application/json');
    }
    /**
     * Evaluates the input-collection and removes any invalid values.
     *
     * @param $input array
     * @return array filtered array where all invalid values are removed
     */
    public function evaluateInput(array $input) : array
    {
        return collect($input)->filter(function($value, $key) {

            return $key == 'station_name' || is_numeric($value);
        })->toArray();
    }


    /**
     * @param array $validated
     * @return bool
     */
    private function requestCameBeforeTimeout(array $validated): bool
    {
        $lastMeasurement = Measurement::getLastMeasurementByStationName($validated['station_name']);

        if (!$lastMeasurement) {
            return false; // allow always if station does not have any measurements at all
        }

        $requestTimeout = $lastMeasurement->created_at->addSeconds(env('REQUEST_TIMEOUT_IN_SECONDS'));
        if (Carbon::now() < $requestTimeout) {
            return true;
        }

        return false;
    }

}
