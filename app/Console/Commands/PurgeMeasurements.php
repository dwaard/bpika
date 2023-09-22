<?php

namespace App\Console\Commands;

use App\Models\Measurement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PurgeMeasurements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:purge-measurements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes all the obsolete records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            Measurement::where('sun_total', '=', -255)->delete();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
