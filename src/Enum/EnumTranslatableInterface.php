<?php

namespace Zhortein\SymfonyToolboxBundle\Enum;

use Symfony\Contracts\Translation\TranslatorInterface;

interface EnumTranslatableInterface
{
    /**
     * Get a translated label for the given enum value if a translator is provided or returns given enum name.
     *
     * @param TranslatorInterface|null $translator         Translator service, if null the name of the enum case given will be returned
     * @param string|null              $translatableDomain Custom translatable domain if you don't want to use provided translations, default null
     * @param string|null              $locale             Force translation language if you don't want to use current request language, default null
     *
     * @return string Translated label, or enum case name
     */
    public function label(?TranslatorInterface $translator = null, ?string $translatableDomain = null, ?string $locale = null): string;
}
