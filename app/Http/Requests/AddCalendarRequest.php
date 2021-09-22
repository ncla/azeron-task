<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class AddCalendarRequest extends FormRequest
{
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

    /**
     * Configure the validator instance to add check for valid day/month/year number values with Carbon.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            /**
             * @var $validator \Illuminate\Validation\Validator
             */
            try {
                Carbon::createSafe(
                    $this->request->get('year'),
                    $this->request->get('month'),
                    $this->request->get('day'),
                    0,
                    0,
                    0
                );
            } catch (\Carbon\Exceptions\InvalidDateException $exp) {
                 $validator->errors()->add('calendar', 'Invalid year/month/day number values!');
            }
        });
    }
}
