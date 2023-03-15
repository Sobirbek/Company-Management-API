<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Helpers\Helper;
use Illuminate\Contracts\Validation\Validator;

class CompanyRequest extends FormRequest
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
            'name' => 'required',
            'owner_fname' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'website' => 'required',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:12',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        Helper::sendError('Validation error', $validator->errors()->toArray());
    }
}