<?php

namespace App\Http\Controllers;

use App\Station;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GrafiekController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $stations = Station::where('enabled', 1)->get();

        return view('grafiek', [
            'stations' => $stations
        ]);
    }
}
