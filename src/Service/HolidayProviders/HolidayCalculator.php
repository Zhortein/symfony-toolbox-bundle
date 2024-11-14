<?php

namespace Zhortein\SymfonyToolboxBundle\Service\HolidayProviders;

use Psr\Log\LoggerInterface;

readonly class HolidayCalculator
{
    public function __construct(protected ?LoggerInterface $logger = null)
    {
    }

    /**
     * Calculer les dates des jours fériés basés sur Pâques.
     *
     * @param int $year L'année pour laquelle calculer les jours fériés
     *
     * @return \DateTimeInterface[] Liste des jours fériés
     */
    public function calculateEasterBasedHolidays(int $year): array
    {
        $holidays = [];
        if (function_exists('easter_date')) {
            try {
                $holidays[] = $this->calculateEasterSunday($year);
                $holidays[] = $this->easterMonday($year);
                $holidays[] = $this->ascensionDay($year);
                $holidays[] = $this->pentecostDay($year);
            } catch (\Exception $e) {
                $this->logger?->error($e->getMessage());
            }
        } else {
            // @todo Traduire
            $this->logger?->warning('L\'extension PHP ext_calendar est nécessaire pour calculer les dates de Pâques, Ascension et Pentecôte. Ces dates seront ignorées.', [
                'title' => 'Missing PHP extension',
                'description' => 'The easter_date function is missing. Try to install the php-calendar extension to activate this feature.',
            ]);
        }

        return array_filter($holidays);
    }

    public function newYear(int $year): \DateTime
    {
        return new \DateTime("$year-01-01");
    }

    public function epiphany(int $year): \DateTime
    {
        return new \DateTime("$year-01-06");
    }

    public function labourDay(int $year): \DateTime
    {
        return new \DateTime("$year-05-01");
    }

    public function assumptionOfMary(int $year): \DateTime
    {
        return new \DateTime("$year-08-15");
    }

    public function allSaintsDay(int $year): \DateTime
    {
        return new \DateTime("$year-11-01");
    }

    public function immaculateConception(int $year): \DateTime
    {
        return new \DateTime("$year-12-08");
    }

    public function christmasDay(int $year): \DateTime
    {
        return new \DateTime("$year-12-25");
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function holyThursday(int $year): ?\DateTime
    {
        return $this->calculateEasterSunday($year)?->modify('-3 days');
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function goodFriday(int $year): ?\DateTime
    {
        return $this->calculateEasterSunday($year)?->modify('-2 days');
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function easterMonday(int $year): ?\DateTime
    {
        return $this->calculateEasterSunday($year)?->modify('+1 day');
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function ascensionDay(int $year): ?\DateTime
    {
        return $this->calculateEasterSunday($year)?->modify('+39 days');
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function pentecostDay(int $year): ?\DateTime
    {
        return $this->calculateEasterSunday($year)?->modify('+50 days');
    }

    public function calculateEasterSunday(int $year): ?\DateTime
    {
        $base = new \DateTime("$year-03-21");
        if (function_exists('easter_date')) {
            $days = easter_days($year);

            return $base->add(new \DateInterval("P{$days}D"));
        }

        // @todo Traduire
        $this->logger?->warning('L\'extension PHP ext_calendar est nécessaire pour calculer les dates de Pâques, Ascension et Pentecôte. Ces dates seront ignorées.', [
            'title' => 'Missing PHP extension',
            'description' => 'The easter_date function is missing. Try to install the php-calendar extension to activate this feature.',
        ]);

        return null;
    }
}
