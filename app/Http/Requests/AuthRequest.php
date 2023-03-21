<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Helpers\Helper;

class AuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $authRules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ];

        if (request()->is('api/login')) {
            $authRules = [
                'email' => 'required|email',
                'password' => 'required',
            ];
        }
        return $authRules;
    }
    //Send Validation error messages
    public function failedValidation(Validator $validator)
    {
        Helper::sendError('Validation error', $validator->errors()->toArray(), 422);
    }
}
