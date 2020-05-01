<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Measurement extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'station_name', 'th_temp', 'th_hum', 'th_dew', 'th_heatindex', 'thb_temp', 'thb_hum', 'thb_dew',
        'thb_press', 'thb_seapress', 'wind_wind', 'wind_avgwind', 'wind_dir', 'wind_chill',
        'rain_rate', 'rain_total', 'uv_index', 'sol_rad', 'sol_evo', 'sun_total'
    ];

    /**
     * Return validation rules for the resource
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'station_name' => 'required|exists:stations,name',
            'th_temp' => 'sometimes',
            'th_hum' => 'sometimes',
            'th_dew' => 'sometimes',
            'th_heatindex' => 'sometimes',
            'thb_temp' => 'sometimes',
            'thb_hum' => 'sometimes',
            'thb_dew' => 'sometimes',
            'thb_press' => 'sometimes',
            'thb_seapress' => 'sometimes',
            'wind_wind' => 'sometimes',
            'wind_avgwind' => 'sometimes',
            'wind_dir' => 'sometimes',
            'wind_chill' => 'sometimes',
            'rain_rate' => 'sometimes',
            'rain_total' => 'sometimes',
            'uv_index' => 'sometimes',
            'sol_rad' => 'sometimes',
            'sol_evo' => 'sometimes',
            'sun_total' => 'sometimes',
        ];
    }

    /**
     * Returns the latest inserted record of a Measurement filtered by station name
     *
     * @param string $name
     * @return Measurement | null
     */
    public static function getLastMeasurementByStationName(string $name): ?Measurement
    {
        return Measurement::all()->sortBy('created_at', 'desc')->where('station_name', $name)->first();
    }
}
