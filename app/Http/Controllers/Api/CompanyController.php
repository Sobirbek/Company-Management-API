<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\CompanyCollection;
use App\Http\Requests\CompanyRequest;
use App\Http\Helpers\Helper;
use App\Models\Company;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $token = request()->bearerToken();
        $user = Helper::getUserByToken($token);
        $data = [];
        if (empty($user)) {
            Helper::sendError(__('Token is invalid!'));
        }
        if($user->hasPermissionTo('company show')){
            $data = $user->companies()->get();
            if ($user->hasRole('admin')) {
               $data = Company::all();
            }
        } else {
            Helper::sendError(__('You do not have the appropriate permissions!'));
        }

        return new CompanyCollection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $request)
    {
        $token = $request->bearerToken();
        $user = Helper::getUserByToken($token);
        if (empty($user)) {
            Helper::sendError(__('Token is invalid!'));
        }

        if($user->hasPermissionTo('company create')){
            $company_exist = Company::where([['phone', $request->input('phone')], ['user_id', $user->id ]])->first();
            if (!empty($company_exist)) {
                Helper::sendError(__('You entered this phone number to another company'));
            }
            $request->request->add(['user_id' => $user->id]);
            $company = Company::create($request->all());
        } else {
            Helper::sendError(__('You do not have the appropriate permissions!'));
        }

        return new CompanyResource($company);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRequest $request, string $id)
    {
        $token = $request->bearerToken();
        $user = Helper::getUserByToken($token);
        if (empty($user)) {
            Helper::sendError(__('Token is invalid!'));
        }
        $company = Company::find($id);
        if (!$company) {
            Helper::sendError(__('Company not found!'));
        }
        if($user->hasPermissionTo('company edit')){
            if ($user->id != $company->user_id && !$user->hasRole('admin')) {
                Helper::sendError(__('You do not have the appropriate permissions!'));
            }
            $company->update($request->all());
        } else {
            Helper::sendError(__('You do not have the appropriate permissions!'));
        }
        return new CompanyResource($company);
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
        $company = Company::find($id);
        if (empty($company)) {
            Helper::sendError(__('Company not found'));
        }
        if($user->hasPermissionTo('company delete')){
            if ($user->id != $company->user_id && !$user->hasRole('admin')) {
                Helper::sendError(__('You do not have the appropriate permissions!'));
            }
            $company->delete();
        } else {
            Helper::sendError(__('You do not have the appropriate permissions!'));
        }
        return response()->json(['message' => __('Company deleted!')]);
    }
}
