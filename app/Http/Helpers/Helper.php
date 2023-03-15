<?php
namespace App\Http\Helpers;

use Illuminate\Http\Exceptions\HttpResponseException;
use Laravel\Sanctum\PersonalAccessToken;

class Helper{
    public static function sendError($message, $errors = [], $code = 401)
    {
        $response = ['success' => false, 'message' => $message];
        if (!empty($errors)) {
            $response['data'] = $errors;
        }

        throw new HttpResponseException(response()->json($response, $code)); 
    }

    public static function getUserByToken($token = '')
    {
        $user = [];
        if (!empty($token)) {
            $checkToken = PersonalAccessToken::findToken($token);
            if ($checkToken) {
                $user = $checkToken->tokenable;
            }
        }

        return $user;
    }
}