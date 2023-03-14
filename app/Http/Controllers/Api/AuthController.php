<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Helpers\Helper;
use Auth;
use App\Http\Resources\UserResource;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        //Get request token
        $token = request()->bearerToken();
        if (empty($token)) {
            Helper::sendError(__('Token is empty!'));
        }
        //Check token
        $checkToken = PersonalAccessToken::findToken($token);
        if (!$checkToken) {
            Helper::sendError(__('Token is incorrect!'));
        }

        // $user = $checkToken->tokenable;
        // $role = $user->getRoleNames();
        // if (!in_array($role, array('admin'))) {
        //     Helper::sendError(__('You do not have permissions to use the API'));
        // }

        //Register new user


        return true;
    }

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            Helper::sendError(__('Email or Password is wrong!'));
        }

        return new UserResource(Auth()->user());
    }
}
