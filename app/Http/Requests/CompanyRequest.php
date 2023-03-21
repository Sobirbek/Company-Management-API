<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use App\Http\Helpers\Helper;
use App\Models\Company;

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

        $companyRules = [
            'name' => 'required',
            'owner' => 'required',
            'email' => 'required|email|unique:companies,email',
            'address' => 'required',
            'website' => 'required',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:12|unique:companies,phone',
        ];
        if (request()->is('api/companies/*') && in_array(request()->method(), array('PUT', 'PATCH'))) {
            $company = Company::where('email', request()->email)->first();
            $companyRules['email'] = ['required', 'email', Rule::unique('companies')->ignore($company->id)];
            $companyRules['phone'] = ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:12', Rule::unique('companies')->ignore($company->id)];
        }
        
        return $companyRules;
    }

    public function failedValidation(Validator $validator)
    {
        Helper::sendError('Validation error', $validator->errors()->toArray(), 422);
    }
}
