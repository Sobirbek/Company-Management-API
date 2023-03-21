<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\AuthRequest;
use Illuminate\Http\Request;
use App\Http\Helpers\Helper;
use App\Http\Resources\UserResource;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Auth;

class AuthController extends Controller
{
    //Register new user
    public function register(AuthRequest $request)
    {
        $user = User::create($request->all());
        $role = Role::where(['name' => 'company'])->first();
        if ($role) {
            $user->assignRole($role);
        }
        return new UserResource($user);
    }

    //Login
    public function login(AuthRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            Helper::sendError(__('Email or Password is wrong!'));
        }

        return new UserResource(Auth()->user());
    }

    //Logout
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response(['message' => __('Logged out') ], 200);
    }
}
