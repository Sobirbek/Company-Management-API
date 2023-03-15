<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\EmployeeCollection;
use App\Http\Helpers\Helper;
use App\Models\Company;
use App\Models\Employee;


class EmployeeController extends Controller
{
    //Get employees by company id
    public function getEmployeesByCompanyId(string $id)
    {
        $token = request()->bearerToken();
        $user = Helper::getUserByToken($token);
        $employees = [];
        if (empty($user)) {
            Helper::sendError(__('Token is invalid!'));
        }
        $company = Company::find($id);
        if (!$company) {
            Helper::sendError(__('Company not found!'));
        }

        if ($user->id != $company->user_id && !$user->hasRole('admin')) {
            Helper::sendError(__('You do not have the appropriate permissions!'));
        }
        if($user->hasPermissionTo('employee show')){
            $employees = $company->employees()->get();
            if ($employees->isEmpty()) {
                Helper::sendError(__('Employees not found'));
            }
        } else {
            Helper::sendError(__('You do not have the appropriate permissions!'));
        }
        return new EmployeeCollection($employees);
    }

    //Validate Passport pin number
    public function validatePassportPin($passportPin = '', $maxLimit = 7)
    {
        $validPassportPin = '';
        if (! empty($passportPin)) {
            $passportStartingLetter = substr($passportPin,0,2);
            if (! empty($passportStartingLetter) && strlen($passportStartingLetter) == 2 && preg_match('/^[a-zA-Z]+$/', $passportStartingLetter)) {
                $uppercaseLetter = strtoupper($passportStartingLetter);
                $passportNumber = substr($passportPin,2);
                //Check passport number limit
                if((int)$passportNumber > 0 && strlen((string)$passportNumber) == $maxLimit){
                    $validPassportPin = $uppercaseLetter.$passportNumber;
                }
            }
            
        }
        return $validPassportPin;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeRequest $request, String $id)
    {
        $token = request()->bearerToken();
        $user = Helper::getUserByToken($token);
        if (empty($user)) {
            Helper::sendError(__('Token is invalid!'));
        }
        $company = Company::find($id);
        if (!$company) {
            Helper::sendError(__('Company not found!'));
        }

        if ($user->id != $company->user_id && !$user->hasRole('admin')) {
            Helper::sendError(__('You do not have the appropriate permissions!'));
        }

        if($user->hasPermissionTo('employee create')){
            $checkPassport = Employee::where([['passport_pin', $request->input('passport_pin')], ['company_id', $company->id ]])->get();
            if (count($checkPassport) > 0) {
                Helper::sendError(__('The passport pin exists in your employee!'));
            }

            $validPassportPin = $this->validatePassportPin($request->input('passport_pin'));
            if (empty($validPassportPin)) {
                Helper::sendError(__('The Passport pin in invalid!'));
            }
            $request->replace(['passport_pin' => $validPassportPin]);
            
            $checkPhone = Employee::where([['phone', $request->input('phone')], ['company_id', $company->id ]])->get();
            if (count($checkPhone) > 0) {
                Helper::sendError(__('The phone number exists in your employee!'));
            }

            $request->request->add(['company_id' => $company->id]);
            $employee = Employee::create($request->all());
        } else {
            Helper::sendError(__('You do not have the appropriate permissions!'));
        }
        return new EmployeeResource($employee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeRequest $request, string $id)
    {
        $token = request()->bearerToken();
        $user = Helper::getUserByToken($token);
        if (empty($user)) {
            Helper::sendError(__('Token is invalid!'));
        }
        $employee = Employee::find($id);
        if (empty($employee)) {
            Helper::sendError(__('Employee not found!'));
        }
        $company = Company::find($employee->company_id);
        if ($company->user_id != $user->id && !$user->hasRole('admin') ) {
            Helper::sendError(__('You do not have the appropriate permissions!'));
        }

        if($user->hasPermissionTo('company create')){
            $checkPassport = Employee::where([['passport_pin', $request->input('passport_pin')], ['company_id', $company->id ]])->first();
            if (! empty($checkPassport)) {
                if ($checkPassport->id != $employee->id) {
                    Helper::sendError(__('The passport pin exists in your employee!'));
                }
            }

            $validPassportPin = $this->validatePassportPin($request->input('passport_pin'));
            if (empty($validPassportPin)) {
                Helper::sendError(__('The Passport pin in invalid!'));
            }

            $request->replace(['passport_pin' => $validPassportPin]);

            $checkPhone = Employee::where([['phone', $request->input('phone')], ['company_id', $company->id ]])->first();
            if (! empty($checkPhone)) {
                if ($checkPhone->id != $employee->id) {
                    Helper::sendError(__('The phone number exists in your employee!'));
                }
            }
            $request->request->add(['company_id' => $company->id]);
            $employee->update($request->all());
        } else {
            Helper::sendError(__('You do not have the appropriate permissions!'));
        }
        return new EmployeeResource($employee);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $token = request()->bearerToken();
        $user = Helper::getUserByToken($token);
        if (empty($user)) {
            Helper::sendError(__('Token is invalid!'));
        }

        $employee = Employee::find($id);
        if (empty($employee)) {
            Helper::sendError(__('Employee not found'));
        }

        $company = Company::find($employee->company_id);
        if ($company->user_id != $user->id && !$user->hasRole('admin') ) {
            Helper::sendError(__('You do not have the appropriate permissions!'));
        }

        if($user->hasPermissionTo('employee delete')){
            $employee->delete();
        } else {
            Helper::sendError(__('You do not have the appropriate permissions!'));
        }
        return response()->json(['message' => __('Employee deleted!')]); 
    }
}
