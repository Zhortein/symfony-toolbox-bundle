# StringTools

The StringTools service provides various utilities to manipulate and clean strings. These tools help simplify common string operations required in web applications, such as removing diacritics, sanitizing file names, truncating text, and counting words.
## Features

- [Detection of string arrays](#detection-of-string-arrays)
- [File name sanitization](#file-name-sanitization)
- [Removing diacritics/accents](#removing-diacriticsaccents)
- [String sanitization](#string-sanitization)
- [Convert CSV line to array](#convert-csv-line-to-array)
- [Truncate a string](#truncate-a-string)
- [Convert text to boolean](#convert-text-to-boolean)
- [Special character replacement](#special-character-replacement)
- [Word counting](#word-counting)
- [Ensure string (no null)](#ensure-string-no-null)

### Detection of string arrays

Checks if the given variable is an array of strings.

#### Method

```php
public static function isArrayOfStrings(mixed $var): bool
```
Returns true if `$var` is an array of strings, false otherwise.

#### Parameters
- `mixed $var` : The variable to check.

#### Example

```php
$myArray = ['aaa', 'bbb'];
StringTools::isArrayOfStrings($myArray); // Returns true

$myArray = [1, 'bbb'];
StringTools::isArrayOfStrings($myArray); // Returns false

$myArray = [['aaa', 'ccc'], 'bbb'];
StringTools::isArrayOfStrings($myArray); // Returns false
```

### File name sanitization

Cleans the file name by replacing special characters and spaces, truncating if needed. This method is mainly intended for creating a file name.
#### Method
```php
public static function sanitizeFileName(string $filename, int $maxLength = 0): string
```
Returns the cleaned file name.

#### Parameters
- `string $filename` : Name of the file to sanitize.
- `int $maxLength = 0` : Desired maximum length for the file name.

#### Example
```php
$filename = "résumé.pdf";
$safeFilename = StringTools::sanitizeFilename($filename);
echo $safeFilename; // Returns: "resume.pdf"
```

### Removing diacritics/accents

Removes accents and diacritics in the provided string.

#### Method
```php
public static function removeDiacritics(string $string): string
```
Returns the string without accents or diacritics.

#### Parameters
- `string $string` : The string to "clean".

#### Example
```php
$text = "Café à la crème";
$normalized = StringTools::removeDiacritics($text);
echo $normalized; // Returns: "Cafe a la creme"
```

### String sanitization

Cleans a string by removing HTML elements and converting it according to the system (ISO-8859-1 for Windows, UTF-8 otherwise).

#### Method
```php
public static function sanitizeString(?string $string): ?string
```
Returns the cleaned string without HTML tags and with encoding conversion if needed.

#### Parameters
- `?string $string` : The string to "clean".

#### Example
```php
$filename = '<a href="https://www.example.com/my-file.txt">My article title</a>'; 
$safeFilename = StringTools::sanitizeFilename($filename); 
echo $safeFilename; // Returns: My article title
```

### Convert CSV line to array

Converts a CSV string to an array.

#### Method
```php
public static function explodeCsvLine(string $line, string $delimiter = ',', string $enclosure = '"'): array
```
Returns an array of elements parsed from the CSV line string.

#### Parameters
- `string $line` : The string representing the CSV line to convert.
- `string $delimiter = ','` : The delimiter between CSV fields (default: ",").
- `string $enclosure = '"'` : The character enclosing string values, default is double quote.

#### Example
```php
$csvLine = '"André",123,"Réussi"';
$array = StringTools::explodeCsvLine($csvLine);
var_export($array); // Returns: [0 => "André", 1 => 123, 2 => "Réussi"]
```

### Truncate a string

Truncates the string to a specified length, adding ellipses if necessary.

#### Method
```php
public static function truncate(string $text, int $length = 100): string
```
Returns the truncated string.

#### Parameters
- `string $text` : The string to truncate.
- `int $length = 100` : The maximum allowed length, default is 100 characters.
- `?string $ellipsys = '…'` : The ellipsis to add at the end of the string, default is '…'.

#### Example
```php
$text = "This is a long sentence that needs truncation.";
$truncated = StringTools::truncate($text, 20);
echo $truncated; // Returns: "This is a long se..."
```

### Convert text to boolean

Converts the text to boolean.

#### Method
```php
public static function text2Boolean(?string $text, array $trueValues = ['1', 'true', 'oui', 'yes', 'o', 'y', 'j']): bool
```
Returns true if the provided string is a value equivalent to "true".

#### Parameters
- `?string $text` : The string to check.
- `array $trueValues = ['1', 'true', 'oui', 'yes', 'o', 'y', 'j']` : The array of values considered "true"; any other value will be considered "false".

#### Example
```php
echo StringTools::text2Boolean('oui'); // true
echo StringTools::text2Boolean('O'); // true
echo StringTools::text2Boolean('True'); // true
echo StringTools::text2Boolean('aaa'); // false
echo StringTools::text2Boolean(null); // false
```

### Special character replacement

Replaces special characters with their non-accented equivalents. However, it is preferable to use [removeDiacritics](#removing-diacriticsaccents).

#### Method
```php
public static function replaceSpecialChar(string $str): string
```
Returns the string with special characters replaced.

#### Parameters
- `string $str` : The string in which you want to perform replacements.

#### Example
```php
$str = 'Mon œuf est cuit';
echo StringTools::replaceSpecialChar($str); // Returns: "Mon oeuf est cuit"
```

### Word counting

Counts the number of words in a text.

#### Method
```php
public static function countWords(?string $text): int
```
Returns the number of words found.

#### Parameters
- `?string $text` : Text in which you want to count words.

#### Example
```php
$text = 'Ceci est une longue phrase dans laquelle je veux compter les mots';
echo StringTools::countWords($text); // Returns: 12
```

### Ensure string (no null)

Returns a string, or an empty string if the parameter is null.

#### Method
```php
public static function getStringOrEmpty(mixed $input): string
```
Returns the string representation of the variable if not null; returns an empty string if null.

#### Parameters
- `mixed $input` : The variable to check and transform into a string.

#### Example
```php
echo StringTools::getStringOrEmpty(null); // Returns: ''
echo StringTools::getStringOrEmpty(123); // Returns: '123'
echo StringTools::getStringOrEmpty('Azerty'); // Returns: 'Azerty'
echo StringTools::getStringOrEmpty(''); // Returns: ''
```

## Notes

These features will evolve with the advances in the symfony/string component. 
They have been created over multiple projects to avoid redundant code.