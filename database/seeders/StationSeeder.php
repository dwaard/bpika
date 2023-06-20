<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Station;

class StationSeeder extends Seeder
{

    /**
     * @var array List of the station names that will be seeded
     */
    protected $stations = [
        [
            'code' => 'HZ1',
            'city' => 'Vlissingen',
            'name' => 'Vredehof-Zuid',
            'chart_color' => '#0000ff',
            'latitude' => 51.4535,
            'longitude' => 3.5583,
            'enabled' => true,
            'timezone' => 'Europe/Amsterdam'
        ],
        [
            'code' => 'HZ2',
            'city' => 'Middelburg',
            'name' => 'Binnenstad',
            'chart_color' => '#ff6666',
            'latitude' => 51.497,
            'longitude' => 3.6142,
            'enabled' => true,
            'timezone' => 'Europe/Amsterdam'
        ],
        [
            'code' => 'HZ3',
            'city' => 'Middelburg',
            'name' => 'Magistraatwijk',
            'chart_color' => 'ff0000',
            'latitude' => 51.4853,
            'longitude' => 3.6166,
            'enabled' => true,
            'timezone' => 'Europe/Amsterdam'
        ],
        [
            'code' => 'HZ4',
            'city' => 'Vlissingen',
            'name' => 'Oude Binnenstad',
            'chart_color' => '#41BEAE',
            'latitude' =>  51.4426,
            'longitude' => 3.5727,
            'enabled' => true,
            'timezone' => 'Europe/Amsterdam'
        ],
        [
            'code' => 'HSR1',
            'city' => 'Rotterdam',
            'name' => 'Liskwartier',
            'chart_color' => '#f4730b',
            'latitude' => 51.9371,
            'longitude' => 4.481,
            'enabled' => true,
            'timezone' => 'Europe/Amsterdam'
        ],
        [
            'code' => 'HSR2',
            'city' => 'Rotterdam',
            'name' => 'Bloemhof',
            'chart_color' => '#f8ab6d',
            'latitude' => 51.901115,
            'longitude' => 4.5009905,
            'enabled' => true,
            'timezone' => 'Europe/Amsterdam'
        ],
        [
            'code' => 'VHL1',
            'city' => 'Leeuwaarden',
            'name' => 'Stiens',
            'chart_color' => '#00ff00',
            'latitude' => 53.2575,
            'longitude' => 5.7666,
            'enabled' => true,
            'timezone' => 'Europe/Amsterdam'
        ],
        [
            'code' => 'VHL2',
            'city' => 'Leeuwaarden',
            'name' => 'Cambuursterpad',
            'chart_color' => '#7aff7a',
            'latitude' => 53.206947,
            'longitude' => 5.811691,
            'enabled' => true,
            'timezone' => 'Europe/Amsterdam'
        ],
        [
            'code' => 'HHG1',
            'city' => 'Groningen',
            'name' => 'Paddepoel',
            'chart_color' => '#973f73',
            'latitude' => 53.23,
            'longitude' => 6.539833,
            'enabled' => true,
            'timezone' => 'Europe/Amsterdam'
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->stations as $station) {
            Station::create([
                'code' => $station['code'],
                'city' => $station['city'],
                'name' => $station['name'],
                'chart_color' => $station['chart_color'],
                'latitude' => $station['latitude'],
                'longitude' => $station['longitude'],
                'timezone' => $station['timezone'],
                'enabled' => $station['enabled']
            ]);
        }
    }
}
