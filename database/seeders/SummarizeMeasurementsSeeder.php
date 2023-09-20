<?php

namespace Database\Seeders;

use App\Models\Measurement;
use App\Models\Station;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SummarizeMeasurementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Station::all() as $station) {
            DB::beginTransaction();
            $this->command->info("Summarizing $station->name");
            $last = null;
            $others = [];
            foreach ($station->measurements as $m) {

                if ($last) {
                    $others[] = $last;
                }
                $last = $m;
                if (!$last) {
                    $this->command->info("Starting at: $m->created_at");
                } else if ($m->created_at->diffInMinutes($last->created_at) < 10) {
                    $others[] = $m;
                } else {
                    if (count($others)>1) {
                        $this->summarize($last, $others);
                        $last->save();
                        foreach ($others as $item) {
                            $item->delete();
                        }
                    }
                    $last = $m;
                    $others = [];
                    $this->command->info("Next interval: $m->created_at");
                }
            }
            DB::commit();
        }
    }

    private function summarize(Measurement $first, array $others)
    {
        $attrs = [
            'th_temp', 'th_hum', 'th_dew', 'th_heatindex', 'thb_temp', 'thb_hum', 'thb_dew',
            'thb_press', 'thb_seapress', 'wind_wind', 'wind_avgwind', 'wind_dir', 'wind_chill',
            'rain_rate', 'rain_total', 'uv_index', 'sol_rad', 'sol_evo', 'sun_total'
        ];
        foreach ($attrs as $attr) {
            $first->$attr = $this->summarizeAttribute($attr, $first, $others);
        }
    }

    private function summarizeAttribute(string $attr, Measurement $first, array $others)
    {
        $val = [$first->$attr];
        $count = 1;
        foreach ($others as $m) {
            $val[] = $m->$attr;
        }
        $val = array_filter($val, fn($i) => $i!==null);
        $cnt = count($val);
//        $this->command->info($attr."($cnt) => ".implode(',', $val));
        return $cnt>0 ? array_sum($val) / $cnt : null;
    }
}
