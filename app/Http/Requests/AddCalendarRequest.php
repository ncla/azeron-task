<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCalendarRequest extends FormRequest
{
    use DateCarbonValidator, IsAdminAuthorize;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'year' => 'required|numeric',
            'month' => 'required|numeric',
            'day' => 'required|numeric'
        ];
    }
}
