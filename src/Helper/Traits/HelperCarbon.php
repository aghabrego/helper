<?php

namespace Weirdo\Helper\Traits;

use DateTimeZone;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;

/**
 * @license MIT
 * @package Weirdo\Helper
 */
trait HelperCarbon
{
    /**
     * @param string $date
     * @param string $format
     * @param string $timezone
     * @return bool
     */
    public function validateDate($date, $format = 'Y-m-d H:i:s', $timezone = 'America/Panama')
    {
        // set the default timezone to use.
        date_default_timezone_set($timezone);

        try {
            /** @var DateTimeZone */
            $dateTimeZone = new DateTimeZone($timezone);
            /** @var string */
            $parse = Carbon::parse($date, $dateTimeZone)->format($format);

            return strtotime($parse) > 0 ? true : false;
        } catch (InvalidFormatException $e) {
            return false;
        }
    }

    /**
     * @param string|Carbon $date
     * @param string $format
     * @param string $timezone
     * @return string|null
     */
    public function getOriginalDateFormat($date = '0000-00-00', $format = 'Y-m-d', $timezone = 'America/Panama')
    {
        // set the default timezone to use.
        date_default_timezone_set($timezone);

        if (is_null($date)) {
            return null;
        }

        if ($this->validateDate($date, $format, $timezone) === false) {
            return null;
        }

        /** @var DateTimeZone */
        $dateTimeZone = new DateTimeZone($timezone);

        if ($date instanceof Carbon) {
            $date->timezone($dateTimeZone);

            return $date->format($format);
        }

        /** @var Carbon */
        $carbon = new Carbon($date);
        $carbon->timezone($dateTimeZone);

        return $carbon->format($format);
    }

    /**
     * @param string $date
     * @param string $format
     * @param string $timezone
     * @return Carbon
     */
    public function createCarbonFormat($date, $format = 'Y-m-d', $timezone = 'America/Panama')
    {
        /** @var string|null */
        $newFormat = $this->getOriginalDateFormat($date, $format, $timezone);
        /** @var DateTimeZone */
        $dateTimeZone = new DateTimeZone($timezone);
        $carbon = new Carbon($newFormat);
        $carbon->timezone($dateTimeZone);

        return $carbon;
    }

    /**
     * @param string $date
     * @param string $format
     * @param string|null $originalFormat
     * @param string $timezone
     * @return string
     */
    public function getSpecificDateFormat($date = '0000-00-00', $format = 'DD-MMM-YYYY h:mm:ss', $originalFormat = 'Y-m-d H:i:s', $timezone = 'America/Panama')
    {
        // set the default timezone to use.
        date_default_timezone_set($timezone);

        if ($this->validateDate($date, $originalFormat, $timezone) === false) {
            return null;
        }

        /** @var DateTimeZone */
        $dateTimeZone = new DateTimeZone($timezone);
        /** @var Carbon */
        $carbon = Carbon::parse($date)->locale('es_PA.utf8');
        $carbon->timezone($dateTimeZone);

        return $carbon->isoFormat($format, $originalFormat);
    }

    /**
     * @param string $dateA
     * @param string $dateB
     * @param string $format
     * @return int
     */
    public function differencesInDays($dateA, $dateB, $format = 'Y-m-d')
    {
        $cDateA = $this->createCarbonFormat($dateA, $format);
        $cDateB = $this->createCarbonFormat($dateB, $format);

        return $cDateA->diffInDays($cDateB);
    }

    /**
     * @param string $dateA
     * @param string $dateB
     * @param string $format
     * @return int
     */
    public function differencesInHours($dateA, $dateB, $format = 'Y-m-d H:i:s')
    {
        $cDateA = $this->createCarbonFormat($dateA, $format);
        $cDateB = $this->createCarbonFormat($dateB, $format);

        return $cDateA->diffInHours($cDateB);
    }

    /**
     * @param string $dateA
     * @param string $dateB
     * @param string $format
     * @return int
     */
    public function differencesInSeconds($dateA, $dateB, $format = 'Y-m-d H:i:s')
    {
        $cDateA = $this->createCarbonFormat($dateA, $format);
        $cDateB = $this->createCarbonFormat($dateB, $format);

        return $cDateA->diffInSeconds($cDateB);
    }

    /**
     * @param string $dateA
     * @param string $dateB
     * @param string $format
     * @return boolean
     */
    public function greaterThan($dateA, $dateB, $format = 'Y-m-d')
    {
        $cDateA = $this->createCarbonFormat($dateA, $format);
        $cDateB = $this->createCarbonFormat($dateB, $format);

        return $cDateA->gt($cDateB);
    }

