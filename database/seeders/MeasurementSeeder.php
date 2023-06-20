<?php

namespace Database\Seeders;

use App\Models\Measurement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeasurementSeeder extends Seeder
{
    use CsvReadable;

    /**
     * Construct a new ResultSeeder
     */
    public function __construct()
    {
        $this->path = "seed_files/measurements.csv";
        $this->delimiter = ",";
        $this->header_row = 0;
        $this->start_row = 1;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();
            $this->readCsvData(function ($data) {
                unset($data['id']);
//                unset($data['created_at']);
//                unset($data['updated_at']);
                foreach($data as $key => $value) {
                    if ($value === 'NULL') {
                        unset($data[$key]);
                    }
                }

                Measurement::create($data);
            });
            DB::commit();
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
        }
    }
}
