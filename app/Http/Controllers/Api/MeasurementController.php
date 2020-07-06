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

    public function getJSON($inputFormat = "Y-m-d", $startDate = NULL, $endDate = NULL, $station = NULL, $outputFormat = "Y-m-d", $columns = 'all', PETService $petservice)
    {
        // Define output
        $output = [];

        // Get and filter measurements based on times and station given
        $from = DateTime::createFromFormat($inputFormat, $startDate);
        $to = DateTime::createFromFormat($inputFormat, $endDate);
        $now = date($inputFormat);
        
        if (($from and $from->format($inputFormat) === $startDate) and ($to and $to->format($inputFormat) === $endDate)) {
            $betweenStart = $from;
            $betweenEnd = $to;
        }

        else if (($from and $from->format($inputFormat) === $startDate) and ($from < $now)) {
            $betweenStart = $from;
            $betweenEnd = $now;
        }

        else if (($to and $to->format($inputFormat) === $endDate) and ($to > $now)) {
            $betweenStart = $now;
            $betweenEnd = $to;
        }

        if ($station === NULL) {
            $measurements = Measurement::whereBetween("created_at", [$betweenStart, $betweenEnd])->get();
        }

        else {
            $measurements = Measurement::whereBetween("created_at", [$from, $betweenEnd])->where("station_name", "=", $station)->get();
        }

        // The database timezone should be in the UTC timezone
        $databaseTimezone = new DateTimeZone('utc');

        // Iterate through each measurement
            foreach ($measurements as $measurement) {

                // Define output for this measurement
                $outputMeasurement = [];

                // Calculate PET value
                // TODO get longitude and latitude from station
                if ($measurement['created_at'] !== null or
                    $measurement['th_temp'] !== null or
                    $measurement['sol_rad'] !== null or
                    $measurement['th_hum'] !== null or
                    $measurement['wind_avgwind'] !== null) {
                    $measurement['PET'] = $petservice->computePETFromMeasurement(   $measurement['created_at'],
                                                                                    $measurement['th_temp'],
                                                                                    $measurement['sol_rad'],
                                                                                    $measurement['th_hum'],
                                                                                    $measurement['wind_avgwind'],
                                                                                    52.,
                                                                                    5.1);
                }
                else {
                    $measurement['PET'] = null;
                }

                // Turn datetime string into object
                $datetime = new DateTime($measurement['created_at'], $databaseTimezone);

                // Apply timezone from station
                // TODO get timezone from station
                $targetTimeZone = new DateTimeZone('Europe/Amsterdam');
                $datetime->setTimeZone($targetTimeZone);
                $outputMeasurement['created_at'] = $datetime->format($outputFormat);

                // add columns to output
                $selectedColumns = explode(',', $columns);
                if (in_array('all', $selectedColumns)) {
                    foreach ($measurement as $key => $value) {
                        $outputMeasurement[$key] = $value;
                    }
                }
                array_push($output, $outputMeasurement);
            }
        
        return response(json_encode(["data" => $output]))
            ->header('Content-type', 'application/json');    
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
