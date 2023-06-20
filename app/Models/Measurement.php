<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Measurement
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $station_name
 * @property float|null $th_temp
 * @property float|null $th_hum
 * @property float|null $th_dew
 * @property float|null $th_heatindex
 * @property float|null $thb_temp
 * @property float|null $thb_hum
 * @property float|null $thb_dew
 * @property float|null $thb_press
 * @property float|null $thb_seapress
 * @property float|null $wind_wind
 * @property float|null $wind_avgwind
 * @property float|null $wind_dir
 * @property float|null $wind_chill
 * @property float|null $rain_rate
 * @property float|null $rain_total
 * @property float|null $uv_index
 * @property float|null $sol_rad
 * @property float|null $sol_evo
 * @property float|null $sun_total
 * @method static Builder|Measurement newModelQuery()
 * @method static Builder|Measurement newQuery()
 * @method static Builder|Measurement query()
 * @method static Builder|Measurement whereCreatedAt($value)
 * @method static Builder|Measurement whereId($value)
 * @method static Builder|Measurement whereRainRate($value)
 * @method static Builder|Measurement whereRainTotal($value)
 * @method static Builder|Measurement whereSolEvo($value)
 * @method static Builder|Measurement whereSolRad($value)
 * @method static Builder|Measurement whereStationName($value)
 * @method static Builder|Measurement whereSunTotal($value)
 * @method static Builder|Measurement whereThDew($value)
 * @method static Builder|Measurement whereThHeatindex($value)
 * @method static Builder|Measurement whereThHum($value)
 * @method static Builder|Measurement whereThTemp($value)
 * @method static Builder|Measurement whereThbDew($value)
 * @method static Builder|Measurement whereThbHum($value)
 * @method static Builder|Measurement whereThbPress($value)
 * @method static Builder|Measurement whereThbSeapress($value)
 * @method static Builder|Measurement whereThbTemp($value)
 * @method static Builder|Measurement whereUpdatedAt($value)
 * @method static Builder|Measurement whereUvIndex($value)
 * @method static Builder|Measurement whereWindAvgwind($value)
 * @method static Builder|Measurement whereWindChill($value)
 * @method static Builder|Measurement whereWindDir($value)
 * @method static Builder|Measurement whereWindWind($value)
 * @mixin Eloquent
 */
class Measurement extends Model
{
    use HasFactory;

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
}
