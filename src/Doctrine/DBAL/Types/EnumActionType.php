<?php

namespace Zhortein\SymfonyToolboxBundle\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Zhortein\SymfonyToolboxBundle\Enum\Action;
use Zhortein\SymfonyToolboxBundle\Enum\EnumActionInterface;

class EnumActionType extends Type
{
    public const string NAME = 'zhortein_enum_action_type';

    /**
     * Static property to store detected enum classes.
     *
     * @var string[]
     */
    private static array $enumClasses = [Action::class];

    public static function addEnumClass(string $enumClass): void
    {
        self::$enumClasses[] = $enumClass;
        self::$enumClasses = array_unique(self::$enumClasses);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?EnumActionInterface
    {
        if (null === $value) {
            return null;
        }

        foreach (self::$enumClasses as $enumClass) {
            /** @var \BackedEnum|null $enumValue */
            $enumValue = $enumClass::tryFrom($value);
            if ($enumValue instanceof EnumActionInterface) {
                return $enumValue;
            }
        }

        return null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): int|string|null
    {
        return $value instanceof EnumActionInterface ? $value->value : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
