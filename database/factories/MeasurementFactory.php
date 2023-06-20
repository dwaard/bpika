<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Measurement>
 */
class MeasurementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'th_temp' => $this->faker->numberBetween(-5., 30.), // 23.4,
            'th_hum' => $this->faker->numberBetween( 15, 99), // 65.0,
            'th_dew' => $this->faker->numberBetween(0, 15), // 12.3,
            'th_heatindex' => $this->faker->numberBetween(-5., 30.), //21.9,
            'thb_temp' => $this->faker->numberBetween(-5., 30.), //22.1,
            'thb_hum' => $this->faker->numberBetween(-5., 30.), //24.2,
            'thb_dew' => $this->faker->numberBetween(-5., 30.), //13.7,
            'thb_press' => $this->faker->numberBetween(950, 1050), //998,
            'thb_seapress' => $this->faker->numberBetween(950, 150), //1002,
            'wind_wind' => $this->faker->numberBetween(0., 30.), //6.4,
            'wind_avgwind' => $this->faker->numberBetween(0., 30.), //4.4,
            'wind_dir' => $this->faker->numberBetween(0, 359), //127,
            'wind_chill' => $this->faker->numberBetween(-5., 30.), //15.4,
            'rain_rate' => $this->faker->numberBetween(0., 5.), //0.1,
            'rain_total' => $this->faker->numberBetween(0., 250.), //76.3,
            'uv_index' => $this->faker->numberBetween(0., 30.), //11.1,
            'sol_rad' => $this->faker->numberBetween(0., 30.), //22.2,
            'sol_evo' => $this->faker->numberBetween(0., 5.), //2.2,
            'sun_total' => $this->faker->numberBetween(0., 30.), //4.4,
        ];
    }
}
