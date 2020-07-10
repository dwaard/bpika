<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreMeasurementRequest;
use App\Services\PETService;
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

            $msg = 'Already added measurement less than '.env('REQUEST_TIMEOUT_IN_SECONDS').' seconds ago.';

            return new Response(['error' => $msg],Response::HTTP_PRECONDITION_FAILED);

        }

        $evaluated = $this->evaluateInput($validated);

        $measurement = Measurement::create($evaluated);

        return json_encode(['measurement.created' => 'Measurement created with id ' . $measurement->id]);

    }

    public function getJSON(PETService $petservice,
                            $startDate = null,
                            $endDate = null,
                            $stations = 'all',
                            $grouping = null,
                            $aggregation = 'avg',
                            $columns = 'all',
                            $order = 'desc') {

        // Define standard variables

        // Year-Month-Day Hour(24 h format)-Minute-Second
        $timeFormat = 'Y-m-d H:i:s';

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
        // TODO get stations from database
        $stationsWhitelist = collect([
            'HZ1',
            'HZ2',
            'HZ3',
            'HZ4',
            'VHL1',
            'VHL2',
            'HSR1',
            'HSR2',
            'HHG1'
        ]);

        // Get and filter measurements based on times and stations given
        $from = DateTime::createFromFormat($timeFormat, $startDate);
        $to = DateTime::createFromFormat($timeFormat, $endDate);
        $now = new DateTime(date($timeFormat));

        // If start and end date are given, search in between them
        if (($from and $from->format($timeFormat) === $startDate) and ($to and $to->format($timeFormat) === $endDate)) {
            $betweenStart = $from;
            $betweenEnd = $to;
        }
        // If only start date is given, search from then until now
        else if (($from and $from->format($timeFormat) === $startDate) and ($from < $now)) {
            $betweenStart = $from;
            $betweenEnd = $now;
        }
        // If only end date is given, search from now until then
        else if (($to and $to->format($timeFormat) === $endDate) and ($to > $now)) {
            $betweenStart = $now;
            $betweenEnd = $to;
        }
        // If none are given, don't filter on date
        else {
            $betweenStart = null;
            $betweenEnd = null;
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
        $query = 'SELECT `station_name`, ' . $timeSelection . ', ';

        // Add columns to query
        $columns = collect(explode(',', $columns));
        // If PET is included then set the flag and remove the value from the collection
        if (in_array('PET', $columns->toArray())) {
            $includePET = true;
            $columns->reject(function ($value, $key) {
                return $value === 'PET';
            });
        }
        // If all is included then set columns to all of the allowed columns
        if (in_array('all', $columns->toArray())) {
            $columns = collect($columnsWhitelist);
            $includePET = true;
        }
        // Remove any disallowed columns
        $columns = $columns->intersect($columnsWhitelist);
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

            // if PET is included, add the PET value
            if ($includePET) {

                // Get necessary values
                $airTemperature = $measurement['th_temp'];
                $solarRadiation = $measurement['sol_rad'];
                $humidity = $measurement['th_hum'];
                $windspeed = $measurement['wind_avgwind'];
                // TODO get coordinates from station
                $latitude = 52.;
                $longitude = 5.1;

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
            // TODO get timezone from station
            $stationTimeZone = new DateTimeZone('Europe/Amsterdam');
            $createdAtDateTime->setTimezone($stationTimeZone);

            // Add new time to measurement
            if ($measurement->offsetExists('created_at')) {
                $measurement['created_at'] = $createdAtDateTime->format($timeFormat);
            }
            if ($measurement->offsetExists('hour')) {
                $measurement['hour'] = intval($createdAtDateTime->format('H'));
                $measurement['day'] = intval($createdAtDateTime->format('d'));
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

    public function dashboard() {
        return view('dashboard');
    }
}