    /**
     * @param string $dateA
     * @param string $dateB
     * @param string $format
     * @return boolean
     */
    public function equals($dateA, $dateB, $format = 'Y-m-d')
    {
        $cDateA = $this->createCarbonFormat($dateA, $format);
        $cDateB = $this->createCarbonFormat($dateB, $format);

        return $cDateA->eq($cDateB);
    }

    /**
     * @param string $dateA
     * @param string $dateB
     * @param string $format
     * @return boolean
     */
    public function notEquals($dateA, $dateB, $format = 'Y-m-d')
    {
        $cDateA = $this->createCarbonFormat($dateA, $format);
        $cDateB = $this->createCarbonFormat($dateB, $format);

        return $cDateA->ne($cDateB);
    }

    /**
     * @param string $dateA
     * @param string $dateB
     * @param string $format
     * @return boolean
     */
    public function greaterThanOrEquals($dateA, $dateB, $format = 'Y-m-d')
    {
        $cDateA = $this->createCarbonFormat($dateA, $format);
        $cDateB = $this->createCarbonFormat($dateB, $format);

        return $cDateA->gte($cDateB);
    }

    /**
     * @param string $dateA
     * @param string $dateB
     * @param string $format
     * @return boolean
     */
    public function lessThan($dateA, $dateB, $format = 'Y-m-d')
    {
        $cDateA = $this->createCarbonFormat($dateA, $format);
        $cDateB = $this->createCarbonFormat($dateB, $format);

        return $cDateA->lt($cDateB);
    }

    /**
     * @param string $dateA
     * @param string $dateB
     * @param string $format
     * @return boolean
     */
    public function lessThanOrEquals($dateA, $dateB, $format = 'Y-m-d')
    {
        $cDateA = $this->createCarbonFormat($dateA, $format);
        $cDateB = $this->createCarbonFormat($dateB, $format);

        return $cDateA->lte($cDateB);
    }

    /**
     * @param string $dateA
     * @param string $dateB
     * @param string $format
     * @return int
     */
    public function differencesInMinutes($dateA, $dateB, $format = 'Y-m-d H:i:s')
    {
        $cDateA = $this->createCarbonFormat($dateA, $format);
        $cDateB = $this->createCarbonFormat($dateB, $format);

        return $cDateA->diffInMinutes($cDateB);
    }

    /**
     * @param string $dateA
     * @param string $dateB
     * @param string $format
     * @return int
     */
    public function differencesInMonths($dateA, $dateB, $format = 'Y-m-d H:i:s')
    {
        $cDateA = $this->createCarbonFormat($dateA, $format);
        $cDateB = $this->createCarbonFormat($dateB, $format);

        return $cDateA->diffInMonths($cDateB);
    }

    /**
     * @param string|Carbon $date
     * @param string $format
     * @return Carbon
     */
    public function getYesterday($date, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->subDay();
    }

    /**
     * @param string|Carbon $date
     * @param int $value
     * @param string $format
     * @return Carbon
     */
    public function addCenturies($date, $value = 1, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->addCenturies($value);
    }

    /**
     * @param string|Carbon $date
     * @param int $value
     * @param string $format
     * @return Carbon
     */
    public function addYears($date, $value = 1, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->addYears($value);
    }

    /**
     * @param string|Carbon $date
     * @param int $value
     * @param string $format
     * @return Carbon
     */
    public function addQuarters($date, $value = 1, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->addQuarters($value);
    }

    /**
     * @param string|Carbon $date
     * @param int $value
     * @param string $format
     * @return Carbon
     */
    public function addMonths($date, $value = 1, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->addMonths($value);
    }

    /**
     * @param string|Carbon $date
     * @param int $value
     * @param string $format
     * @return Carbon
     */
    public function addDays($date, $value = 1, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->addDays($value);
    }

    /**
     * @param string|Carbon $date
     * @param int $value
     * @param string $format
     * @return Carbon
     */
    public function addWeekdays($date, $value = 1, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->addWeekdays($value);
    }

    /**
     * @param string|Carbon $date
     * @param int $value
     * @param string $format
     * @return Carbon
     */
    public function addWeeks($date, $value = 1, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->addWeeks($value);
    }

    /**
     * @param string|Carbon $date
     * @param int $value
     * @param string $format
     * @return Carbon
     */
    public function addHours($date, $value = 1, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->addHours($value);
    }

    /**
     * @param string|Carbon $date
     * @param int $value
     * @param string $format
     * @return Carbon
     */
    public function addMinutes($date, $value = 1, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->addMinutes($value);
    }

    /**
     * @param string|Carbon $date
     * @param int $value
     * @param string $format
     * @return Carbon
     */
    public function addSeconds($date, $value = 1, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->addSeconds($value);
    }

