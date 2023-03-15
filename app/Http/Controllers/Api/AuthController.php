<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Helpers\Helper;
use App\Http\Resources\UserResource;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        //Register new user
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password)
        ]);
        $role = Role::where(['name' => 'company'])->first();
        if ($role) {
            $user->assignRole($role);
        }
        
        return new UserResource($user);
    }

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            Helper::sendError(__('Email or Password is wrong!'));
        }

        return new UserResource(Auth()->user());
    }
}
