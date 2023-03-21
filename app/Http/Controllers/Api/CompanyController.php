<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\CompanyResource;
use App\Http\Requests\CompanyRequest;
use App\Http\Helpers\Helper;
use App\Models\Company;
use App\Models\User;


class CompanyController extends Controller
{
    //Check if company exists or not
    public static function checkCompanyExist($id = 0)
    {
        $company = Company::find($id);
        if (!$company) {
            Helper::sendError(__('Company not found!'), [], 404);
        }
    }

    //Check if current user have permission to do this action
    protected function userCan($action = '', $company = [])
    {
        if (! empty($action)) {
            $UserCan = Gate::inspect($action, $company);
            if (!$UserCan->allowed()) {
                Helper::sendError(__('You do not have any permission!'), [], 403);
            }
        }
    }

    /**
     * Display companies by permissions.
     */
    public function index()
    {
        //Check current user permission
        Helper::checkUserPermission('company show');
        //Get all companies if current user is admin
        if (Auth()->user()->hasRole('admin')) {
            $companies = Cache::remember('companies', 60*60*12, function () {
                return Company::all();
            });
        } else {
            //Get user companies
            $companies = Cache::remember('companies_'.Auth()->user()->id, 60*60*12, function () {
                return Company::with('user')->where('user_id', Auth()->user()->id)->get();
            });

            if ($companies->isEmpty()) {
                Helper::sendError(__('Companies not found!'), [], 404);
            }
        }

        return CompanyResource::collection($companies);
    }

    /**
     * Store new company.
     */
    public function store(CompanyRequest $request)
    {
        //Check current user permission
        Helper::checkUserPermission('company create');
        //Add current user id to request company user id
        $request->request->add(['user_id' => Auth()->user()->id]);
        //Create new company
        $company = Company::create($request->all());

        return new CompanyResource($company);
    }

    /**
     * Update company.
     */
    public function update(CompanyRequest $request, String $id)
    {
        $company = Company::find($id);
        //Check current user permission
        Helper::checkUserPermission('company edit');
        //Check if company exists
        self::checkCompanyExist($id);
        //Check if current user can update this company 
        $this->userCan('update', $company);
        //Update
        $company->update($request->all());

        return new CompanyResource($company);
    }

    /**
     * Delete company.
     */
    public function destroy(string $id)
    {
        $company = Company::find($id);
        //Check if company exists
        self::checkCompanyExist($id);
        //Check current user permission
        Helper::checkUserPermission('company delete');
        //Check if current user can update this company 
        $this->userCan('delete', $company);
        //Delete
        $company->delete();
        
        return response(['message' => __('Company deleted!')], 200);
    }
}
