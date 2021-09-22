<?php

namespace Tests\Feature;

use App\Calendar;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarControllerTest extends TestCase
{
    use RefreshDatabase;

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

        // Impossible day in a month
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
}
