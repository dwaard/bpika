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
        'thb_press', 'thb_seapress', 'wind_wind', 'wind_avgwind', 'wind_chill',
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
            'th_temp' => 'numeric|min:-50|max:75',
            'th_hum' => 'numeric|min:0|max:100',
            'th_dew' => 'numeric|min:-50|max:75',
            'th_heatindex' => 'numeric|min:-50|max:75',
            'thb_temp' => 'numeric|min:-50|max:75',
            'thb_hum' => 'numeric|min:0|max:100',
            'thb_dew' => 'numeric|min:-50|max:75',
            'thb_press' => 'numeric|min:700|max:1200',
            'thb_seapress' => 'numeric|min:700|max:1200',
            'wind_wind' => 'numeric|min:0|max:120',
            'wind_avgwind' => 'numeric|min:0|max:120',
            'wind_chill' => 'numeric|min:-50|max:75',
            'rain_rate' => 'numeric|min:0|max:200',
            'rain_total' => 'numeric|min:0|max:2000',
            'uv_index' => 'numeric|min:0|max:800',
            'sol_rad' => 'numeric|min:0|max:1500',
            'sol_evo' => 'numeric|min:0|max:200',
            'sun_total' => 'numeric|min:0|max:24',
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
        return Measurement::where('station_name', $name)->orderBy('created_at', 'desc')->first();
    }
}
