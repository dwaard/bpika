<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;

class PingTest extends TestCase
{

    public function testPingShouldReturnHelloWorld()
    {
        // Make the request then record the time,
        // so time to process the request doesn't affect the test
        $response = $this->get('/api/ping');
        $now = Carbon::now()->format('Y-m-d H:i:s');

        $response->assertJson([
            'message' => 'You sent us a request at ' . $now
        ]);
    }

}
