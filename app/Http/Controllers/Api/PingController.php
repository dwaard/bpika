<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Carbon\Carbon;

class PingController extends Controller
{
    public function handle()
    {
        $dt = Carbon::now()->format('Y-m-d H:i:s');
        return response(json_encode(['message' => 'You sent us a request at ' . $dt]))
            ->header('Content-type', 'application/json');
    }
}
