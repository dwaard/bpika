<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PingController extends Controller
{
    public function handle(Request $request)
    {
        return json_encode(['message' => 'Hello world!']);
    }
}
