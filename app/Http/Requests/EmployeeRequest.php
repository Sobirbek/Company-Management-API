<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Helpers\Helper;
use Illuminate\Contracts\Validation\Validator;

class EmployeeRequest extends FormRequest
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
        return [
            'passport_pin' => 'required',
            'surname' => 'required',
            'name' => 'required',
            'middle_name' => 'required',
            'position' => 'required',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:12',
            'address' => 'required',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        Helper::sendError('Validation error', $validator->errors()->toArray());
    }
}
