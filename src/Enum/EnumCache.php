<?php

namespace Zhortein\SymfonyToolboxBundle\Enum;

use Symfony\Contracts\Translation\TranslatorInterface;

class EnumCache
{
    /**
     * @var array<string, string>
     */
    private static array $cache = [];

    public static function getLabel(string $enumClass, string $caseName, ?TranslatorInterface $translator = null, string $translationDomain = 'default', ?string $locale = null): string
    {
        // Création de la clé de cache unique incluant la présence du traducteur, le domaine et la locale
        $cacheKey = $enumClass.'_'.$caseName.'_'.($translator ? 'translated' : 'raw').'_'.$translationDomain.'_'.($locale ?? 'default');

        if (!isset(self::$cache[$cacheKey])) {
            $label = $translator ? $translator->trans($caseName, [], $translationDomain, $locale) : $caseName;
            self::$cache[$cacheKey] = $label;
        }

        return self::$cache[$cacheKey];
    }
}
