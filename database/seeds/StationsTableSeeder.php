<?php

use Illuminate\Database\Seeder;

class StationsTableSeeder extends Seeder
{

    /**
     * @var array List of the station names that will be seeded
     */
    protected $names = [
        'HZ1', 'HZ2', 'HZ3', 'HZ4',
        'HSR1', 'HSR2',
        'VHL1', 'VHL2',
        'HHG1', 'HHG2'
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
