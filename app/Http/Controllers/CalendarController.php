<?php

namespace App\Http\Controllers;

use App\Calendar;
use App\Http\Requests\AddCalendarRequest;
use Auth;

class CalendarController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param AddCalendarRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(AddCalendarRequest $request)
    {
        Calendar::create([
            'title' => Auth::user()->name . ' calendar',
            'user_id' => Auth::user()->id
        ])->year()->create([
            'year' => $request->get('year')
        ])->month()->create([
            'month' => $request->get('month')
        ])->day()->create([
            'day' => $request->get('day')
        ]);

        return response()->json([
            'message' => 'Success',
        ]);
    }
}
