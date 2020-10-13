<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class Station extends Model
{

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

    public function getLabelAttribute()
    {
        $result = $this->name;
        if ($this->city) {
            $result = $this->city.":".$result;
        }
        return $result;
    }
}
