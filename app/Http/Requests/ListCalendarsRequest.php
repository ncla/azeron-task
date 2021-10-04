<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListCalendarsRequest extends FormRequest
{
    /**
     * @var array
     */
    const ALLOWED_FIELDS = [
        'year',
        'month',
        'day',
        'calendar_id'
    ];

    /**
     * @var array
     */
    const ALLOWED_FILTER_OPERATORS = [
        'in',
        'or',
        'and'
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_by' => [
                'array',
                function ($attribute, $value, $fail) {
                    if (count(array_diff_key($value, array_flip(self::ALLOWED_FIELDS))) > 0) {
                        $fail($attribute . ' contains disallowed field ids.');
                    }
                }
            ],
            'order_by.*' => [
                'in:asc,desc'
            ],
            'filter' => [
                'array',
                function ($attribute, $value, $fail) {
                    if (count(array_diff_key($value, array_flip(self::ALLOWED_FILTER_OPERATORS))) > 0) {
                        $fail($attribute . ' contains disallowed operator.');
                    }
                }
            ],
            'filter.*' => [
                function ($attribute, $value, $fail) {
                    if (count(array_diff_key($value, array_flip(self::ALLOWED_FIELDS))) > 0) {
                        $fail($attribute . ' contains disallowed field ids.');
                    }
                }
            ],
            'filter.*.*' => [
                function ($attribute, $value, $fail) {
                    if (is_array($value) === false && is_numeric($value) === false) {
                        $fail($attribute . ' must be a number or array.');
                    }
                }
            ],
            'filter.*.*.*' => [
                'numeric'
            ]
        ];
    }
}
