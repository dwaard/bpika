<?php

use Illuminate\Database\Seeder;

class StationsTableSeeder extends Seeder
{

    /**
     * @var array List of the station names that will be seeded
     */
    protected $names = [
        'Middelburg.Zuid',
        'Vlissingen.Spuistraat'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->names as $name) {
            \App\Station::create(['name' => $name]);
        }
    }
}
