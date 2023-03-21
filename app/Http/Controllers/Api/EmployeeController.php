<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Helpers\Helper;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;

class EmployeeController extends Controller
{
    //Check employee exist 
    public static function checkEmployee($employee = [])
    {
        if (empty($employee)) {
            Helper::sendError(__('Employee not found!'), [], 404);
        }
    }
    //Check if current company user id
    protected function checkCompanyOwner($user = [], $company = [])
    {
        if (!empty($user) && ! empty($user)) {
            if ($user->id != $company->user_id && !$user->hasRole('admin')) {
                Helper::sendError(__('You do not have the appropriate permissions!'), [], 403);
            }
        }
    } 
    //Get employees by company id
    public function getEmployeesByCompanyId(string $id)
    {
        //Check user permission
        Helper::checkUserPermission('employee show');
        //Check if company exists
        CompanyController::checkCompanyExist($id);
        //Get user companies
        $employees = Cache::remember('employees_'.$id, 60*60*12, function () use ($id) {
            return Employee::with('company')->where('company_id', $id)->get();
        });

        if ($employees->isEmpty()) {
            Helper::sendError(__('Employees not found!'), [], 404);
        }
        return EmployeeResource::collection($employees);
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

    //Check passport pin by company
    public function checkEmployeeDataByCompany($data = [], $key = 'passport_pin')
    {
        if (!empty($data)) {
            //Check if passport pin exists or not
            if ($key == 'passport_pin') {
                $checkPassport = Employee::where([['passport_pin', $data['passport_pin']], ['company_id', $data['companyId'] ]])->first();
                if (!empty($checkPassport)) {
                    if (in_array($data['method'], array('PUT','PATCH'))) {
                        if ($checkPassport->id != $data['requestId']) {
                            Helper::sendError(__('The passport pin exists in your employee!'), [], 422);
                        }
                    } else {
                        Helper::sendError(__('The passport pin exists in your employee!'), [], 422);
                    }
                }
                //Check passport pin valid format
                $validPassportPin = $this->validatePassportPin($data['passport_pin']);
                if (empty($validPassportPin)) {
                    Helper::sendError(__('The Passport pin is invalid!'), [], 422);
                }
            }
            //Check if phone exists or not
            if ($key == 'phone_exists') {
                $checkPhone = Employee::where([['phone', $data['phone']], ['company_id', $data['companyId']]])->first();
                if (!empty($checkPhone)) {
                    if (in_array($data['method'], array('PUT','PATCH'))) {
                        if ($checkPhone->id != $data['requestId']) {
                            Helper::sendError(__('The phone exists in your employee!'), [], 422);
                        }
                    } else {
                        Helper::sendError(__('The phone exists in your employee!'), [], 422);
                    }
                }
            }

        }
    }

    /**
     * Store new employee.
     */
    public function store(EmployeeRequest $request, String $id)
    {
        //Check if company exists
        CompanyController::checkCompanyExist($id);
        $company = Company::find($id);
        //Check user permission
        Helper::checkUserPermission('employee create');
        //Check if current user is company owner
        $this->checkCompanyOwner(Auth()->user(), $company);
        $data = [
            'method' => 'POST',
            'passport_pin' => $request->passport_pin,
            'companyId' => $company->id,
            'phone' => $request->phone
        ];
        //Check passport pin exists and valid format in your employee
        $this->checkEmployeeDataByCompany($data);
        //Check phone exists in your employee
        $this->checkEmployeeDataByCompany($data, 'phone_exists');
        //Replace passport pin in valid format
        $request->replace(['passport_pin' => $this->validatePassportPin($request->passport_pin)]);
        //Add company id
        $request->request->add(['company_id' => $company->id]);
        //Create new employee
        $employee = Employee::create($request->all());

        return new EmployeeResource($employee);
    }

    /**
     * Update employee.
     */
    public function update(EmployeeRequest $request, string $id)
    {
        //Check user permission
        Helper::checkUserPermission('employee edit');
        $employee = Employee::find($id);
        //Check employee exists or not
        $this->checkEmployee($employee);
        //Get company by employee id
        $company = Company::find($employee->company_id);
        //Check if current user is company owner
        $this->checkCompanyOwner(Auth()->user(), $company);
        $data = [
            'method' => 'PUT',
            'requestId' => $employee->id,
            'passport_pin' => $request->passport_pin,
            'companyId' => $company->id,
            'phone' => $request->phone
        ];
        //Check passport pin exists and valid format in your employee
        $this->checkEmployeeDataByCompany($data);
        //Check phone exists in your employee
        $this->checkEmployeeDataByCompany($data, 'phone_exists');
        //Replace passport pin valid format
        $request->replace(['passport_pin' => $this->validatePassportPin($request->passport_pin)]);
        //Update data
        $employee->update($request->all());
        return new EmployeeResource($employee);
    }

    /**
     * Delete employee.
     */
    public function destroy(string $id)
    {
        //Check user permission
        Helper::checkUserPermission('employee delete');
        $employee = Employee::find($id);
        //Check employee exists or not
        $this->checkEmployee($employee);
        //Get company by employee id
        $company = Company::find($employee->company_id);
        //Check if current user is company owner
        $this->checkCompanyOwner(Auth()->user(), $company);
        //Delete employee 
        $employee->delete();

        return response(['message' => __('Employee deleted!')], 200); 
    }
}
