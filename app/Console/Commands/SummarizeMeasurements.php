<?php

namespace App\Console\Commands;

use App\Models\Measurement;
use App\Models\Station;
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
    protected $signature = 'app:summarize-measurements';

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
        $this->info("Starting");
        foreach (Station::all() as $station) {
            $this->info("Summarizing $station->name");
            $data = collect();
            foreach ($station->measurements as $m) {
                if ($data->count() > 0 && $data[0]->created_at->diffInMinutes($m->created_at) > 10) {
                    $this->summarize($data);
                    $data = collect();
                }
                $data->push($m);
            }
            if ($data->count() > 0) {
                $this->summarize($data);
            }
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
    private function summarize(Collection $data): void
    {
        $attrs = [
            'th_temp', 'th_hum', 'th_dew', 'th_heatindex', 'thb_temp', 'thb_hum', 'thb_dew',
            'thb_press', 'thb_seapress', 'wind_wind', 'wind_avgwind', 'wind_dir', 'wind_chill',
            'rain_rate', 'rain_total', 'uv_index', 'sol_rad', 'sol_evo', 'sun_total'
        ];
        try {
            DB::beginTransaction();
            $remainder = $data->last();
            foreach ($attrs as $attr) {
                $remainder->$attr = $this->summarizeAttribute($attr, $data);
            }
            $remainder->save();
            // Delete the other records
            $others = $data->filter(function (Measurement $value, int $key) use ($remainder) {
                return $value->id != $remainder->id;
            });
            $others->each(function (Measurement $value) {
                $value->delete();
            });
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
        }
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
