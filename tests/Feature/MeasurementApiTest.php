<?php

namespace Tests\Feature;

use App\Models\Measurement;
use App\Models\Station;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Throwable;

class MeasurementApiTest extends TestCase
{
    use RefreshDatabase;

    protected Station $station;

    protected function setUp(): void
    {
        parent::setUp();
        // Set up dummy but enabled weather station
        $this->station = Station::factory()->create([
            'enabled' => true
        ]);
    }

    public function testAllParametersShouldSeeInDatabase()
    {
        // Arrange a measurement to be added
        $new = Measurement::factory()->make([
            'station_name' => $this->station->code
        ]);
        $data = $new->attributesToArray();

        // Act by trying to store the arranged measurement
        $response = $this->get('/api/store?' . http_build_query($data, '', '&'));

        $response->assertOk();
        $this->assertDatabaseHas('measurements', $data);
    }

    public function testInvalidParameterShouldNotSeeInDatabase()
    {
        // Arrange a measurement with invalid data
        $new = Measurement::factory()->make([
            'station_name' => $this->station->code
        ]);
        $data = $new->attributesToArray();
        $data['th_temp'] = '[tomskjfo sf]';

        // Act by trying to store the arranged measurement
        $response = $this->get('/api/store?' . http_build_query($data, '', '&'));

        $response->assertOk();
        // because of the invalid parameter, this value should be null
        $data['th_temp'] = null;
        $this->assertDatabaseHas('measurements', $data);
    }

    public function testInvalidNameShouldReturn()
    {
        // Arrange a non-existing station code
        $data = [
            'station_name' => 'AA' // Should be non-existing because the factory generates at least 3
        ];

        // Act by trying to store the arranged measurement
        $response = $this->get('/api/store?' . http_build_query($data, '', '&'));

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'station_name'
        ]);
    }

    public function testStoreWithOnlyStationNameShouldNotAddtoDatabase()
    {
        $str = "station_name=HZ3&th_temp=[th*temp-avg1]&th_hum=[th*hum-avg1]&th_dew=[th*dew-avg1]&th_heatindex=[th*heatindex-avg1]&thb_temp=[thb*temp-avg1]&thb_hum=[thb*hum-avg1]&thb_dew=[thb*dew-avg1]&thb_press=[thb*press-avg1]&thb_seapress=[thb*seapress-avg1]&wind_wind=[wind*wind-max1]&wind_avgwind=[wind*avgwind-avg1]&wind_dir=[wind*dir-avg1]&rain_rate=[rain*rate-avg1]&rain_total=[rain*total-sum1]&sol_rad=[sol*rad-avg1]";
        parse_str($str, $data);
        //dd($data);
        $data['station_name'] = $this->station->code;

        // Act by trying to store the arranged measurement
        $response = $this->get('/api/store?' . http_build_query($data, '', '&'));


        $response->assertStatus(412);
        $this->assertDatabaseMissing('measurements', [
            'station_name' => $this->station->code
        ]);
    }


    /**
     * @throws Throwable
     */
    public function testTimeoutShouldReturn412()
    {
        // Arrange the database with a station and a measurement
        $station = Station::factory()
            ->has(Measurement::factory())
            ->create();

        // Act by trying to store a new measurement within the timeout period
        $new = Measurement::factory()->make([
            'station_name' => $station->code
        ]);
        $response = $this->call('GET', '/api/store?' . http_build_query($new->attributesToArray(), '', '&'));

        $response->assertStatus(412);
        $response->assertJsonStructure([
            'error'
        ]);
    }

    public function testTimeoutAfterIntervalShouldAddToDatabase()
    {
        // Arrange the database with a station and a measurement at least one minute in the past
        $created_at = now()->subMinute();
        $station = Station::factory()
            ->has(Measurement::factory()->state([
                'created_at' => $created_at
            ]))
            ->create();

        // Act by trying to store a new measurement that will be after the timeout period
        $new = Measurement::factory()->make([
            'station_name' => $station->code
        ]);
        $response = $this->call('GET', '/api/store?' . http_build_query($new->attributesToArray(), '', '&'));

        $response->assertOk();
        $this->assertDatabaseHas('measurements', $new->attributesToArray());
    }
}
