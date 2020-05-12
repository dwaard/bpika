<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;

class PingTest extends TestCase
{

    public function testPingShouldReturnHelloWorld()
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $response = $this->get('/api/ping');


        $response->assertJson([
            'message' => 'You sent us a request at ' . $now
        ]);
    }

}
