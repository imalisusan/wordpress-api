<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Traits\CanLog;
use Exception;
use Illuminate\Support\Str;
use Stevebauman\Purify\Facades\Purify;

class CraydelHelperFunctions
{
    use CanLog;

    /**
     * ReturnRequirements number
     */
    public static function toNumbers(mixed $rawNumber): float
    {
        return (float) filter_var($rawNumber, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    public static function numberFormat($rawNumber, int $decimals = 0): string
    {
        return number_format($rawNumber, $decimals, '.', ',');
    }

    /**
     * ReturnRequirements email
     */
    public static function toEmailAddress(mixed $rawEmailAddress): ?string
    {
        if (self::isNull($rawEmailAddress)) {
            return null;
        }

        return strtolower(trim($rawEmailAddress));
    }

    /**
     * Check if the value is null
     */
    public static function isNull(mixed $input_to_check): bool
    {
        if (is_object($input_to_check)) {
            return false;
        }

        $input_to_check = self::toCleanString($input_to_check);

        if (strlen($input_to_check) <= 0) {
            return true;
        }

        if ($input_to_check === null) {
            return true;
        }

        if (strcmp('null', (string) $input_to_check) === 0) {
            return true;
        }

        return false;
    }

    /**
     * ReturnRequirements string
     *
     * @param  string|null  $string $string
     */
    public static function toCleanString(mixed $string): ?string
    {
        $string = (string) $string;
        $string = htmlspecialchars_decode($string);
        $string = preg_replace('/\s+/', ' ', $string);
        $string = trim(preg_replace('/\s+/', ' ', $string));
        $string = nl2br($string);
        $string = self::unaccent($string);
        $string = Str::of($string)->replaceMatches('/ {2,}/', ' ');

        return !empty($string) ? (string) $string : null;
    }

    /**
     * Unaccent string
     */
    public static function unaccent(mixed $string): string
    {
        return preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8'));
    }

    /**
     * Decode JSON
     */
    public static function toObjectFromJSON(mixed $string): mixed
    {
        return json_decode($string);
    }

    /**
     * ReturnRequirements email
     */
    public static function makeCleanEmailAddress(string $rawEmailAddress, bool $strict = true): ?string
    {
        $rawEmailAddress = strtolower(trim($rawEmailAddress));

        return !empty($rawEmailAddress) ? $rawEmailAddress : null;
    }

    /**
     * Process name abbreviation
     */
    public static function makeAcronym(?string $name, bool $onlyIncludeFirstNameLastname = false): ?string
    {
        if (empty(trim($name))) {
            return null;
        }

        preg_match('#\((.*?)\)#', $name, $match);

        if (!empty($match[1])) {
            return strtoupper(strtolower($match[1]));
        }

        if (!empty($name)) {
            $name = self::toCleanString($name);
            $name = preg_replace('/\b(\w)|./', '$1', $name);
        }

        if ($onlyIncludeFirstNameLastname) {
            $name = $name[0] . $name[(strlen($name) - 1)];
        }

        return strtoupper(strtolower($name));
    }

    /**
     * Get first name from full names
     */
    public static function makeFirstName(?string $fullNames): ?string
    {
        if (!empty($fullNames)) {
            $_fullNames = explode(' ', self::toCleanString($fullNames));

            return !empty($_fullNames[0]) ? ucfirst($_fullNames[0]) : '';
        }

        return $fullNames;
    }

    /**
     * Get other names from full names
     */
    public static function makeOtherNames(?string $fullNames): ?string
    {
        if (!empty($fullNames)) {
            $fullNames = self::toCleanString($fullNames);

            return call_user_func(function () use ($fullNames) {
                return ucfirst(trim(substr($fullNames, strpos($fullNames, ' '))));
            });
        }

        return $fullNames;
    }

    /**
     * Hide email details
     */
    public static function makeObfuscatedEmailAddress(?string $emailAddress): string
    {
        if (!empty($emailAddress)) {
            if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                $emailAddressSegments = explode('@', $emailAddress);
                $emailAddressSegments[0] = substr($emailAddressSegments[0], 0, 2) . str_repeat('*', strlen($emailAddressSegments[0]) - 2) . substr($emailAddressSegments[0], -1);
                $emailAddressSegments[1] = $emailAddressSegments[1] ?? '';

                return implode('@', $emailAddressSegments);
            } else {
                return substr($emailAddress, 0, -4) . '****';
            }
        } else {
            return '';
        }
    }

    /**
     * Generate random fixed length number
     *
     * @return mixed
     */
    public static function makeRandomNumber(?int $length): int
    {
        if ($length > 16) {
            $length = 16;
        }

        $returnString = mt_rand(1, 9);

        while (strlen((string) $returnString) < $length) {
            $returnString .= mt_rand(0, 9);
        }

        return !empty($returnString) ? (int) $returnString : rand(intval((PHP_INT_MAX / 2)), PHP_INT_MAX);
    }

    /**
     * Generate a random token
     */
    public static function makeRandomString(int $length, string $prefix = null, ?bool $encode = true): string
    {
        $length = min($length, 40);
        if ($prefix !== null) {
            return strtoupper($prefix . Str::random($length));
        } else {
            return strtoupper(Str::random($length));
        }
    }

    /**
     * Get file contents
     *
     * @return mixed
     */
    public static function getFileContent($filePath): string
    {
        if (!empty($filePath) && file_exists($filePath)) {
            return file_get_contents($filePath);
        } else {
            return '';
        }
    }

    /**
     * Convert image size to Mbs
     */
    public static function convertBytesToMBs(float $bytes): float
    {
        $base = log($bytes, 1024);
        $suffixes = ['B', 'K', 'M', 'G', 'T'];
        $type = $suffixes[floor($base)] ?? null;
        if ($type !== null) {
            $value = 0;
            if ($type === 'B') {
                $value = round($bytes / 1024, 4);
            } elseif ($type === 'K') {
                $value = (floatval($bytes) * 0.001) * 0.001;
            } elseif ($type === 'M') {
                $value = (floatval($bytes) * 0.001) * 0.001;
            } elseif ($type === 'G') {
                $value = ((floatval($bytes) * 0.001) * 0.001) * 1000;
            }

            return $value;
        } else {
            return 0;
        }
    }

    /**
     * Get the image aspect ratio
     */
    public static function ratio($width, $height): ?string
    {
        $gcd = function ($a, $b) use (&$gcd) {
            return ($a % $b) ? $gcd($b, $a % $b) : $b;
        };

        $g = $gcd($width, $height);

        return $width / $g . ':' . $height / $g;
    }

    /**
     * Get the image aspect ratio multiplier
     */
    public static function imageAspectRationMultiplier(?float $width, ?float $height): ?float
    {
        $value = function ($width, $height) use (&$value) {
            return ($width % $height) ? $value($height, $width % $height) : $height;
        };

        $g = $value($width, $height);
        $r1 = $width / $g;
        $r2 = $height / $g;
        $arr = [$r1, $r2];

        natcasesort($arr);
        $arr = array_reverse($arr, true);

        return round((floatval($arr[0]) / floatval($arr[1])), 1);
    }

    /**
     * Get the file name from URL
     *
     * @param string|null $url
     * @return ?string
     */
    public static function getFileNameFromURL(?string $url): ?string
    {
        return basename(parse_url($url, PHP_URL_PATH));
    }

    /**
     * Get the file name from URL
     *
     * @param string|null $url
     * @return ?string
     */
    public static function getFileExtensionFromURL(?string $url): ?string
    {
        return pathinfo(self::getFileNameFromURL($url), PATHINFO_EXTENSION);
    }

    /**
     * Clean Array Keys
     */
    public static function cleanArrayKeys(?array $dataArray): ?array
    {
        $updatedDataArray = [];
        foreach ($dataArray as $key => $value) {
            $updatedDataArray[str_replace(' ', '_', strtolower($key))] = $value;
        }

        return $updatedDataArray;
    }

    /**
     * Check if the URL exists
     */
    public static function urlExists($url): bool
    {
        return curl_init($url) !== false;
    }

    /**
     * Is valid details
     */
    public static function isJson(mixed $string): bool
    {
        if(is_array($string)){
            return false;
        }

        if(is_string($string) === false){
            return false;
        }

        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Check if the value is a valid email
     */
    public static function isEmail($input_to_check): bool
    {
        $input_to_check = self::toCleanString($input_to_check);

        if (!filter_var($input_to_check, FILTER_VALIDATE_EMAIL)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if the value is a valid URL
     */
    public static function isURL($input_to_check): bool
    {
        if(self::isNull($input_to_check)){
            return false;
        }

        $exemptedFileExtensions = [
            'svg+xml'
        ];

        $input_to_check = self::toCleanString($input_to_check);
        $extension = pathinfo($input_to_check, PATHINFO_EXTENSION);

        if (!filter_var($input_to_check, FILTER_VALIDATE_URL)) {
            if(in_array($extension, $exemptedFileExtensions)){
                return true;
            }else{
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * Check if the value is a valid date
     */
    public static function isDate(?string $value): bool
    {
        if (CraydelHelperFunctions::isNull($value)) {
            return false;
        }

        $value = self::toCleanString($value);

        if (DateHelper::parse($value)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Create a soft delete name
     */
    public static function createSoftDeleteValue(?string $value, ?int $random_string_length = 5): ?string
    {
        if (self::isNull($value)) {
            return null;
        }

        return $value . '_del' . Str::random($random_string_length);
    }

    /**
     * Pop multiple array elements
     */
    public static function popMultipleElements($array, $keys)
    {
        foreach ($keys as $key) {
            unset($array[$key]);
        }

        return $array;
    }

    /**
     * Parse number to alphabet
     */
    public static function convertNumberToAlphabet(?int $number): ?string
    {
        $number = intval($number);

        if ($number <= 0) {
            return null;
        }

        $alphabet = '';

        while ($number !== 0) {
            $p = ($number - 1) % 26;
            $number = intval(($number - $p) / 26);
            $alphabet = chr(65 + $p) . $alphabet;
        }

        return $alphabet;
    }

    /**
     * Compare 2 arrays
     */
    public static function checkIfArraysMatch(?array $array_one, ?array $array_two): bool
    {
        return count(array_diff($array_one, $array_two)) <= 0;
    }

    /**
     * Replace array keys
     *
     * @throws Exception
     */
    public static function changeMultipleArrayKeys($array, $new_keys)
    {
        $keys = array_keys($array);

        if (count($keys) !== count($new_keys)) {
            throw new Exception('The array keys do not match');
        }

        $final_list = $array;

        for ($i = 0; $i <= (count($new_keys) - 1); $i++) {
            $final_list = self::changeArrayKey($final_list, $keys[$i], self::slugifyString($new_keys[$i], false, [], '_'));
        }

        return $final_list;
    }

    /**
     * Replace array keys
     */
    public static function changeArrayKey($array, $old_key, $new_key)
    {
        if (!array_key_exists($old_key, $array)) {
            return $array;
        }

        $keys = array_keys($array);
        $keys[array_search($old_key, $keys)] = $new_key;

        return array_combine($keys, $array);
    }

    /**
     * Generate a slug given a string
     *
     * @param  string|null  $string $string
     *
     * @throws Exception
     */
    public static function slugifyString(?string $string, ?bool $hash = false, ?array $replace = [], ?string $delimiter = '-'): ?string
    {
        $string = trim($string);

        if (empty($string)) {
            return null;
        }

        if (!extension_loaded('iconv')) {
            throw new Exception('iconv module not loaded');
        }

        $string = urldecode($string);
        $oldLocale = setlocale(LC_ALL, '0');
        setlocale(LC_ALL, 'en_US.UTF-8');
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);

        if (!empty($replace)) {
            $clean = str_replace((array) $replace, ' ', $clean);
        }

        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower($clean);
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
        $clean = trim($clean, $delimiter);
        setlocale(LC_ALL, $oldLocale);

        $clean = trim($clean);
        $clean = preg_replace('/-+/', '-', $clean);

        return $hash ? md5($clean) : $clean;
    }

    /**
     * Capitalize the first letter of the first word
     */
    public static function capitalizeTheFirstLetterOfTheFirstWord(string $sentence = null): ?string
    {
        if (CraydelHelperFunctions::isNull($sentence)) {
            return null;
        }

        $words = explode(' ', $sentence);

        if (!isset($words[0])) {
            return null;
        }

        $wordsWithoutFirstWord = $words;
        unset($wordsWithoutFirstWord[0]);

        if (is_array($wordsWithoutFirstWord) && count($wordsWithoutFirstWord) > 0) {
            return self::toCleanString(ucfirst(strtolower($words[0])) . ' ' . implode(' ', $wordsWithoutFirstWord));
        }

        return self::toCleanString(ucfirst(strtolower($words[0])));
    }

    /**
     * Implode and add and to the last element
     */
    public static function implodeWithAndOnLastElement(array $list): string
    {
        if (count($list) > 1) {
            $list[count($list) - 1] = 'and ' . $list[count($list) - 1];
        }

        return implode(', ', $list);
    }

    /**
     * Flatten an array recursively
    */
    public static function flattenArray(array $array): array
    {
        $return = [];
        array_walk_recursive($array, function ($a) use (&$return) {
            $return[] = $a;
        });

        return $return;
    }

    /**
     * Convert month name to number
    */
    public static function convertMonthNameToNumber(string $month): int
    {
        return (int)date('m',strtotime($month));
    }

    /**
     * Clean HTML string
    */
    public static function cleanHTMLString(string $string): string
    {
        $string = preg_replace('#<a.*?>([^>]*)</a>#i', '$1', $string);
        $string = preg_replace("/<p[^>]*><\\/p[^>]*>/", '', $string);
        $string = str_replace('<p>&nbsp;</p>',"", $string);
        $string = str_replace("&nbsp;"," ", $string);
        $string = str_replace("\n", "", $string);
        return Purify::clean($string);
    }
}
