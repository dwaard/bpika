<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Station;

class ApiStoreTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Set up dummy weather station
        Station::create([
            'name' => "Bonaire.jibe_city"
        ]);
    }


    public function testAllParametersShouldSeeInDatabase()
    {
        $data = [
            'station_name' => 'Bonaire.jibe_city',
            'th_temp' => 23.4,
            'th_hum' => 65.0,
            'th_dew' => 12.3,
            'th_heatindex' => 21.9,
            'thb_temp' => 22.1,
            'thb_hum' => 24.2,
            'thb_dew' => 13.7,
            'thb_press' => 998,
            'thb_seapress' => 1002,
            'wind_wind' => 6.4,
            'wind_avgwind' => 4.4,
            'wind_dir' => 127,
            'wind_chill' => 15.4,
            'rain_rate' => 0.1,
            'rain_total' => 76.3,
            'uv_index' => 11.1,
            'sol_rad' => 22.2,
            'sol_evo' => 2.2,
            'sun_total' => 4.4,
        ];

        $uri = '/api/store?' . http_build_query($data, '', '&');

        $this->get($uri);

        $this->assertResponseOk();

        $this->seeInDatabase('measurements', $data);
    }

    public function testInvalidNameShouldReturn()
    {
        $this->get('/api/store');

        $this->assertResponseStatus(422);

    }

    public function testSimpleStoreShouldAddtoDatabase()
    {
        $this->get('/api/store?station_name=Bonaire.jibe_city');

        $this->assertResponseOk();

        $this->seeInDatabase('measurements', [
            'station_name' => 'Middelburg.Zuid'
        ]);
    }

    public function testTimeoutShouldReturn412()
    {
        $this->get('/api/store?station_name=Bonaire.jibe_city');

        $this->assertResponseOk();

        $response = $this->get('/api/store?station_name=Bonaire.jibe_city');

        $response->assertResponseStatus(412);
        $response->seeJson(['error' => "Already added measurement less than 55 seconds ago."]);


    }

}