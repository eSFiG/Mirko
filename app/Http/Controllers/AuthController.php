<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorizeRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController
{
    public function registration(RegisterRequest $request)
    {
        $data = $request->validated();
        if ($data) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            $user = new User();
            $user->fill($data)->save();

            return response('Registered');
        }
    }

    public function authorization(AuthorizeRequest $request)
    {
        if (Auth::attempt($request->validated())) {
            $user = Auth::user();
            $user->tokens()->delete();

            $token = $user->createToken('AuthToken')->plainTextToken;
            $user->api_token = $token;
            $user->save();

            return response(['token' => $token]);
        }

        return response('Wrong email or password');
    }
}
