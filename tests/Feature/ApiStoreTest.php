<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Station;

class ApiStoreTest extends TestCase
{
    use RefreshDatabase;

    protected $station;

    protected function setUp(): void
    {
        parent::setUp();
        // Set up dummy weather station
        $this->station = factory(Station::class)->create([
            'code' => 'BO1',
            'city' => 'Bonaire',
            'name' => 'Jibe city',
            'chart_color' => '#00FF00',
            'latitude' => 45,
            'longitude' => 75,
            'timezone' => 'Europe/Amsterdam',
            'enabled' => true
        ]);
    }


    public function testAllParametersShouldSeeInDatabase()
    {
        $data = [
            'station_name' => 'Jibe city',
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

        $response = $this->get($uri);

        $response->assertOk();

        $this->assertDatabaseHas('measurements', $data);
    }


    public function testInvalidParameterShouldNotSeeInDatabase()
    {
        $data = [
            'station_name' => 'Jibe city',
            'th_temp' => '[tomskjfo sf]',
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

        $response = $this->get($uri);

        $response->assertOk();

        // because of the invalid parameter, this value should be null
        $data['th_temp'] = null;

        $this->assertDatabaseHas('measurements', $data);

    }


    public function testInvalidNameShouldReturn()
    {
        $response = $this->call('GET', '/api/store');

        $this->assertEquals(422, $response->status());

    }


    public function testSimpleStoreShouldAddtoDatabase()
    {
        $response = $this->get('/api/store?station_name=Jibe city');

        $response->assertOk();

        $this->assertDatabaseHas('measurements', [
            'station_name' => 'Jibe city'
        ]);
    }


    public function testTimeoutShouldReturn412()
    {
        $response = $this->call('GET', '/api/store?station_name=Jibe city');

        $response->assertOk();

        $response = $this->call('GET', '/api/store?station_name=Jibe city');

        $this->assertEquals(412, $response->status());
        $response->assertJson([
            'error' => "Already added measurement less than 55 seconds ago."
        ]);
    }

}
