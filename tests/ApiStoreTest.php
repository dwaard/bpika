<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ApiStoreTest extends TestCase
{
    use DatabaseTransactions;

    public function testAllParametersShouldSeeInDatabase()
    {
        $this->get('/api/store?station_name=Middelburg.Zuid&th_temp=23.4&th_hum=65&th_dew=12.3');

        $this->assertResponseOk();

        $this->seeInDatabase('measurements', [
            'station_name' => 'Middelburg.Zuid',
            'th_temp' => 23.4,
            'th_hum' => 65.0,
            'th_dew' => 12.3
        ]);
    }

    public function testInvalidNameShouldReturn()
    {
        $this->get('/api/store');

        $this->assertResponseStatus(422);

    }

    public function testSimpleStoreShouldAddtoDatabase()
    {
        $this->get('/api/store?station_name=Middelburg.Zuid');

        $this->assertResponseOk();

        $this->seeInDatabase('measurements', [
            'station_name' => 'Middelburg.Zuid'
        ]);
    }

    public function testTimeoutShouldReturn412()
    {
        $this->get('/api/store?station_name=Middelburg.Zuid');

        $this->assertResponseOk();

        $response = $this->get('/api/store?station_name=Middelburg.Zuid');

        $response->assertResponseStatus(412);
        $response->seeJson(['error' => "Already added measurement less than 1 minutes ago."]);


    }

}