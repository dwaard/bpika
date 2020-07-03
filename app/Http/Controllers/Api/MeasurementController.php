<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreMeasurementRequest;
use Illuminate\Routing\Controller;
use App\Measurement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DateTime;
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

    public function getJSON($startDate = NULL, $endDate = NULL, $format = "Y-m-d", $station = NULL)
    {
        $from = DateTime::createFromFormat($format, $startDate);
        $to = DateTime::createFromFormat($format, $endDate);
        $now = date($format);
        if (($from and $from->format($format) === $startDate) and ($to and $to->format($format) === $endDate)) {
            if ($station === NULL) { 
                $measurements = Measurement::whereBetween("created_at", [$from, $to])->get();
            }
            else {
                $measurements = Measurement::whereBetween("created_at", [$from, $to])->where("station_name", "=", $station)->get();
            }
        }
        // TODO check if $startDate is earlier than today or $endDate is later than today.
        else if (($from and $from->format($format) === $startDate) and ($from < $now)) {
            if ($station === NULL) {
                $measurements = Measurement::whereBetween("created_at", [$from, $now])->get();
            }
            else {
                $measurements = Measurement::whereBetween("created_at", [$from, $now])->where("station_name", "=", $station)->get();
            }
        }
        else if (($to and $to->format($format) === $endDate) and ($to > $now)) {
            if ($station === NULL) {
                $measurements = Measurement::whereBetween("created_at", [$now, $to])->get();
            }
            else {
                $measurements = Measurement::whereBetween("created_at", [$from, $to])->where("station_name", "=", $station)->get();
            }
        }
        else {
            if ($station === NULL) {
                $measurements = Measurement::all();
            }
            else {
                $measurements = Measurement::where("station_name", "=", $station)->get();
            }
        }
        return response(json_encode(["data" => $measurements]))
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

    public function datavis() {
        return view('datavis');
    }
}
