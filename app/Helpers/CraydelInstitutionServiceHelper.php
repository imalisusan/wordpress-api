<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Traits\CanCache;
use App\Traits\CanRespond;
use Exception;
use Illuminate\Support\Facades\Http;

class CraydelInstitutionServiceHelper
{
    use CanCache;
    use CanRespond;

    protected static string $cacheKey = '%s_DETAILS_CACHE_KEY';

    /**
     * Get institution details
    */
    public static function get(string $institutionCode)
    {
        $result = Http::get(sprintf(config('services.craydel.institution.get_institution_details'), $institutionCode))->object();
        return $result->data ?? null;
    }

    /**
     * Get institution campuses summary details
     * @throws Exception
     */
    public static function getCampusesSummaryDetails(array $campusCodes)
    {
        $codes = collect($campusCodes)
            ->reject(function ($code){
                return CraydelHelperFunctions::isNull($code);
            })->unique()
            ->sort()
            ->toArray();

        if(count($campusCodes) <= 0){
            return [];
        }

        return Http::post(config('services.craydel.institution.get_institution_campuses_details'), [
            'campus_codes' => implode(',', $codes)
        ])->json('data');
    }
}