    /**
     * @param string|Carbon $date
     * @param int $value
     * @param string $format
     * @return Carbon
     */
    public function addMilliseconds($date, $value = 1, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->addMilliseconds($value);
    }

    /**
     * @param string|Carbon $date
     * @param int $value
     * @param string $format
     * @return Carbon
     */
    public function subCenturies($date, $value = 1, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->subCenturies($value);
    }

    /**
     * @param string|Carbon $date
     * @param int $value
     * @param string $format
     * @return Carbon
     */
    public function subYears($date, $value = 1, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->subYears($value);
    }

    /**
     * @param string|Carbon $date
     * @param int $value
     * @param string $format
     * @return Carbon
     */
    public function subQuarters($date, $value = 1, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->subQuarters($value);
    }

    /**
     * @param string|Carbon $date
     * @param int $value
     * @param string $format
     * @return Carbon
     */
    public function subMonths($date, $value = 1, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->subMonths($value);
    }

    /**
     * @param string|Carbon $date
     * @param int $value
     * @param string $format
     * @return Carbon
     */
    public function subDays($date, $value = 1, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->subDays($value);
    }

    /**
     * @param string|Carbon $date
     * @param int $value
     * @param string $format
     * @return Carbon
     */
    public function subWeekdays($date, $value = 1, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->subWeekdays($value);
    }

    /**
     * @param string|Carbon $date
     * @param int $value
     * @param string $format
     * @return Carbon
     */
    public function subWeeks($date, $value = 1, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->subWeeks($value);
    }

    /**
     * @param string|Carbon $date
     * @param int $value
     * @param string $format
     * @return Carbon
     */
    public function subMinutes($date, $value = 1, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->subMinutes($value);
    }

    /**
     * @param string|Carbon $date
     * @param int $value
     * @param string $format
     * @return Carbon
     */
    public function subSeconds($date, $value = 1, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->subSeconds($value);
    }

    /**
     * @param string|Carbon $date
     * @param int $value
     * @param string $format
     * @return Carbon
     */
    public function subMillisecond($date, $value = 1, $format = 'Y-m-d H:i:s')
    {
        $cDate = $this->createCarbonFormat($date, $format);

        return $cDate->subMillisecond($value);
    }

    /**
     * @param string|Carbon $date
     * @param string $format
     * @return string
     */
    public function getDayWeek($date, string $format = 'Y-m-d H:i:s', string $timezone = 'America/Panama')
    {
        /** @var string $date */
        $date = $this->getOriginalDateFormat($date, $format, $timezone);
        /** @var string $day */
        $day = $this->getSpecificDateFormat($date, 'dd', $format, $timezone);

        return $this->cleanSpecialCharacters(strtolower($day));
    }

    /**
     * @param int $time
     * @return int
     */
    public function getWorkdayTime(int $time)
    {
        $horaInicioLaboral = 8;
        $horaFinLaboral = 18;
        /** @var \Carbon\Carbon $cDate */
        $cDate = $this->createCarbonFormat(now(), 'Y-m-d H:i:s');
        /** @var \Carbon\Carbon $nextDate */
        $nextDate = $cDate->addSeconds($time);
        /** @var int $dayWeek */
        $dayWeek = $nextDate->getDaysFromStartOfWeek();
        /** @var array $dayOfTheWeeks */
        $dayOfTheWeeks = [1, 2, 3, 4, 5];
        if (in_array($dayWeek, $dayOfTheWeeks, true)) {
            /** @var int $hour */
            $hour = $this->filterVar($nextDate->isoFormat('H'), FILTER_VALIDATE_INT);
            if ($hour >= $horaInicioLaboral && $hour <= $horaFinLaboral) {
                return $time;
            } else {
                /** @var null|int $hoursUntilStartWork */
                $hoursUntilStartWork = null;
                if ($hour < $horaInicioLaboral) {
                    $hoursUntilStartWork = $horaInicioLaboral - $hour;
                } else {
                    $hoursUntilStartWork = 24 - $hour + $horaInicioLaboral;
                }
                /** @var int $seconds */
                $seconds = $hoursUntilStartWork * 3600;
                /** @var int $nextSeconds */
                $nextSeconds = $time + $seconds;

                return $nextSeconds;
            }
        } else {
            /** @var null|int $diffSeconds */
            $diffSeconds = null;
            if ($dayWeek === 6) { // Saturday
                $diffSeconds = 2 * 86400;
            } elseif ($dayWeek === 0) { // Sunday
                $diffSeconds = 1 * 86400;
            }
            $nextDate->setSeconds($diffSeconds)->setTime($horaInicioLaboral, 0);
            $nextSeconds = $nextDate->diffInSeconds(now());

            return $nextSeconds;
        }
    }
}
