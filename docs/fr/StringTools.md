# StringTools

Le service `StringTools` fournit divers utilitaires pour manipuler et nettoyer les chaînes. Ces outils aident à simplifier les opérations de chaîne couramment requises dans les applications Web, telles que la suppression des signes diacritiques, le nettoyage des noms de fichiers, la troncature du texte et le comptage des mots.

## Fonctionnalités

- [Détection de tableaux de chaînes](#détection-de-tableaux-de-chaînes)
- [Nettoyage de nom de fichier](#nettoyage-de-nom-de-fichier)
- [Suppression des diacritiques / accents](#suppression-des-diacritiques--accents)
- [Nettoyage d'une chaîne](#nettoyage-dune-chaîne)
- [Convertir ligne de CSV en tableau](#convertir-ligne-de-csv-en-tableau)
- [Tronquer une chaîne](#tronquer-une-chaîne)
- [Convertir un texte en booléen](#convertir-un-texte-en-booléen)
- [Remplacement de caractères spéciaux](#remplacement-de-caractères-spéciaux)
- [Comptage des mots](#comptage-des-mots)
- [S'assurer d'avoir une chaîne (pas de null)](#sassurer-davoir-une-chaîne-pas-de-null)
- [Vérifier si une chaine est un alias SQL valide](#vérifier-si-une-chaine-est-un-alias-sql-valide)

### Détection de tableaux de chaînes

Vérifie si la variable donnée est un tableau de chaînes.

#### Méthode

```php
public static function isArrayOfStrings(mixed $var): bool
```
Retourne true si `$var` est un tableau de chaînes, false sinon.

#### Paramètres
- `mixed $var` : La variable à tester

#### Exemple

```php
$myArray = ['aaa', 'bbb'];
StringTools::isArrayOfStrings($myArray); // Retourne true

$myArray = [1, 'bbb'];
StringTools::isArrayOfStrings($myArray); // Retourne false

$myArray = [['aaa', 'ccc'], 'bbb'];
StringTools::isArrayOfStrings($myArray); // Retourne false
```

### Nettoyage de nom de fichier

Nettoie le nom de fichier en remplaçant les caractères spéciaux et les espaces, et en le tronquant au besoin.
Cette méthode est majoritairement destinée à la création d'un fichier.

#### Méthode
```php
public static function sanitizeFileName(string $filename, int $maxLength = 0): string
```
Retourne le nom de fichier nettoyé.

#### Paramètres
- `string $filename` : Nom du fichier à nettoyer.
- `int $maxLength = 0` : Longueur maximale souhaitée pour le nom de fichier.

#### Exemple
```php
$filename = "résumé.pdf";
$safeFilename = StringTools::sanitizeFilename($filename);
echo $safeFilename; // Retourne : "resume.pdf"
```

### Suppression des diacritiques / accents

Supprime les accents et les diacritiques dans la chaîne fournie.

#### Méthode
```php
public static function removeDiacritics(string $string): string
```
Retourne la chaîne sans accents ni diacritiques.

#### Paramètres
- `string $string` : La chaîne à "nettoyer".

#### Exemple
```php
$text = "Café à la crème";
$normalized = StringTools::removeDiacritics($text);
echo $normalized; // Retourne : "Cafe a la creme"
```

### Nettoyage d'une chaîne

Nettoie une chaîne en supprimant les éléments HTML et en convertissant selon le système (ISO-8859-1 pour WINDOWS, UTF-8 sinon).

#### Méthode
```php
public static function sanitizeString(?string $string): ?string
```
Retourne la chaîne nettoyée des tags HTML, avec conversion de l'encodage si besoin.

#### Paramètres
- `?string $string` : La chaîne à "nettoyer"

#### Exemple
```php
$filename = '<a href="https://www.exemple.com/mon-de-fichier.txt">Titre de mon article</a>';
$safeFilename = StringTools::sanitizeFilename($filename);
echo $safeFilename; // Retourne : Titre de mon article
```

### Convertir ligne de CSV en tableau

Convertit une chaîne CSV en tableau.

#### Méthode
```php
public static function explodeCsvLine(string $line, string $delimiter = ',', string $enclosure = '"'): array
```
Retourne le tableau des éléments lus dans la chaîne représentant une ligne de CSV.

#### Paramètres
- `string $line` : La chaîne représentant à ligne de CSV à convertir
- `string $delimiter = ','` : Le délimiteur entre les champs du CSV (par défaut : ",")
- `string $enclosure = '"'` : Le "délimiteur" des chaînes de caractères, par défaut le double guillemet

#### Exemple
```php
$csvLine = '"André",123,"Réussi"';
$array = StringTools::explodeCsvLine($csvLine);
var_export($array); // Retourne : [0 => "André", 1 => 123, 2 => "Réussi"]
```

### Tronquer une chaîne

Tronque la chaîne à une longueur spécifiée avec des points de suspension si nécessaire.

#### Méthode
```php
public static function truncate(string $text, int $length = 100): string
```
Retourne la chaîne tronquée.

#### Paramètres
- `string $text` : La chaîne à tronquer.
- `int $length = 100` : La longueur maximale autorisée, 100 caractères par défaut.
- `?string $ellipsys = '…'` : L'ellipse à ajouter en fin de chaîne, si souhaité, par défaut '…'.

#### Exemple
```php
$text = "This is a long sentence that needs truncation.";
$truncated = StringTools::truncate($text, 20);
echo $truncated; // Retourne : "This is a long se..."
```

### Convertir un texte en booléen

Convertit le texte en booléen.

#### Méthode
```php
public static function text2Boolean(?string $text, array $trueValues = ['1', 'true', 'oui', 'yes', 'o', 'y', 'j']): bool
```
Renvoie true si la chaîne fournie est une valeur équivalente à "vrai".

#### Paramètres
- `?string $text` : La chaîne à tester
- `array $trueValues = ['1', 'true', 'oui', 'yes', 'o', 'y', 'j']` : Le tableau des valeurs étant considérées comme "Vrai", toute autre valeur sera considérée comme "faux".

#### Exemple
```php
echo StringTools::text2Boolean('oui'); // true
echo StringTools::text2Boolean('O'); // true
echo StringTools::text2Boolean('True'); // true
echo StringTools::text2Boolean('aaa'); // false
echo StringTools::text2Boolean(null); // false
```

### Remplacement de caractères spéciaux

Remplace les caractères spéciaux par leurs équivalents non accentués. Il est préférable toutefois d'utiliser [removeDiacritics](#suppression-des-diacritiques--accents).

#### Méthode
```php
public static function replaceSpecialChar(string $str): string
```
Renvoie la chaîne avec ses caractères spéciaux remplacés.

#### Paramètres
- `string $str` : La chaîne dans laquelle on souhaite effectuer les remplacements.

#### Exemple
```php
$str = 'Mon œuf est cuit';
echo StringTools::replaceSpecialChar($str); // Retourne : "Mon oeuf est cuit"
```

### Comptage des mots

Compte le nombre de mots dans un texte.

#### Méthode
```php
public static function countWords(?string $text): int
```
Retourne le nombre de mots trouvé.

#### Paramètres
- `?string $text` : Texte dans lequel on souhaite compter les mots.

#### Exemple
```php
$text = 'Ceci est une longue phrase dans laquelle je veux compter les mots';
echo StringTools::countWords($text); // Retourne : 12
```

### S'assurer d'avoir une chaîne (pas de null)

Retourne une chaîne, ou une chaîne vide si le paramètre est null.

#### Méthode
```php
public static function getStringOrEmpty(mixed $input): string
```
Renvoie la chaîne représentant la variable si la variable est 'non nulle', renvoie une chaîne vide en cas de valeur nulle.

#### Paramètres
- `mixed $input` : La variable à tester, transformer en chaîne.

#### Exemple
```php
echo StringTools::getStringOrEmpty(null); // Retourne : ''
echo StringTools::getStringOrEmpty(123); // Retourne : '123'
echo StringTools::getStringOrEmpty('Azerty'); // Retourne : 'Azerty'
echo StringTools::getStringOrEmpty(''); // Retourne : ''
```

### Vérifier si une chaine est un alias SQL valide

Retourne true si la chaine peut être acceptée pour un alias SQL, false sinon. Si une longueur est spécifiée,
vérifie également que la chaine ne dépasse pas cette longueur. Une longueur négative ou nulle supprime le test sur la longueur.

#### Méthode
```php
public static function isValidSqlAlias(string $alias, int $maxLength = 30): bool
```
Retourne true si la chaine peut être acceptée pour un alias SQL, false sinon.

#### Paramètres
- `string $alias` : La chaîne à tester.
- `int $maxLength = 30` : La longueur maximale, négatif ou 0 pour ne pas tester la longueur.

#### Exemple
```php
echo StringTools::isValidSqlAlias("validAlias"); // true
echo StringTools::isValidSqlAlias("invalid-alias"); // false
echo StringTools::isValidSqlAlias("TooLongAliasName12345", 30); // false
echo StringTools::isValidSqlAlias("AliasWithNoLimit", 0); // true
echo StringTools::isValidSqlAlias("AnotherSuperLongAliasWithoutLimit", -1); // true
echo StringTools::isValidSqlAlias("validAlias", 64); // true
echo StringTools::isValidSqlAlias("TooLongAliasForMyLimit", 10); // false
```

## Notes

Ces fonctionnalités évolueront en fonction des avancées dans le composant symfony/string.
Elles ont été créées au fil des besoins sur de multiples projets, pour éviter les redondances de code.