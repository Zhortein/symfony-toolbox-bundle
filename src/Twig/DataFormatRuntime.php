<?php

namespace Zhortein\SymfonyToolboxBundle\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

readonly class DataFormatRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function formatDurationIso(?string $duration, bool $shortFormat = true): string
    {
        if (null === $duration) {
            return ' ';
        }

        try {
            $interval = new \DateInterval($duration);
        } catch (\Exception) {
            return $this->translator->trans('InvalidFormat', [], 'zhortein_symfony_toolbox-datetime');
        }

        $formatted = '';

        if ($interval->y) {
            $formatted .= $interval->y.' '.$this->translator->trans($shortFormat ? 'YearShort' : 'Year', ['count' => $interval->y], 'zhortein_symfony_toolbox-datetime').' ';
        }
        if ($interval->m) {
            $formatted .= $interval->m.' '.$this->translator->trans($shortFormat ? 'MonthShort' : 'Month', ['count' => $interval->m], 'zhortein_symfony_toolbox-datetime').' ';
        }
        if ($interval->d) {
            $formatted .= $interval->d.' '.$this->translator->trans($shortFormat ? 'DayShort' : 'Day', ['count' => $interval->d], 'zhortein_symfony_toolbox-datetime').' ';
        }
        if ($interval->h) {
            $formatted .= $interval->h.' '.$this->translator->trans($shortFormat ? 'HourShort' : 'Hour', ['count' => $interval->h], 'zhortein_symfony_toolbox-datetime').' ';
        }
        if ($interval->i) {
            $formatted .= $interval->i.' '.$this->translator->trans($shortFormat ? 'MinuteShort' : 'Minute', ['count' => $interval->i], 'zhortein_symfony_toolbox-datetime').' ';
        }
        if ($interval->s) {
            $formatted .= $interval->s.' '.$this->translator->trans($shortFormat ? 'SecondShort' : 'Second', ['count' => $interval->s], 'zhortein_symfony_toolbox-datetime').' ';
        }

        return trim($formatted);
    }

    public function formatDurationSeconds(?float $duration, bool $shortFormat = true): string
    {
        if (null === $duration) {
            return '';
        }

        $hours = floor($duration / 3600);
        $minutes = floor(($duration / 60) % 60);
        $seconds = fmod($duration, 60);

        $formatted = '';

        if ($hours) {
            $formatted .= $hours.' '.$this->translator->trans($shortFormat ? 'HourShort' : 'Hour', ['count' => $hours], 'zhortein_symfony_toolbox-datetime').' ';
        }
        if ($minutes) {
            $formatted .= $minutes.' '.$this->translator->trans($shortFormat ? 'MinuteShort' : 'Minute', ['count' => $minutes], 'zhortein_symfony_toolbox-datetime').' ';
        }
        if ($seconds) {
            $formatted .= number_format($seconds, 3).' '.$this->translator->trans($shortFormat ? 'SecondShort' : 'Second', ['count' => $seconds], 'zhortein_symfony_toolbox-datetime').' ';
        }

        return trim($formatted);
    }
}
