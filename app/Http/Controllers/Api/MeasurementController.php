<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Measurement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MeasurementController extends Controller
{
    /**
     * Checks request for validity and stores the input data if it is.
     *
     * Returns a JSON message with outcome of validation.
     *
     * @param Request $request
     * @return false|Response|string
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validated = $request->validate(Measurement::rules());

        if ($this->requestCameBeforeTimeout($validated)) {

            $msg = 'Already added measurement less than '.env('REQUEST_TIMEOUT_IN_SECONDS').' seconds ago.';

            return new Response(['error' => $msg],Response::HTTP_PRECONDITION_FAILED);

        }

        $evaluated = $this->evaluateInput($validated);

        $measurement = new Measurement($evaluated);

        return json_encode(['measurement.created' => 'Measurement created with id ' . $measurement->getId()]);

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
