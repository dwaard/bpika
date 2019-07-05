<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PingController extends Controller
{
    public function handle()
    {
        $dt = Carbon::now()->format('Y-m-d H:i:s');
        return json_encode(['message' => 'You sent us a request at ' . $dt]);
    }
}
