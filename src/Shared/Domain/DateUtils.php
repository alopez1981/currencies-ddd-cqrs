<?php

declare(strict_types=1);

namespace Hoyvoy\Shared\Domain;

use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use function Lambdish\Phunctional\map;

final class DateUtils
{
    public const ISO8601Z             = 'Y-m-d\TH:i:s.v\Z';
    public const RFC3339_MICROSECONDS = 'Y-m-d\TH:i:s.uP';

    public const NONE = 0;
    public const BOTH = 1;
    public const FROM = 2;
    public const TO   = 3;

    /** * @SuppressWarnings(PHPMD.CyclomaticComplexity) */
    public static function dateIsInBetween(
        DateTimeImmutable|DateTime|null $from,
        DateTimeImmutable|DateTime|null $to,
        DateTimeImmutable|DateTime $subject,
        int $equalMatch = self::BOTH,
    ): bool {
        return match ($equalMatch) {
            self::BOTH => (null === $from || $subject->getTimestamp() >= $from->getTimestamp()) &&
                (null === $to || $subject->getTimestamp() <= $to->getTimestamp()),
            self::FROM => (null === $from || $subject->getTimestamp() >= $from->getTimestamp()) &&
                (null === $to || $subject->getTimestamp() < $to->getTimestamp()),
            self::TO => (null === $from || $subject->getTimestamp() > $from->getTimestamp()) &&
                (null === $to || $subject->getTimestamp() <= $to->getTimestamp()),
            default => (null === $from || $subject->getTimestamp() > $from->getTimestamp()) &&
                (null === $to || $subject->getTimestamp() < $to->getTimestamp()),
        };
    }

    public static function datePeriod(
        DateTimeImmutable|DateTime $begin,
        DateTimeImmutable|DateTime $end,
        string $interval = 'P1D',
    ): DatePeriod {
        return new DatePeriod($begin, new DateInterval($interval), $end);
    }

    public static function dateToString(
        DateTimeImmutable|DateTime|string $date,
        string $format = self::RFC3339_MICROSECONDS,
        string $timezone = null,
    ): string {
        if (is_string($date)) {
            $date = self::stringToDate($date, timezone: $timezone);
        }

        if (null !== $timezone) {
            $date = $date->setTimezone(new DateTimeZone($timezone));
        }

        return $date->format($format);
    }

    /** @SuppressWarnings(PHPMD.BooleanArgumentFlag) */
    public static function stringToDate(
        string $date,
        bool $immutable = false,
        string $timezone = null
    ): DateTimeImmutable|DateTime {
        $timezone = null !== $timezone ? new DateTimeZone($timezone) : null;

        $datetime = $immutable ?
            new DateTimeImmutable($date, $timezone) :
            new DateTime($date, $timezone);

        if (null !== $timezone) {
            $datetime->setTimezone($timezone);
        }

        return $datetime;
    }

    public static function nowString(string $format = self::RFC3339_MICROSECONDS): string
    {
        return (new DateTime('now'))->format($format);
    }

    public static function addBusinessDays(
        DateTimeImmutable|DateTime|string $date,
        string $timezone = null,
        int $businessDays,
        DateTimeImmutable|DateTime|string ...$skipDays,
    ): DateTimeImmutable|DateTime {
        if (is_string($date)) {
            $date = self::stringToDate($date, timezone: $timezone);
        }

        $skipDays = map(function (DateTimeImmutable|DateTime|string $skipDay) {
            return self::dateToString($skipDay, 'Y-m-d');
        }, $skipDays);

        while ($businessDays > 0) {
            if ($date->format('N') < 6 && !in_array($date->format('Y-m-d'), $skipDays)) {
                $businessDays--;
            }

            $date = $date->modify('+ 1 day');
        }

        return $date;
    }

    public static function calendar(int $year): array
    {
        $dates = [];

        $days = date("L", mktime(0, 0, 0, 7, 7, $year) ?: null) ? 366 : 365;

        for ($i = 1; $i <= $days; $i++) {
            $month      = date('m', mktime(0, 0, 0, 1, $i, $year) ?: null);
            $weekNumber = date('W', mktime(0, 0, 0, 1, $i, $year) ?: null);
            $weekDay    = date('D', mktime(0, 0, 0, 1, $i, $year) ?: null);
            $day        = date('d', mktime(0, 0, 0, 1, $i, $year) ?: null);

            $dates[$month][$weekNumber][$weekDay] = $day;
        }

        return $dates;
    }
}
