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

    public function testStoreWithOnlyStationNameShouldAddtoDatabase()
    {
        // Act by trying to store a measurement with only the station name
        $response = $this->get('/api/store?station_name='.$this->station->code);

        $response->assertOk();
        $this->assertDatabaseHas('measurements', [
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
