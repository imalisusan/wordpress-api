<?php

declare(strict_types=1);

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Str;

class DateHelper
{
    /**
     * @var string $timezone
    */
    private static string $timezone = 'Africa/Nairobi';

    /**
     * Current date
     *
     * @returns Carbon
    */
    public static function now(): Carbon
    {
        return Carbon::now(self::$timezone);
    }

    /**
     * Compare dates
    */
    public static function diffInMinutes(string $date_one, string $date_two): float
    {
        return Carbon::parse($date_one, self::$timezone)->diffInMinutes(
            Carbon::parse($date_two, self::$timezone)
        );
    }

    /**
     * Compare dates - get the difference in Hours
     */
    public static function diffInHours(string $date_one, string $date_two): float
    {
        return Carbon::parse($date_one, self::$timezone)->diffInHours(
            Carbon::parse($date_two, self::$timezone)
        );
    }

    public static function diffInDays(string $date_one, string $date_two): float
    {
        return Carbon::parse($date_one, self::$timezone)->diffInDays(
            Carbon::parse($date_two, self::$timezone)
        );
    }


    /**
     * Convert to format datetime
     * @param string|null $date
     * @param string|null $format
     * @return string|null
     */
    public static function makeDisplayDateTime(?string $date, ?string $format = 'd-m-Y H:i:s'): ?string
    {
        return self::parse($date)->format($format);
    }

    /**
     * Make database query datetime
    */
    public static function makeDatabaseQueryDatetime(?string $date, bool $setToEndOfDay = false, ?string $format = 'Y-m-d H:i:s'): ?string
    {
        $newDate = self::parse($date);

        if($setToEndOfDay) {
            $newDate = $newDate->endOfDay();
        }

        return $newDate->format($format);
    }

    /**
     * Extra dates from string
     *
     * @param string|null $dates
     * @return array|null
     */
    public static function extraDates(?string $dates): ?array
    {
        if(CraydelHelperFunctions::isNull($dates)) {
            return [];
        }

        $dates = str_replace(["-"], "/", $dates);
        $dates = str_replace(["~"], "-", $dates);
        $dates = explode('-', $dates);

        $startDate = isset($dates[0]) && !CraydelHelperFunctions::isNull(CraydelHelperFunctions::toCleanString($dates[0])) ? CraydelHelperFunctions::toCleanString($dates[0]) : null;
        $endDate = isset($dates[1]) && !CraydelHelperFunctions::isNull(CraydelHelperFunctions::toCleanString($dates[0])) ? CraydelHelperFunctions::toCleanString($dates[1]) : null;

        if(CraydelHelperFunctions::isNull($startDate) || CraydelHelperFunctions::isNull($endDate)) {
            return [];
        }

        return [
            'start_date' => DateHelper::makeDatabaseQueryDatetime($startDate),
            'end_date' => DateHelper::makeDatabaseQueryDatetime($endDate, true)
        ];
    }

    /**
     * Parse Date
     *
     * @param string $date
     * @return Carbon|null
     */
    public static function parse(string $date): ?Carbon
    {
        $date = str_replace(["/", "~"], "-", $date);

        if(Str::contains($date, 'T')) {
            $rawDate = explode('T', $date);

            if(isset($rawDate[0])) {
                $date = $rawDate[0];
            }

            if(isset($rawDate[1])) {
                $date .= " " . strstr($rawDate[1], "+", true);
            }
        }

        return Carbon::parse($date, self::$timezone);
    }

    /**
     * Check if dates are in the same day
     *
     * @param string $start_date
     * @param string $end_date
     * @return bool
     */
    public static function checkIfDatesAreInTheSameDay(string $start_date, string $end_date): bool
    {
        $start_date = self::parse($start_date);
        $end_date = self::parse($end_date);

        return $start_date->diffInDays($end_date) <= 0;
    }

    /**
     * Check if dates are in the same week
     *
     * @param string $start_date
     * @param string $end_date
     * @return bool
     */
    public static function checkIfDatesAreInTheSameWeek(string $start_date, string $end_date): bool
    {
        $start_date = self::parse($start_date);
        $end_date = self::parse($end_date);

        return $start_date->diffInWeeks($end_date) <= 0;
    }

    /**
     * Check if dates are in the same month
     *
     * @param string $start_date
     * @param string $end_date
     * @return bool
     */
    public static function checkIfDatesAreInTheSameMonth(string $start_date, string $end_date): bool
    {
        $start_date = self::parse($start_date);
        $end_date = self::parse($end_date);

        return $start_date->diffInMonths($end_date) <= 0;
    }

    /**
     * Check if dates are in the same Year
     *
     * @param string $start_date
     * @param string $end_date
     * @return bool
     */
    public static function checkIfDatesAreInTheSameYear(string $start_date, string $end_date): bool
    {
        $start_date = self::parse($start_date);
        $end_date = self::parse($end_date);

        return $start_date->diffInYears($end_date) <= 0;
    }

    /**
     * Check if dates are in different years
     *
     * @param string $start_date
     * @param string $end_date
     * @return bool
     */
    public static function checkIfDatesAreInDifferentYears(string $start_date, string $end_date): bool
    {
        $start_date = self::parse($start_date);
        $end_date = self::parse($end_date);

        return $start_date->diffInYears($end_date) > 0;
    }
}
