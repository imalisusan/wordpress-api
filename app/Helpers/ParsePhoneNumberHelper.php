<?php

declare(strict_types=1);

namespace App\Helpers;

use Propaganistas\LaravelPhone\Exceptions\CountryCodeException;
use Propaganistas\LaravelPhone\PhoneNumber;

class ParsePhoneNumberHelper
{
    /**
     * Make country phone number
     *
     * @param string $countryCode
     * @param string $mobileNumber
     * @param bool $skipPlusSign
     * @return string|null
     */
    public static function makeNationalizedMobileNumber(string $countryCode, string $mobileNumber, bool $skipPlusSign = true): ?string
    {
        $countryCode = strtoupper(trim($countryCode));
        $formattedMobileNumber = (new PhoneNumber($mobileNumber, $countryCode))->formatE164();

        return !$skipPlusSign ? $formattedMobileNumber : strval(CraydelHelperFunctions::toNumbers($formattedMobileNumber));
    }

    /**
     * Remove country code
     *
     * @param string $countryCode
     * @param string $mobileNumber
     * @return string|null
     * @throws CountryCodeException
     */
    public static function makeLocalNumber(string $countryCode, string $mobileNumber): ?string
    {
        $countryCode = strtoupper(trim($countryCode));
        $formattedMobileNumber = (new PhoneNumber($mobileNumber, $countryCode))->formatForCountry($countryCode);

        return strval(CraydelHelperFunctions::toNumbers($formattedMobileNumber));
    }
}
