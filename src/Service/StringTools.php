<?php

namespace Zhortein\SymfonyToolboxBundle\Service;

use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\UnicodeString;

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
    public static function sanitizeString(?string $string): ?string
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
    public static function truncate(string $text, int $length = 100, ?string $ellipsys = '…'): string
    {
        return strlen($text) > $length ? substr($text, 0, $length - 3).$ellipsys : $text;
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
     * @param string $alias          La chaîne à vérifier.
     * @param int    $maxLength      Longueur maximale autorisée (par défaut 30). Pas de limite si <= 0.
     * @return bool True si l'alias est valide, sinon False.
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

}
