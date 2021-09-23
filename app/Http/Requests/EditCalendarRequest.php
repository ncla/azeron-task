<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditCalendarRequest extends FormRequest
{
    use DateCarbonValidator;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'calendar_id' => 'exists:App\Calendar,id',
            'year' => 'required|numeric',
            'month' => 'required|numeric',
            'day' => 'required|numeric'
        ];
    }
}
