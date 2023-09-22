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
            exit('Station not found');
        }
        $this->info("Summarizing $station->name ($station->code)");
        $data = collect();
        $last_committed = null;
        foreach ($station->measurements()->cursor() as $m) {
            if ($m->sun_total != null) {
                // Skip it
            } else {
                // Check if we want to commit (first one is an empty commit)
                if (!$last_committed || !$m->created_at->isSameDay($last_committed)) {
                    $this->info('    summarized: '.$m->created_at->format('Y F d'));
                    $last_committed = $m->created_at;
                }
                // Create groups and summarize, if needed
                if ($data->count() == 0) {
                    $data->push($m);
                } else {
                    if ($data[0]->created_at->diffInSeconds($m->created_at) >= 600) {
                        if ($data->count() > 1) {
                            $this->summarize($data);
                        }
                        $data = collect([$m]);
                    } else {
                        $data->push($m);
                    }
                }
            }
            // End loop, move to next measurement
        }

        // If there's any remaining data left
        if ($data->count() > 1) {
            $this->summarize($data);
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
        Measurement::whereIn('id', $others->pluck('id'))->update([
            'sun_total' => -255
        ]);
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
