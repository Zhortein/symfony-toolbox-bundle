
# Enums documentation

This project contains several `enum`s to represent various common and specific options, organized into three main types: `Action`, `Day`, and `Month`. Here are the details about each `enum`, the available values, and the associated methods and options.


## Table of contents
- [General](#general)
  - [Translatable enums](#translatable-enums)
  - [Enums to arrays](#enums-to-arrays)
- [Enum Action](#enum-action)
  - [Cases available](#cases-available)
  - [Methods](#methods)
- [Enum Day](#enum-day)
  - [Cases available](#cases-available---day)
  - [Methods](#methods-1)
- [Enum Month](#enum-month)
  - [Cases available](#cases-available---month)
  - [Methods](#methods-2)

---

## General

The bundle offers various tools around enumerations and in particular:
- An interface and a trait for translatable Enums
- A system for caching translations of enumerations
- A feature to add functionality for tabulating names, values `[names / values]` of Enums

### Translatable enums

All enumerations in the bundle are translatable. They implement the interface
`EnumTranslatableInterface`, which defines the prototype of the `label()` method. This label method
is defined in the `TranslatableTrait` trait. Both of these elements can be used in your own enumerations.

The `TranslatableTrait` trait adds the `label()` method, corresponding to the `EnumTranslatableInterface` interface
and ensuring management of the default domain for the enumeration. This default domain must
be defined in a public string constant named `TRANSLATION_DOMAIN`, otherwise it is the domain
`messages` which will be taken into account. This feature also uses a “simple” translation cache system which avoids
multiple requests from the translator for the same wording request during a Symfony request.

Example to define your custom Enum:
```php
<?php
namespace App\Enum;

use Zhortein\SymfonyToolboxBundle\Traits\TranslatableEnumTrait;

enum MyEnum: string implements EnumTranslatableInterface
{
    use TranslatableEnumTrait;
    
    case OPTION_1 = 'option_a';
    case OPTION_2 = 'option_b';
    case OPTION_3 = 'option_c';
    
    public const string TRANSLATION_DOMAIN = 'my_domain';
}
```

In use, you get:
```php
use Symfony\Contracts\Translation\TranslatorInterface;


$myChoice = MyEnum::OPTION_1;

echo $myChoice->label(); // Affiche 'option_a'
echo $myChoice->label($translator); // Affiche la traduction depuis my_domain pour 'option_a' dans la langue courante
echo $myChoice->label($translator, null, 'de'); // Affiche la traduction depuis my_domain pour 'option_a' dans la langue allemande si définie sinon dans la langue par défaut de votre projet
echo $myChoice->label($translator, 'my_other_domain'); // Affiche la traduction depuis my_other_domain pour 'option_a'
```

### Enums to arrays

All enumerations offered by this bundle have table retrieval functionalities.
These features are collected in the `EnumToArrayTrait` trait, which you can use on your own enumerations.
This trait offers 3 methods:
- `names(): array`: returns a PHP array of possible names for the enumeration
- `values(): array`: returns a PHP array of possible values ​​for the enumeration
- `asArray(bool $valuesAsKeys = false): array`: returns a PHP array `[name => value]`, or `[value => name]` for enumeration depending on the boolean passed as an argument.

Example to define your custom Enum:
```php
<?php
namespace App\Enum;

use Zhortein\SymfonyToolboxBundle\Traits\EnumToArrayTrait;

enum MyEnum: string
{
    use EnumToArrayTrait;
    
    case OPTION_1 = 'option_a';
    case OPTION_2 = 'option_b';
    case OPTION_3 = 'option_c';
}
```

In use, you get:
```php
var_export(MyEnum::names()); // ['OPTION_1', 'OPTION_3', 'OPTION_3']
var_export(MyEnum::values()); // ['option_a', 'option_b', 'option_c']
var_export(MyEnum::asArray()); // ['OPTION_1' => 'option_a', 'OPTION_2' => 'option_b', 'OPTION_3' => 'option_c']
var_export(MyEnum::asArray(true)); // ['option_a' => 'OPTION_1', 'option_b' => 'OPTION_2', 'option_c' => 'OPTION_3']
```

## Enum Action

The `Action` enum represents a series of possible actions, typically used for user interactions or events in an application.

### Cases available

Values of the `Action` `enum` include:

- `ACT_NONE`
- `ACT_CANCEL`
- `ACT_SAVE`
- `ACT_LIST`
- `ACT_VIEW`
- `ACT_ADD`
- `ACT_EDIT`
- `ACT_INSTALL`
- `ACT_SOFT_DELETE`
- `ACT_DESTROY`
- `ACT_RESTORE`
- `ACT_SEARCH`
- `ACT_IMPORT`
- `ACT_EXPORT`
- `ACT_UPDATE`
- `ACT_SEND`
- `ACT_UPLOAD`
- `ACT_DOWNLOAD`
- `ACT_CHANGE_STATUS`
- `ACT_FACTORY_RESET`
- `ACT_START`
- `ACT_PAUSE`
- `ACT_FIRST`
- `ACT_PREVIOUS`
- `ACT_PLAY`
- `ACT_STOP`
- `ACT_NEXT`
- `ACT_LAST`
- `ACT_LOG`
- `ACT_API_CALL`
- `ACT_ENABLE`
- `ACT_DISABLE`
- `ACT_HISTORY`
- `ACT_CONNECT`
- `ACT_DISCONNECT`
- `ACT_LOGIN`
- `ACT_LOGOUT`
- `ACT_ARCHIVED`
- `ACT_STATS`
- `ACT_DASHBOARD`
- `ACT_READ`
- `ACT_UNREAD`
- `ACT_IMPERSONATE`
- `ACT_EXIT_IMPERSONATE`
- `ACT_GRANT`
- `ACT_REVOKE`
- `ACT_SHARE`
- `ACT_COMMENT`
- `ACT_DUPLICATE`
- `ACT_PRINT`
- `ACT_REFRESH`
- `ACT_PROCESS`

### Methods

Each `case` of the `Action` enum has several utility methods:

- **`label(?TranslatorInterface $translator = null, ?string $translatableDomain = null, ?string $locale = null): string`**
  - Returns a translated label for the action using an optional translation service.
  - If `$translator` is provided, it is used to translate the action name according to the specified domain.
  - If `$locale` is provided, the label will be translated into this locale (if available), otherwise the current locale is used.

- **`icon(bool $withSpan = true, string $magnifyClass = '', string $faPrefix = 'fa', ?TranslatorInterface $translator = null, string $translationDomain = 'messages'): string`**
  - Returns the icon class associated with the action.
  - Can include a `<span>` for the icon according to `$withSpan`.

- **`badge(bool $icon = false, bool $text = true, string $faPrefix = 'fa', string $colorScheme = 'primary', ?TranslatorInterface $translator = null, string $translationDomain = 'messages'): string`**
  - Generates a badge for the action, compatible with Bootstrap or other CSS frameworks.

## Enum Day

The `Day` enum represents the days of the week. It is useful for features involving specific days, such as schedules or reminders.

### Cases available - Day

The values ​​of the `Day` `enum` include the days of the week:

- `SUNDAY`
- `MONDAY`
- `TUESDAY`
- `WEDNESDAY`
- `THURSDAY`
- `FRIDAY`
- `SATURDAY`

### Methods

Each `case` of the `Action` enum has several utility methods:

- **`label(?TranslatorInterface $translator = null, ?string $translatableDomain = null, ?string $locale = null): string`**
  - Returns a translated label for the action using an optional translation service.
  - If `$translator` is provided, it is used to translate the action name according to the specified domain.
  - If `$locale` is provided, the label will be translated into this locale (if available), otherwise the current locale is used.

## Enum Month

The `Month` enum represents the months of the year, useful for date management or monthly filter features.

### Cases available - Month

The values of the `enum` `Month` include the months of the year:

- `JANUARY`
- `FEBRUARY`
- `MARCH`
- `APRIL`
- `MAY`
- `JUNE`
- `JULY`
- `AUGUST`
- `SEPTEMBER`
- `OCTOBER`
- `NOVEMBER`
- `DECEMBER`

### Methods

Each `case` of the `Action` enum has several utility methods:

- **`label(?TranslatorInterface $translator = null, ?string $translatableDomain = null, ?string $locale = null): string`**
  - Returns a translated label for the action using an optional translation service.
  - If `$translator` is provided, it is used to translate the action name according to the specified domain.
  - If `$locale` is provided, the label will be translated into this locale (if available), otherwise the current locale is used.