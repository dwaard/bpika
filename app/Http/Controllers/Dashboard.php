<?php

namespace App\Http\Controllers;

use App\Models\Station;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return view('dashboard', [
            'stations' => Station::active()->get()
        ]);
    }
}
