<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;
use App\Models\Company;

class CompanyObserver
{
    /**
     * Handle the Company "created" event.
     */
    public function created(Company $company): void
    {
        Cache::forget('companies');
        Cache::forget('companies_'.$company->user_id);
    }

    /**
     * Handle the Company "updated" event.
     */
    public function updated(Company $company): void
    {
        Cache::forget('companies');
        Cache::forget('companies_'.$company->user_id);
    }

    /**
     * Handle the Company "deleted" event.
     */
    public function deleted(Company $company): void
    {
        Cache::forget('companies');
        Cache::forget('companies_'.$company->user_id);
    }

    /**
     * Handle the Company "restored" event.
     */
    public function restored(Company $company): void
    {
        Cache::forget('companies');
        Cache::forget('companies_'.$company->user_id);
    }

    /**
     * Handle the Company "force deleted" event.
     */
    public function forceDeleted(Company $company): void
    {
        Cache::forget('companies');
        Cache::forget('companies_'.$company->user_id);
    }
}
