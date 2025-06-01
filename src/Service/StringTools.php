<?php

namespace Zhortein\SymfonyToolboxBundle\Service;

use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\TruncateMode;
use Symfony\Component\String\UnicodeString;

use function Symfony\Component\String\u;

class StringTools
{
    public const string WINDOWS = 'WINDOWS';

    /**
     * Vérifie si la variable donnée est un tableau de chaînes.
     */
    public static function isArrayOfStrings(mixed $var): bool
    {
        return is_array($var) && array_reduce($var, static fn ($carry, $item) => $carry && is_string($item), true);
    }

    /**
     * Nettoie le nom de fichier en remplaçant les caractères spéciaux et les espaces.
     */
    public static function sanitizeFileName(string $filename, int $maxLength = 0): string
    {
        $slugger = new AsciiSlugger();
        $filename = $slugger->slug($filename)->lower()->toString();

        return $maxLength > 0 ? substr($filename, 0, $maxLength) : $filename;
    }

    /**
     * Supprime les accents et les diacritiques.
     */
    public static function removeDiacritics(string $string): string
    {
        return (new UnicodeString($string))->ascii()->toString();
    }

    /**
     * Assainit une chaîne en supprimant les éléments HTML et en convertissant selon le système.
     *
     * @todo Gestion du format RTF
     */
    public static function sanitizeString(?string $string): string|false|null
    {
        if (null === $string) {
            return null;
        }
        $string = strip_tags($string); // Supprime HTML

        $string = str_replace(['e0', 'e9', 'e8'], ['à', 'é', 'è'], $string);

        return str_contains(php_uname(), self::WINDOWS) ? mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1') : $string;
    }

    /**
     * Convertit une chaîne CSV en tableau.
     *
     * @return array<int, string|null>
     */
    public static function explodeCsvLine(string $line, string $delimiter = ',', string $enclosure = '"'): array
    {
        return str_getcsv($line, $delimiter, $enclosure);
    }

    /**
     * Tronque la chaîne à une longueur spécifiée avec des points de suspension si nécessaire.
     */
    public static function truncate(string $text, int $length = 100, ?string $ellipsis = '…'): string
    {
        if (SymfonyVersion::isSymfony72OrHigher()) {
            $truncated = u($text)->truncate($length);

            return strlen($text) > $length ? $truncated->toString().$ellipsis : $truncated->toString();
        }

        return strlen($text) > $length ? substr($text, 0, $length).$ellipsis : $text;
    }

    /**
     * Tronque le texte avant le mot sans couper les mots, en ajoutant éventuellement une ellipse si le texte est plus long que la longueur spécifiée.
     */
    public static function truncateBefore(string $text, int $length = 100, ?string $ellipsis = '…'): string
    {
        if (SymfonyVersion::isSymfony72OrHigher()) {
            $truncated = u($text)->truncate($length, cut: TruncateMode::WordBefore);

            return strlen($text) > $length ? $truncated->toString().$ellipsis : $truncated->toString();
        }

        return self::truncate($text, $length, $ellipsis);
    }

    /**
     * Troncature du texte après un certain nombre de caractères, en conservant les mots entiers si possible.
     */
    public static function truncateAfter(string $text, int $length = 100, ?string $ellipsis = '…'): string
    {
        if (SymfonyVersion::isSymfony72OrHigher()) {
            $truncated = u($text)->truncate($length, cut: TruncateMode::WordAfter);

            return strlen($text) > $length ? $truncated->toString().$ellipsis : $truncated->toString();
        }

        return self::truncate($text, $length, $ellipsis);
    }

    /**
     * Convertit le texte en booléen.
     *
     * @param string[] $trueValues
     */
    public static function text2Boolean(?string $text, array $trueValues = ['1', 'true', 'oui', 'yes', 'o', 'y', 'j']): bool
    {
        if (!self::isArrayOfStrings($trueValues)) {
            return false;
        }

        return in_array(strtolower(trim($text ?? '')), $trueValues, true);
    }

