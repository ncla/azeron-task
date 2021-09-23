<?php

namespace Tests\Feature;

use App\Calendar;
use App\Month;
use App\User;
use App\Year;
use App\Day;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function __createCalendar()
    {
        $calendar = factory(Calendar::class)->create();

        $year = factory(Year::class)->create([
            'calendar_id' => $calendar->getAttribute('id')
        ]);

        $month = factory(Month::class)->create([
            'year_id' => $year->getAttribute('id')
        ]);

        factory(Day::class)->create([
            'month_id' => $month->getAttribute('id')
        ]);

        return $calendar;
    }

    public function testGuestsCannotAddCalendars()
    {
        $response = $this->putJson('/calendar/add', [
            'year' => 2021,
            'month' => 9,
            'day' => 22
        ]);

        $response->assertStatus(401);
    }

    public function testLoggedInUsersWithoutAdminRoleCannotAddCalendar()
    {
        $user = factory(User::class)->create();

        $response = $this
            ->actingAs($user)
            ->putJson('/calendar/add', [
                'year' => 2021,
                'month' => 9,
                'day' => 22
            ]);

        $response->assertStatus(403);
    }

    public function testAddingCalendarCreatesDatabaseRecords()
    {
        $user = factory(User::class)->create([
            'is_admin' => 1
        ]);

        $response = $this
            ->actingAs($user)
            ->putJson('/calendar/add', [
                'year' => 2021,
                'month' => 9,
                'day' => 22
            ]);

        $response->assertStatus(200);

        $this->assertEquals(1, Calendar::count());

        $calendar = Calendar::first();

        $this->assertDatabaseHas('calendar_years', [
            'year' => 2021,
            'calendar_id' => $calendar->getAttribute('id')
        ]);

        $this->assertDatabaseHas('calendar_months', [
            'month' => 9,
            'year_id' => $calendar->year->id
        ]);

        $this->assertDatabaseHas('calendar_days', [
            'day' => 22,
            'month_Id' => $calendar->month->id
        ]);
    }

    public function testInvalidAddCalendarRequestParamsAreUnprocessable()
    {
        $user = factory(User::class)->create([
            'is_admin' => 1
        ]);

        $response = $this
            ->actingAs($user)
            ->putJson('/calendar/add', [
                'year' => 'lol',
                'month' => 'eee',
            ]);

        $response->assertStatus(422);
        $response->assertSee('The given data was invalid');

        // Impossible day in a month, impossible month in a year
        $response = $this
            ->actingAs($user)
            ->putJson('/calendar/add', [
                'year' => 2021,
                'month' => 13,
                'day' => 40
            ]);

        $response->assertStatus(422);
        $response->assertSee('The given data was invalid');
    }

    public function testEditRequestUpdatesCalendarValuesInDatabase()
    {
        // Create calendar that we are gonna edit
        $calendar = $this->__createCalendar();

        $this->assertEquals(1, Calendar::count());
        $this->assertEquals(1, Year::count());
        $this->assertEquals(1, Month::count());
        $this->assertEquals(1, Calendar::count());

        $user = factory(User::class)->create([
            'is_admin' => 1
        ]);

        $response = $this
            ->actingAs($user)
            ->patchJson('/calendar/edit', [
                'calendar_id' => $calendar->getAttribute('id'),
                'year' => 1994,
                'month' => 8,
                'day' => 17
            ]);

        $response->assertStatus(200);
        $this->assertEquals( 1994, Calendar::whereId($calendar->getAttribute('id'))->firstOrFail()->year->year);
        $this->assertEquals( 8, Calendar::whereId($calendar->getAttribute('id'))->firstOrFail()->month->month);
        $this->assertEquals( 17, Calendar::whereId($calendar->getAttribute('id'))->firstOrFail()->day->day);
    }

    public function testEditRequestValidationChecksForExistenceOfCalendarRecord()
    {
        $this->__createCalendar();

        $user = factory(User::class)->create([
            'is_admin' => 1
        ]);

        $response = $this
            ->actingAs($user)
            ->patchJson('/calendar/edit', [
                'calendar_id' => 1337,
                'year' => 1994,
                'month' => 8,
                'day' => 17
            ]);

        $response->assertStatus(422);
        $response->assertSee('The selected calendar id is invalid');
    }

    public function testDeleteRequestDeletesAllRelatedDatabaseEntries()
    {
        $this->__createCalendar();

        $this->assertEquals(1, Calendar::count());

        $user = factory(User::class)->create([
            'is_admin' => 1
        ]);

        $response = $this
            ->actingAs($user)
            ->deleteJson('/calendar/delete', [
                'calendar_id' => 1,
            ]);

        $response->assertStatus(200);
        $this->assertEquals(0, Calendar::count());
        $this->assertEquals(0, Year::count());
        $this->assertEquals(0, Month::count());
        $this->assertEquals(0, Day::count());
    }
}
