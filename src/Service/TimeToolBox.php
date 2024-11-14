<?php

namespace Zhortein\SymfonyToolboxBundle\Service;

use Zhortein\SymfonyToolboxBundle\Exception\UnsupportedTimeUnitException;

class TimeToolBox
{
    private const int SECONDS_IN_MINUTE = 60;
    private const int SECONDS_IN_HOUR = 3600;
    private const int SECONDS_IN_DAY = 86400;
    private const int SECONDS_IN_MONTH = 2592000; // approx
    private const int SECONDS_IN_YEAR = 31536000; // approx

    /**
     * Retourne l'heure actuelle en millisecondes.
     */
    public static function getCurrentMicrotime(): float
    {
        return microtime(true);
    }

    /**
     * Retourne les dates de début et de fin pour une semaine donnée.
     *
     * @return array<string, string>
     *
     * @throws \InvalidArgumentException|\DateMalformedStringException
     */
    public static function getWeekStartAndEnd(?int $year = null, ?int $week = null, string $format = 'Y-m-d'): array
    {
        $year = $year ?? (int) date('Y');
        $week = $week ?? (int) date('W');

        if ($week < 1 || $week > 53) {
            // @todo Traduire
            throw new \InvalidArgumentException('La semaine doit être comprise entre 1 et 53.');
        }

        $date = new \DateTime();
        $date->setISODate($year, $week);

        $start = $date->format($format);
        $end = $date->modify('+6 days')->format($format);

        return ['start' => $start, 'end' => $end];
    }

    /**
     * Convertit un `DateInterval` en nombre de secondes.
     */
    public static function dateIntervalToSeconds(?\DateInterval $dateInterval): int
    {
        if (null === $dateInterval) {
            return 0;
        }

        return $dateInterval->s +
            $dateInterval->i * self::SECONDS_IN_MINUTE +
            $dateInterval->h * self::SECONDS_IN_HOUR +
            $dateInterval->d * self::SECONDS_IN_DAY +
            $dateInterval->m * self::SECONDS_IN_MONTH +
            $dateInterval->y * self::SECONDS_IN_YEAR;
    }

    /**
     * Divise un `DateInterval` par un autre et renvoie le quotient en tant que float.
     * Retourne `null` si le dénominateur est zéro.
     */
    public static function dateIntervalDivide(\DateInterval $numerator, \DateInterval $denominator): ?float
    {
        $denominatorSeconds = self::dateIntervalToSeconds($denominator);

        return 0 !== $denominatorSeconds ? self::dateIntervalToSeconds($numerator) / $denominatorSeconds : null;
    }

    /**
     * Additionne plusieurs `DateInterval`.
     */
    public static function dateIntervalAdd(?\DateInterval ...$intervals): ?\DateInterval
    {
        $totalSeconds = array_reduce(
            $intervals,
            fn ($carry, $interval) => $carry + ($interval ? self::dateIntervalToSeconds($interval) : 0),
            0
        );

        $zeroDate = new \DateTimeImmutable();

        return $zeroDate->diff($zeroDate->add(new \DateInterval("PT{$totalSeconds}S")));
    }

    /**
     * Soustrait plusieurs `DateInterval`.
     */
    public static function dateIntervalSub(?\DateInterval ...$intervals): ?\DateInterval
    {
        $totalSeconds = null;

        foreach ($intervals as $interval) {
            if (null !== $interval) {
                $intervalSeconds = self::dateIntervalToSeconds($interval);
                $totalSeconds = null === $totalSeconds ? $intervalSeconds : $totalSeconds - $intervalSeconds;
            }
        }

        if (null === $totalSeconds) {
            return null;
        }

        $isNegative = $totalSeconds < 0;
        $totalSeconds = abs($totalSeconds);

        $zeroDate = new \DateTimeImmutable();
        $endDate = $zeroDate->add(new \DateInterval("PT{$totalSeconds}S"));
        $interval = $zeroDate->diff($endDate);

        if ($isNegative) {
            $interval->invert = 1;
        }

        return $interval;
    }