    /**
     * Remplace les caractères spéciaux par leurs équivalents non accentués.
     */
    public static function replaceSpecialChar(string $str): string
    {
        $str = (new UnicodeString($str))->ascii()->toString();

        return strtr($str, [
            'œ' => 'oe', 'Œ' => 'OE', 'æ' => 'ae', 'Æ' => 'AE', 'Ç' => 'C',
            // Ajouter d'autres remplacements spécifiques ici...
        ]);
    }

    /**
     * Compte les mots dans un texte.
     */
    public static function countWords(?string $text): int
    {
        return $text ? str_word_count(strip_tags($text)) : 0;
    }

    /**
     * Retourne une chaîne, ou une chaîne vide si le paramètre est null.
     */
    public static function getStringOrEmpty(int|float|bool|string|null $input): string
    {
        return (string) ($input ?? '');
    }

    /**
     * Vérifie si une chaîne est un alias SQL valide.
     *
     * Un alias valide :
     * - Commence par une lettre (majuscule ou minuscule).
     * - Contient uniquement des lettres (sans accents), chiffres et underscores.
     * - Optionnellement, respecte une longueur maximale.
     *
     * @param string $alias     la chaîne à vérifier
     * @param int    $maxLength Longueur maximale autorisée (par défaut 30). Pas de limite si <= 0.
     *
     * @return bool true si l'alias est valide, sinon False
     */
    public static function isValidSqlAlias(string $alias, int $maxLength = 30): bool
    {
        // Vérifie le format général de l'alias
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $alias)) {
            return false;
        }

        // Vérifie la longueur si une limite est définie
        if ($maxLength > 0 && strlen($alias) > $maxLength) {
            return false;
        }

        return true;
    }

    /**
     * Formatte un prénom : trim, première lettre de chaque mot en majuscule, le reste en minuscule.
     * Supporte Unicode, accents, multi-mots ("Jean-Marc", "José Maria", ...).
     *
     * @param string|null $firstName Prénom brut ou null
     * @return string|null Prénom normalisé ou null si entrée vide
     */
    public static function formatFirstName(?string $firstName): ?string
    {
        if (null === $firstName) {
            return null;
        }
        $firstName = trim($firstName);

        // 1. Symfony String
        if (class_exists(UnicodeString::class)) {
            return (string) u($firstName)->title(true);
        }
        // 2. mbstring
        if (function_exists('mb_convert_case')) {
            return preg_replace_callback(
                '/([^\s-]+)/u',
                fn($m) => mb_convert_case($m[1], MB_CASE_TITLE, 'UTF-8'),
                $firstName
            );
        }
        // 3. ASCII fallback (majuscules pour la 1ère lettre, minuscule le reste)
        return preg_replace_callback(
            '/([^\s-]+)/',
            fn($m) => ucfirst(strtolower($m[1])),
            $firstName
        );
    }

    /**
     * Formatte un nom de famille : trim, majuscules, support Unicode/accents.
     *
     * @param string|null $lastName Nom de famille brut ou null
     * @return string|null Nom normalisé en MAJUSCULES ou null si entrée vide
     */
    public static function formatLastName(?string $lastName): ?string
    {
        if (null === $lastName) {
            return null;
        }
        $lastName = trim($lastName);

        if (class_exists(UnicodeString::class)) {
            return (string) u($lastName)->upper();
        }
        if (function_exists('mb_strtoupper')) {
            return mb_strtoupper($lastName, 'UTF-8');
        }
        return strtoupper($lastName);
    }

    /**
     * Formatte un nom d'organisation : trim, titre chaque mot.
     *
     * @param string|null $organisation Nom de l'organisation brut ou null
     * @return string|null Nom formaté ou null si entrée vide
     */
    public static function formatOrganisationName(?string $organisation): ?string
    {
        if (null === $organisation) {
            return null;
        }
        $organisation = trim($organisation);

        if (class_exists(UnicodeString::class)) {
            return (string) u($organisation)->title(true);
        }
        if (function_exists('mb_convert_case')) {
            return preg_replace_callback(
                '/([^\s-]+)/u',
                fn($m) => mb_convert_case($m[1], MB_CASE_TITLE, 'UTF-8'),
                $organisation
            );
        }
        return preg_replace_callback(
            '/([^\s-]+)/',
            fn($m) => ucfirst(strtolower($m[1])),
            $organisation
        );
    }

}
