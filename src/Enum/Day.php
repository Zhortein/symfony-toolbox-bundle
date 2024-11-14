<?php

namespace Zhortein\SymfonyToolboxBundle\Enum;

use Zhortein\SymfonyToolboxBundle\Traits\EnumToArrayTrait;
use Zhortein\SymfonyToolboxBundle\Traits\TranslatableEnumTrait;

enum Day: int implements EnumTranslatableInterface
{
    use EnumToArrayTrait;
    use TranslatableEnumTrait;

    case MONDAY = 1;
    case TUESDAY = 2;
    case WEDNESDAY = 3;
    case THURSDAY = 4;
    case FRIDAY = 5;
    case SATURDAY = 6;
    case SUNDAY = 7;

    public const string TRANSLATION_DOMAIN = 'zhortein_symfony_toolbox-datetime';
}
