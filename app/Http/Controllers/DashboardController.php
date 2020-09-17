<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $stations = [
            [
                'name' => 'HZ1',
                'title' => 'Vlissingen: Vredehof-Zuid',
                'color' => "#0000ff"
            ],
            [
                'name' => 'HZ4',
                'title' => 'Vlissingen: Oude Binnenstad',
                'color' => "#41BEAE"
            ],
            [
                'name' => 'HZ2',
                'title' => 'Middelburg: Binnenstad',
                'color' => "#ff6666"
            ],
            [
                'name' => 'HZ3',
                'title' => 'Middelburg: Magistraatwijk',
                'color' => "#ff0000"
            ],
            [
                'name' => 'HSR1',
                'title' => 'Rotterdam: Liskwartier',
                'color' => "#f4730b"
            ],
            [
                'name' => 'HSR2',
                'title' => 'Rotterdam: Bloemhof',
                'color' => "#f8ab6d"
            ],
            [
                'name' => 'VHL1',
                'title' => 'Leeuwarden: Stiens',
                'color' => "#00ff00"
            ],
            [
                'name' => 'VHL2',
                'title' => 'Leeuwarden: Cambuursterpad',
                'color' => "#7aff7a"
            ],
            [
                'name' => 'HHG1',
                'title' => 'Groningen: Paddepoel',
                'color' => "#973f73"
            ],
        ];
        return view('dashboard', [
            'stations' => $stations
        ]);
    }
}
