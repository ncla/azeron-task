<?php

use Illuminate\Database\Seeder;
use App\Database\Calendar as CalendarDb;
use App\User;

class TestCalendarSeeder extends Seeder
{
    /**
     * @var CalendarDb
     */
    private $calendarDb;

    /**
     * CalendarSeeder constructor.
     * @param CalendarDb $calendarDb
     */
    public function __construct(CalendarDb $calendarDb)
    {
        $this->calendarDb = $calendarDb;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $startingYear = 2021;

        for ($i = 0; $i < 10; $i++) {
            for ($c = 0; $c < 10; $c++) {
                $this->calendarDb->createCalendar(
                    factory(User::class)->create(),
                    ($startingYear - $i),
                    random_int(1, 12),
                    random_int(1, 28)
                );
            }
        }
    }
}
