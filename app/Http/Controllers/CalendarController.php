<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCalendarRequest;
use App\Http\Requests\DeleteCalendarRequest;
use App\Http\Requests\EditCalendarRequest;
use App\Http\Requests\ListCalendarsRequest;
use Auth;
use App\Database\Calendar as CalendarRecordManager;

class CalendarController extends Controller
{
    /**
     * @var CalendarRecordManager
     */
    private $calendarDb;

    /**
     * CalendarController constructor.
     * @param CalendarRecordManager $calendarDb
     */
    public function __construct(CalendarRecordManager $calendarDb)
    {
        $this->calendarDb = $calendarDb;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AddCalendarRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(AddCalendarRequest $request)
    {
        $this->calendarDb->createCalendar(
            Auth::user(),
            $request->get('year'),
            $request->get('month'),
            $request->get('day')
        );

        return response()->json([
            'message' => 'Success',
        ]);
    }

    /**
     * Edit existing calendar
     *
     * @param EditCalendarRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(EditCalendarRequest $request)
    {
        $this->calendarDb->updateCalendar(
            $request->get('calendar_id'),
            $request->get('year'),
            $request->get('month'),
            $request->get('day')
        );

        return response()->json([
            'message' => 'Success',
        ]);
    }

    /**
     * Delete calendar
     *
     * @param DeleteCalendarRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(DeleteCalendarRequest $request)
    {
        $this->calendarDb->deleteCalendar($request->get('calendar_id'));

        return response()->json([
            'message' => 'Success',
        ]);
    }

    /**
     * List calendars
     *
     * @param ListCalendarsRequest $request
     * @return \Illuminate\Support\Collection
     */
    public function list(ListCalendarsRequest $request)
    {
        return $this->calendarDb->listCalendars($request->get('order_by'), $request->get('filter'));
    }
}
