<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Traits\CanCache;
use App\Traits\CanRespond;
use Illuminate\Support\Facades\Http;

class CountryServiceHelper
{
    use CanCache;
    use CanRespond;

    /**
     * @const COUNTRIES_WHERE_CRAYDEL_HAS_INSTITUTIONS
    */
    public const COUNTRIES_WHERE_CRAYDEL_HAS_INSTITUTIONS = 'COUNTRIES_WHERE_CRAYDEL_HAS_INSTITUTIONS';

    /**
     * Get the list of all countries
    */
    public static function getCountries()
    {
        $countries = self::cache(self::COUNTRIES_WHERE_CRAYDEL_HAS_INSTITUTIONS);

        if(null === $countries) {
            $countries = Http::post(config('services.craydel.countries.get_countries_list'))->json('data.countries');
        }

        return $countries;
    }
}
