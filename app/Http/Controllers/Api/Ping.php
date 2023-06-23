<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Ping extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $dt = Carbon::now()->format('Y-m-d H:i:s');
        return response(json_encode(['message' => 'You sent us a request at ' . $dt]))
            ->header('Content-type', 'application/json');
    }
}
