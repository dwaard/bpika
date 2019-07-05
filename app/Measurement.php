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
        'station'
    ];

    /**
     * Return validation rules for the resource
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'station' => 'required|string|max:255'
        ];
    }

    /**
     * Returns the latest inserted record of a Measurement filtered by station name
     *
     * @param string $name
     * @return Measurement
     */
    public static function getLastMeasurementByStationName(string $name): Measurement
    {
        return Measurement::where('station', $name)->orderBy('created_at', 'desc')->first();
    }
}
