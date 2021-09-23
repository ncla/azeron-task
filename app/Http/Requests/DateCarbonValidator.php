<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidDateException;

trait DateCarbonValidator
{
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
            } catch (InvalidDateException $exp) {
                $validator->errors()->add('calendar', 'Invalid year/month/day number values!');
            }
        });
    }
}