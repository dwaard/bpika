<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreMeasurementRequest;
use App\Models\Measurement;
use App\Services\PETService;
use App\Models\Station;
use DateTimeInterface;
use Illuminate\Routing\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class MeasurementController extends Controller
{
    /**
     * Checks request for validity and stores the input data if it is.
     *
     * Returns a JSON message with outcome of validation.
     *
     * @param StoreMeasurementRequest $request
     * @return false|Response|string
     */
    public function store(StoreMeasurementRequest $request): Response|bool|string
    {
        $input = $request->validated();

        $station = Station::findOrFail($input['station_name']);

        $lockout = $station->checkLockOut();

        if ($lockout) {
            $msg = "Station $station->code is still locked out for $lockout seconds.";
            return new Response([
                'error' => $msg
            ], ResponseAlias::HTTP_PRECONDITION_FAILED);
        }

        $evaluated = collect($input)->filter(function ($value, $key) {
                return $key == 'station_name' || is_numeric($value);
        })->toArray();

        $measurement = Measurement::create($evaluated);

        return json_encode(['measurement.created' => 'Measurement created with id ' . $measurement->id]);
    }

    /**
     * Evaluates the input-collection and removes any invalid values.
     *
     * @param $input array
     * @return array filtered array where all invalid values are removed
     */
    public function evaluateInput(array $input) : array
    {
        return collect($input)->filter(function ($value, $key) {

            return $key == 'station_name' || is_numeric($value);
        })->toArray();
    }


    /**
     * Returns a chart time data structure.
     *
     * @param Station $station
     * @param PETService $petservice
     * @param Request $request
     * @return array
     */
    #[ArrayShape(['label' => "mixed", 'column' => "mixed|string", 'chart_color' => "string", 'data' => "mixed"])]
    public function getChartTimeSeries(Station $station, PETService $petservice, Request $request): array
    {
        // Build a query based on the request data, start with the measurements of the station
        $query = $station->measurements();

        // Add a filter for startDate
        if ($request->has('startDate')) {
            $timeFormat = DateTimeInterface::RFC3339_EXTENDED;
            $start = Carbon::createFromFormat($timeFormat, $request->startDate);
            $query->whereDate('created_at', '>=', $start);
        }

        // Add a filter for endDate
        if ($request->has('endDate')) {
            $timeFormat = DateTimeInterface::RFC3339_EXTENDED;
            $end = Carbon::createFromFormat($timeFormat, $request->endDate);
            $query->whereDate('created_at', '<=', $end);
        }

        // Set the proper SELECT clause, aggregation functions and GROUP BY
        // Using `x` and `y` aliases, so it fits a proper ChartJS data structure
        $column = strtolower($request->has('column') ? $request->column : 'pet');

        $grouping = $request->has('grouping') ? $request->grouping : null;
        if ($grouping) {
            $aggr = $request->has('aggregation') ? $request->aggregation : 'AVG';
            if ($column !== 'pet') {
                $variable_select = "$aggr($column) AS y";
            } else {
                // If PET, we need these columns to compute the PET value from
                $variable_select = "$aggr(th_temp) AS th_temp, $aggr(sol_rad) AS sol_rad, $aggr(th_hum) AS th_hum, ".
                    "$aggr(wind_avgwind) AS wind_avgwind";
            }
            // TODO support for other groupings than just hourly. Just change the date formatting accordingly
            $query->selectRaw("$variable_select, DATE_FORMAT(created_at, '%c/%d/%Y %H:00:00') AS x");
            $query->groupBy('x');
        } else {
            $query->selectRaw("$column AS y, DATE_FORMAT(created_at, '%c/%d/%Y %H:%i:%s') AS x");
        }

        // Set the ORDER BY
        $query->orderBy('x', $request->has('order') ? $request->order : 'asc');

        // Fetch the data
        $output = $query->get();
        // Map the data, if it's PET they want
        if ($column === 'pet') {
            $output = $output->map(fn($item) => [
                'x' => $item->x,
                'y' => $petservice->computePETFromMeasurement(
                    $item->x,
                    $item->th_temp,
                    $item->sol_rad,
                    $item->th_hum,
                    $item->wind_avgwind,
                    $station->latitude,
                    $station->longitude
                )
            ]);
        }
        // Return a structure fit for ChartJS
        return [
        'label' => $station->label,
        'column' => $column,
        'chart_color' => $station->chart_color,
        'data' => $output
        ];
    }
}
