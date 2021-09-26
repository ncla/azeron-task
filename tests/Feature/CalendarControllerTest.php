<?php

namespace Tests\Feature;

use App\Calendar;
use App\Month;
use App\User;
use App\Year;
use App\Day;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TestCalendarPredictableSeeder;
use Tests\TestCase;
use TestCalendarSeeder;

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

    public function testListEndpointListsExpectedCalendarCount()
    {
        $this->seed(TestCalendarSeeder::class);

        $user = factory(User::class)->create([
            'is_admin' => 1
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/calendar/list');

        $response->assertJsonCount(100);
    }

    public function testIfNonElevatedUsersCanUseListEndpoint()
    {
        $user = factory(User::class)->create([
            'is_admin' => 0
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/calendar/list');

        $response->assertStatus(200);
    }

    public function testGuestsCannotAccessListEndpoint()
    {
        $response = $this
            ->postJson('/calendar/list');

        $response->assertStatus(401);
    }

    public function testListEndpointSortsCalendarsByYear()
    {
        $this->seed(TestCalendarSeeder::class);

        $user = factory(User::class)->create([
            'is_admin' => 0
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/calendar/list', [
                'order_by' => [
                    'year' => 'desc'
                ]
            ]);

        $json = $response->json();

        $this->assertEquals(2021, $json[0]['year']);

        $response = $this
            ->actingAs($user)
            ->postJson('/calendar/list', [
                'order_by' => [
                    'year' => 'asc'
                ]
            ]);

        $json = $response->json();

        $this->assertEquals(2012, $json[0]['year']);
    }

    public function testDefaultSortingIsDescForYearMonthDay()
    {
        $this->seed(TestCalendarSeeder::class);

        $user = factory(User::class)->create([
            'is_admin' => 0
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/calendar/list');

        $json = $response->json();

        // TODO: this works but probably can be written better
        $this->assertEquals(2021, $json[0]['year']);
        $this->assertEquals(2021, $json[1]['year']);
        $this->assertLessThanOrEqual($json[0]['month'], $json[1]['month']);
        $this->assertLessThanOrEqual($json[1]['month'], $json[2]['month']);
        $this->assertLessThanOrEqual($json[2]['month'], $json[3]['month']);
    }

    public function testDaySortingForListEndpoint()
    {
        $this->seed(TestCalendarSeeder::class);

        $user = factory(User::class)->create([
            'is_admin' => 0
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/calendar/list', [
                'order_by' => [
                    'day' => 'desc'
                ]
            ]);

        $json = $response->json();

        $this->assertLessThanOrEqual($json[0]['day'], $json[1]['day']);
        $this->assertLessThanOrEqual($json[1]['day'], $json[2]['day']);
        $this->assertLessThanOrEqual($json[2]['day'], $json[3]['day']);
    }

    public function testAndFilterOperatorReturnsExpectedCalendars()
    {
        $this->seed(TestCalendarPredictableSeeder::class);

        $user = factory(User::class)->create([
            'is_admin' => 0
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/calendar/list', [
                'filter' => [
                    'and' => [
                        'year' => 1994,
                        'month' => 1,
                        'day' => 1
                    ]
                ]
            ]);

        $response->assertJson(
            [
                [
                    'year' => 1994,
                    'month' => 1,
                    'day' => 1
                ]
            ]
        );

        $response = $this
            ->actingAs($user)
            ->postJson('/calendar/list', [
                'filter' => [
                    'and' => [
                        'month' => 1,
                        'day' => 1
                    ]
                ]
            ]);

        $response->assertJsonCount(2);
        $response->assertJsonFragment([
            'day' => '1',
            'month' => '1',
        ]);
    }

    public function testOrFilterOperatorReturnsExpectedCalendars()
    {
        $this->seed(TestCalendarPredictableSeeder::class);

        $user = factory(User::class)->create([
            'is_admin' => 0
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/calendar/list', [
                'filter' => [
                    'or' => [
                        'day' => [1, 2],
                    ]
                ]
            ]);

        $response
            ->assertJsonFragment(['day' => '1'])
            ->assertJsonFragment(['day' => '2']);
    }

    public function testInFilterOperatorReturnsExpectedCalendars()
    {
        $this->seed(TestCalendarPredictableSeeder::class);

        $user = factory(User::class)->create([
            'is_admin' => 0
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/calendar/list', [
                'filter' => [
                    'in' => [
                        'year' => [1994, 1995],
                        'month' => 9,
                        'day' => [17, 18]
                    ]
                ]
            ]);

        $response
            ->assertJsonFragment(['day' => '17', 'month' => '9', 'year' => '1994'])
            ->assertJsonFragment(['day' => '18', 'month' => '9', 'year' => '1995']);
    }

    public function testMixedFilterOperatorsReturnsExpectedCalendars()
    {
        $this->seed(TestCalendarPredictableSeeder::class);

        $user = factory(User::class)->create([
            'is_admin' => 0
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/calendar/list', [
                'filter' => [
                    'in' => [
                        'year' => [1994, 1995],
                    ],
                    'and' => [
                        'month' => 9,
                        'day' => 17
                    ]
                ]
            ]);

        $response
            ->assertJsonFragment(['day' => '17', 'month' => '9', 'year' => '1994'])
            ->assertJsonFragment(['day' => '17', 'month' => '9', 'year' => '1995']);
    }

    public function testMixedOperatorsAndOrderingReturnsExpectedOrderedCalendars()
    {
        $this->seed(TestCalendarPredictableSeeder::class);

        $user = factory(User::class)->create([
            'is_admin' => 0
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/calendar/list', [
                'filter' => [
                    'in' => [
                        'year' => [1994, 1995],
                    ],
                    'and' => [
                        'month' => 9,
                        'day' => 17
                    ]
                ],
                'order_by' => [
                    'year' => 'asc'
                ]
            ]);

        $json = $response->json();

        $this->assertEquals(1994, $json[0]['year']);

        $response
            ->assertJsonFragment(['day' => '17', 'month' => '9', 'year' => '1994'])
            ->assertJsonFragment(['day' => '17', 'month' => '9', 'year' => '1995']);
    }
}
