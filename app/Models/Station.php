<?php

namespace App\Models;

use Database\Factories\StationFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Station
 *
 * @property string $name
 * @property string $code
 * @property string $city
 * @property string $chart_color
 * @property float $latitude
 * @property float $longitude
 * @property string $timezone
 * @property int $enabled
 * @property-read mixed $label
 * @property-read Collection<int, Measurement> $measurements
 * @property-read int|null $measurements_count
 * @method static Builder|Station active()
 * @method static StationFactory factory($count = null, $state = [])
 * @method static Builder|Station lastActive()
 * @method static Builder|Station newModelQuery()
 * @method static Builder|Station newQuery()
 * @method static Builder|Station query()
 * @method static Builder|Station whereChartColor($value)
 * @method static Builder|Station whereCity($value)
 * @method static Builder|Station whereCode($value)
 * @method static Builder|Station whereEnabled($value)
 * @method static Builder|Station whereLatitude($value)
 * @method static Builder|Station whereLongitude($value)
 * @method static Builder|Station whereName($value)
 * @method static Builder|Station whereTimezone($value)
 * @mixin Eloquent
 */
class Station extends Model
{
    use HasFactory;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['label'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'code';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'city',
        'name',
        'chart_color',
        'latitude',
        'longitude',
        'timezone',
        'enabled'
    ];

    /**
     * The measurements that are associated to this station.
     *
     * @return HasMany
     */
    public function measurements(): HasMany
    {
        return $this->hasMany(Measurement::class, 'station_name', 'code');
    }

    /**
     * Accessor for the `label` computed attribute.
     *
     * @return string
     */
    public function getLabelAttribute(): string
    {
        $result = $this->name;
        if ($this->city) {
            $result = $this->city.":".$result;
        }
        return $result;
    }

    /**
     * Returns the amount of seconds left before the next measurement is accepted.
     * The value will be 0 (or `falsy`)
     *
     * @return int
     */
    public function checkLockout() : int
    {
        $lockout_time = env('REQUEST_TIMEOUT_IN_SECONDS', 55);

        $lastMeasurement = $this->measurements()->latest()->first();


        if (!$lastMeasurement) {
            return false;
        }

        //dd($lastMeasurement);
        $allowedAfter = $lastMeasurement->created_at->addSeconds($lockout_time);

        if (now() < $allowedAfter) {
            return now()->diffInSeconds($allowedAfter);
        } else {
            return 0;
        }
    }

    /**
     * Query scope for the active stations, where `enabled` equals to `true`.
     *
     * @param Builder $query
     * @return void
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('enabled', true);
    }
}
