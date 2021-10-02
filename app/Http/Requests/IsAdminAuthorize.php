<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;

trait IsAdminAuthorize
{
    /**
     * Authorization check if user is logged in and if user is admin
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user() && Auth::user()->is_admin;
    }
}