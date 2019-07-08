<?php


class PingTest extends TestCase
{

    public function testPingShouldReturnHelloWorld()
    {
        $this->get('/api/ping');

        $this->assertResponseOk();
    }

}