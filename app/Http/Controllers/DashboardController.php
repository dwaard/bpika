<?php

namespace App\Http\Controllers;

use App\Station;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stations = Station::all();
        return view('dashboard', [
            'stations' => $stations
        ]);
    }
}
