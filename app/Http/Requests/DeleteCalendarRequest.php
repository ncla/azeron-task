<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteCalendarRequest extends FormRequest
{
    use IsAdminAuthorize;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'calendar_id' => 'required|numeric|exists:App\Calendar,id',
        ];
    }
}
