<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Year
 *
 * @property int $id
 * @property int $year
 * @property int $calendar_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Calendar $calendar
 * @property-read \App\Month|null $month
 * @method static \Illuminate\Database\Eloquent\Builder|Year newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Year newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Year query()
 * @method static \Illuminate\Database\Eloquent\Builder|Year whereCalendarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Year whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Year whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Year whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Year whereYear($value)
 * @mixin \Eloquent
 */
class Year extends Model
{
    protected $table = 'calendar_years';

    public function calendar()
    {
        return $this->belongsTo('App\Calendar');
    }

    public function month()
    {
        return $this->hasOne('App\Month');
    }

    public function day()
    {
        return $this->month->day();
    }
}
