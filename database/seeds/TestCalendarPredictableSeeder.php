<?php

use Illuminate\Database\Seeder;
use App\Database\Calendar as CalendarDb;
use App\User;

class TestCalendarPredictableSeeder extends Seeder
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
        foreach ([1994, 1995] as $year) {
            for ($m = 1; $m <= 12; $m++) {
                for ($d = 1; $d <= 28; $d++) {
                    $this->calendarDb->createCalendar(
                        factory(User::class)->create(),
                        $year,
                        $m,
                        $d
                    );
                }
            }
        }
    }
}