    /**
     * Convertit un `DateInterval` en format ISO8601.
     */
    public static function dateIntervalToIso8601(?\DateInterval $interval): string
    {
        if (null === $interval) {
            $interval = new \DateInterval('PT0S');
        }
        $result = 'P';

        if ($interval->y) {
            $result .= $interval->y.'Y';
        }
        if ($interval->m) {
            $result .= $interval->m.'M';
        }
        if ($interval->d) {
            $result .= $interval->d.'D';
        }

        if ($interval->h || $interval->i || $interval->s) {
            $result .= 'T';
            if ($interval->h) {
                $result .= $interval->h.'H';
            }
            if ($interval->i) {
                $result .= $interval->i.'M';
            }
            if ($interval->s) {
                $result .= $interval->s.'S';
            }
        }

        return 'P' === $result ? 'PT0S' : $result;
    }

    /**
     * Normalise une durée au format ISO8601.
     */
    public static function normalizeISO8601Duration(?string $duration): string
    {
        try {
            $interval = new \DateInterval($duration ?? 'PT0S');
        } catch (\Exception) {
            return 'PT0S';
        }

        $totalSeconds = self::dateIntervalToSeconds($interval);

        $years = intdiv($totalSeconds, self::SECONDS_IN_YEAR);
        $totalSeconds %= self::SECONDS_IN_YEAR;
        $months = intdiv($totalSeconds, self::SECONDS_IN_MONTH);
        $totalSeconds %= self::SECONDS_IN_MONTH;
        $days = intdiv($totalSeconds, self::SECONDS_IN_DAY);
        $totalSeconds %= self::SECONDS_IN_DAY;
        $hours = intdiv($totalSeconds, self::SECONDS_IN_HOUR);
        $totalSeconds %= self::SECONDS_IN_HOUR;
        $minutes = intdiv($totalSeconds, self::SECONDS_IN_MINUTE);
        $seconds = $totalSeconds % self::SECONDS_IN_MINUTE;

        $iso8601Duration = 'P';
        if ($years) {
            $iso8601Duration .= $years.'Y';
        }
        if ($months) {
            $iso8601Duration .= $months.'M';
        }
        if ($days) {
            $iso8601Duration .= $days.'D';
        }
        if ($hours || $minutes || $seconds) {
            $iso8601Duration .= 'T';
            if ($hours) {
                $iso8601Duration .= $hours.'H';
            }
            if ($minutes) {
                $iso8601Duration .= $minutes.'M';
            }
            if ($seconds) {
                $iso8601Duration .= $seconds.'S';
            }
        }

        return 'P' === $iso8601Duration ? 'PT0S' : $iso8601Duration;
    }

    /**
     * Vérifie si une durée est au format ISO8601.
     */
    public static function isISO8601Duration(string $duration): bool
    {
        try {
            new \DateInterval($duration);

            return true;
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Convertit un `DateInterval` en une unité de temps spécifique.
     */
    public static function convertDateInterval(\DateInterval $interval, string $unit, float $hoursPerDay = 24): float
    {
        $days = $interval->d;

        $totalHours = ($days * $hoursPerDay) + $interval->h + ($interval->i / 60) + ($interval->s / 3600);
        $totalMinutes = ($totalHours * 60) + $interval->i + ($interval->s / 60);
        $totalSeconds = ($totalMinutes * 60) + $interval->s;

        return match (strtolower($unit)) {
            'seconds' => $totalSeconds,
            'minutes' => $totalMinutes,
            'hours' => $totalHours,
            'days' => $days + ($interval->h / $hoursPerDay) + ($interval->i / ($hoursPerDay * 60)) + ($interval->s / ($hoursPerDay * 3600)),
            default => throw new UnsupportedTimeUnitException('L\'unité doit être une de : seconds, minutes, hours, days'), // @todo Traduire
        };
    }
}
