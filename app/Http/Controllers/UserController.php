<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class UserController
{
    public function getInformation()
    {
        if (Auth::user()) {
            $user = Auth::user();
            return response(['user' => $user]);
        }
        else {
            return response('You aren\'t authorized');
        }
    }
}
