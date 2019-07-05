<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Measurement;
use Illuminate\Http\Request;

class MeasurementController extends Controller
{
    public function store(Request $request)
    {
        $validated = $this->validate($request, Measurement::rules());

        $measurement = Measurement::create($validated);

        return json_encode(['message' => 'Measurement created with id ' . $measurement->id]);
    }
}
