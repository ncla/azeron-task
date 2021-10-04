<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Month
 *
 * @property int $id
 * @property int $month
 * @property int $year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Day|null $day
 * @property-read \App\Year $year
 * @method static \Illuminate\Database\Eloquent\Builder|Month newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Month newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Month query()
 * @method static \Illuminate\Database\Eloquent\Builder|Month whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Month whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Month whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Month whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Month whereYearId($value)
 * @mixin \Eloquent
 */
class Month extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'calendar_months';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['month'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function calendar()
    {
        return $this->year->calendar();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function year()
    {
        return $this->belongsTo('App\Year');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function day()
    {
        return $this->hasOne('App\Day');
    }
}
