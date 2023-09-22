<?php

namespace App\Console\Commands;

use App\Models\Measurement;
use App\Models\Station;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SummarizeMeasurements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:summarize-measurements {station_code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Summarizes the measurements into 10 minute intervals';

    /**
     * Execute the console command.
     * @throws \Throwable
     */
    public function handle()
    {
        $station_name = $this->argument('station_code');
        $station = Station::where('code', '=', $station_name)->first();
        if (!$station) {
            exit('Station not found\n');
        }
        $this->info("Summarizing $station->name ($station->code)");

        $lower = $station->measurements()->first()->created_at;

        $last_committed = $lower;
        while ($lower <= now()) {
            // Check if we want to commit (first one is an empty commit)
            if (!$lower->isSameDay($last_committed)) {
                $this->info('    summarized: '.$lower->format('Y F d'));
                $last_committed = $lower;
            }
            // Fetch the next 10 min interval
            $upper = $lower->copy()->addMinutes(10);
            $data = $station->measurements()
                ->where('created_at', '>=', $lower)
                ->where('created_at', '<', $upper)
                ->get();
            // Summarize it if needed
            if ($data->count() > 1) {
                $this->summarize($data);
            }

            $lower = $upper;
        }
    }

    /**
     * Summarizes a specific set of attributes of the given collection. The
     * summarized data is stored in the last record of that collection while the
     * rest is deleted
     *
     * @param Collection $data
     * @return void
     */
    private function summarize(Collection $data)
    {
        $attrs = [
            'th_temp', 'th_hum', 'th_dew', 'th_heatindex', 'thb_temp', 'thb_hum', 'thb_dew',
            'thb_press', 'thb_seapress', 'wind_wind', 'wind_avgwind', 'wind_dir', 'wind_chill',
            'rain_rate', 'rain_total', 'uv_index', 'sol_rad', 'sol_evo', 'sun_total'
        ];
        $remainder = $data->last();
        foreach ($attrs as $attr) {
            $remainder->$attr = $this->summarizeAttribute($attr, $data);
        }
        $remainder->save();

        // Mark the records that must be deleted
        $others = $data->filter(function (Measurement $value, int $key) use ($remainder) {
            return $value->id != $remainder->id;
        });
        Measurement::whereIn('id', $others->pluck('id'))->delete();
    }

    /**
     * Summarize the data of the specified attribute. It will return either the average
     * of each valid value or `null`.
     *
     * @param string $attr
     * @param Collection $data
     * @return float|int|null
     */
    private function summarizeAttribute(string $attr, Collection $data): float|int|null
    {
        // Create an array where NULL values are omitted
        $val = [];
        foreach ($data as $m) {
            if ($m->$attr) {
                $val[] = $m->$attr;
            }
        }
        $cnt = count($val);
        // Compute the average and return it if $cnt > 0, `null` otherwise
        return $cnt > 0 ? array_sum($val) / $cnt : null;
    }

}
