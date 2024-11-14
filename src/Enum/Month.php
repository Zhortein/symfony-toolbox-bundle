<?php

namespace Zhortein\SymfonyToolboxBundle\Enum;

use Zhortein\SymfonyToolboxBundle\Traits\EnumToArrayTrait;
use Zhortein\SymfonyToolboxBundle\Traits\TranslatableEnumTrait;

enum Month: int implements EnumTranslatableInterface
{
    use EnumToArrayTrait;
    use TranslatableEnumTrait;

    case JANUARY = 1;
    case FEBRUARY = 2;
    case MARCH = 3;
    case APRIL = 4;
    case MAY = 5;
    case JUNE = 6;
    case JULY = 7;
    case AUGUST = 8;
    case SEPTEMBER = 9;
    case OCTOBER = 10;
    case NOVEMBER = 11;
    case DECEMBER = 12;

    public const string TRANSLATION_DOMAIN = 'zhortein_symfony_toolbox-datetime';
}
