<?php

namespace App\Database;

use App\Calendar as EloquentCalendar;
use App\User;
use Illuminate\Support\Collection;

/**
 * Class Calendar
 *
 * Service class to do CRUD operations with Eloquent to database related to calendar
 *
 * @package App\Database
 */
class Calendar
{
    /**
     * @param User $user
     * @param int $year
     * @param int $month
     * @param int $day
     * @return mixed
     */
    public function createCalendar(User $user, int $year, int $month, int $day)
    {
        return EloquentCalendar::create([
            'title' => $user->name . ' calendar',
            'user_id' => $user->id
        ])->year()->create([
            'year' => $year
        ])->month()->create([
            'month' => $month
        ])->day()->create([
            'day' => $day
        ]);
    }

    /**
     * @param int $calendarId
     * @param int $year
     * @param int $month
     * @param int $day
     * @return bool
     */
    public function updateCalendar(int $calendarId, int $year, int $month, int $day): bool
    {
        $calendar = EloquentCalendar::where('id', $calendarId)->firstOrFail();

        $calendar->year->update([
            'year' => $year
        ]);

        $calendar->month->update([
            'month' => $month
        ]);

        $calendar->day->update([
            'day' => $day
        ]);

        return true;
    }

    /**
     * @param int $calendarId
     * @return bool|mixed|null
     * @throws \Exception
     */
    public function deleteCalendar(int $calendarId)
    {
        return EloquentCalendar::whereId($calendarId)->delete();
    }

    /**
     * @param $orderBy
     * @param $filters
     * @param \App\Database\CalendarListQueryBuilder $calendarListQueryBuilder
     * @return \Illuminate\Support\Collection
     */
    public function listCalendars($orderBy, $filters): Collection
    {
        // TODO: Instead of hard creating object do DI? DI didn't work out as it turned into singleton
        $queryBuilder = new CalendarListQueryBuilder();
        return $queryBuilder->get($orderBy, $filters);
    }
}