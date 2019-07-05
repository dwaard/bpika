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
        'station', 'th0temp'
    ];

    /**
     * Return validation rules for the resource
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'station' => 'required|string|max:255',
            'th0temp' => 'numeric|min:-50|max:75'
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
        return Measurement::where('station', $name)->orderBy('created_at', 'desc')->first();
    }
}
