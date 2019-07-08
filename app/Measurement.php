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
        'station_name', 'th_temp', 'th_hum'
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
            'th_hum' => 'numeric|min:0|max:100'
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
