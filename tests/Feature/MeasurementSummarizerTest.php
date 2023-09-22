<?php

namespace Tests\Feature;

use App\Models\Measurement;
use App\Models\Station;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MeasurementSummarizerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The happy path.
     */
    public function test_it_will_summarize_all_values(): void
    {
        $station = Station::factory()->create();
        // Arrange 10 Measurements, all of them with a th_temp=null
        $measurements = Measurement::factory(10)
            ->state(new Sequence(fn ($sequence) => [
                'id' => $sequence->index + 1,
                'station_name' => $station->code,
                'th_temp' => $sequence->index + 1]))
            ->create();

//        dd($measurements);

        // Act by calling the summarizer service
        Artisan::call("app:summarize-measurements $station->code");
//        Artisan::call("app:purge-measurements");

//        dd(Measurement::all()->pluck(['id', 'sun_total']));
        // Assert that the database contains the first element
        $remainder = $measurements->last();
        $others = $measurements->filter(function (Measurement $value, int $key) use ($remainder) {
            return $value->id != $remainder->id;
        });
        $this->assertDatabaseHas('measurements', [
            'id' => $remainder->id]);
        $others->each(fn($m) =>
            $this->assertDatabaseMissing('measurements', ['id' => $m->id])
        );
        // Assert that the first th_temp = null
        $first = Measurement::find($remainder->id);
        $this->assertEquals(5.5, $first->th_temp);
    }

    /**
     * When all values are `null`.
     */
    public function test_it_will_summarize_all_null_values_as_null(): void
    {
        $station = Station::factory()->create();
        // Arrange 10 Measurements, all of them with a th_temp=null
        $measurements = Measurement::factory(10)->create([
            'station_name' => $station->code,
            'th_temp' => null
        ]);

        // Act by calling the summarizer service
        Artisan::call("app:summarize-measurements $station->code");
//        Artisan::call("app:purge-measurements");

        // Assert that the database contains the first element
        $remainder = $measurements->last();
        $others = $measurements->filter(function (Measurement $value, int $key) use ($remainder) {
            return $value->id != $remainder->id;
        });
        $this->assertDatabaseHas('measurements', [
            'id' => $remainder->id]);
        $others->each(fn($m) =>
        $this->assertDatabaseMissing('measurements', ['id' => $m->id])
        );
        // Assert that the first th_temp = null
        $first = Measurement::find($remainder->id);
        $this->assertNull($first->th_temp);
    }

    /**
     * When one value is `null`.
     */
    public function test_it_will_summarize_range_with_one_null_value_properly(): void
    {
        // Arrange 11 Measurements, 10 of them with a th_temp=1..10; the other is NULL
        $station = Station::factory()->create();
        // Arrange 10 Measurements, all of them with a th_temp=null
        $measurements = Measurement::factory(11)
            ->state(new Sequence(fn ($sequence) => [
                'id' => $sequence->index + 1,
                'station_name' => $station->code,
                'th_temp' => $sequence->index + 1]))
            ->create();
        $measurements->last()->update(['th_temp' => null]);

        // Act by calling the summarizer service
        Artisan::call("app:summarize-measurements $station->code");
//        Artisan::call("app:purge-measurements");

        // Assert that the database contains the first element
        $remainder = $measurements->last();
        $others = $measurements->filter(function (Measurement $value, int $key) use ($remainder) {
            return $value->id != $remainder->id;
        });
        $this->assertDatabaseHas('measurements', [
            'id' => $remainder->id]);
        $others->each(fn($m) =>
        $this->assertDatabaseMissing('measurements', ['id' => $m->id])
        );
        // Assert that the first th_temp = null
        $first = Measurement::find($remainder->id);
        $this->assertEquals(5.5, $first->th_temp);
    }


    /**
     * Create a 2nd entry when it is created after >10min.
     */
    public function test_it_will_summarize_into_groups_of_10_minutes(): void
    {
        // Arrange 11 Measurements, 10 of them within a 10 min. interval with a th_temp range of 1 - 10;
        // The other is later and has a th_temp of -100
        $station = Station::factory()->create();
        // Arrange 10 Measurements, all of them with a th_temp=null
        $measurements = Measurement::factory(11)
            ->state(new Sequence(fn ($sequence) => [
                'id' => $sequence->index + 1,
                'station_name' => $station->code,
                'th_temp' => $sequence->index + 1]))
            ->create();
        $last = $measurements->last();
        $new = $last->created_at->addMinutes(15);
        $last->th_temp = -100;
        $last->created_at = $new;
        $last->updated_at = $new;
        $last->save(['timestamps' => false]);

        // Act by calling the summarizer service
        Artisan::call("app:summarize-measurements $station->code");
//        Artisan::call("app:purge-measurements");

        // Assert that the database contains the first element
        $this->assertDatabaseHas('measurements', [
            'id' => 10]);
        $this->assertDatabaseHas('measurements', [
            'id' => 11]);
        // Assert that the first th_temp = null
        $first = Measurement::find(10);
        $this->assertEquals(5.5, $first->th_temp);
        $second = Measurement::find(11);
        $this->assertEquals(-100, $second->th_temp);
    }

    public function test_it_will_summarize_100_1_minute_interval_measurements_into_10_groups()
    {
        // Arrange 90 measurements; set created_at exactly 1 apart from each other
        $station = Station::factory()->create();
        // Arrange 10 Measurements, all of them with a th_temp=null
        $now = Carbon::now()->subMinutes(100);
        $measurements = Measurement::factory(100)
            ->state(new Sequence(fn ($sequence) => [
                'station_name' => $station->code,
                'created_at' => $now->clone()->addMinutes($sequence->index),
                'updated_at' => $now->clone()->addMinutes($sequence->index)
            ]))->create();
        // Act by calling the summarizer service
        Artisan::call("app:summarize-measurements $station->code");
//        Artisan::call("app:purge-measurements");

        // Assert that the database has 10 entries
        $this->assertDatabaseCount('measurements', 10);
    }

}
