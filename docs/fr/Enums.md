
# Documentation des Enums

Ce projet contient plusieurs `enum` pour représenter diverses options courantes et spécifiques, organisées en trois types principaux : `Action`, `Day`, et `Month`. Voici les détails sur chaque `enum`, les valeurs disponibles, ainsi que les méthodes et options associées.

## Table des Matières
- [Généralités](#généralités)
  - [Enum traductible](#enums-traductibles)
  - [Enum vers tableaux](#enums-vers-tableaux)
- [Enum Action](#enum-action)
  - [Cases Disponibles](#cases-disponibles)
  - [Méthodes](#méthodes)
- [Enum Day](#enum-day)
  - [Cases Disponibles](#cases-disponibles---day)
  - [Méthodes](#méthodes-1)
- [Enum Month](#enum-month)
  - [Cases Disponibles](#cases-disponibles---month)
  - [Méthodes](#méthodes-2)

---

## Généralités

Le bundle propose divers outils autour des énumérations et notamment :
- Une interface et un trait pour les Enum traductibles
- Un système de cache des traductions des énumérations
- Un trait pour ajouter des fonctionnalités de mise en tableau des noms, valeurs noms / valeurs des Enums 

### Enums traductibles

Toutes les énumérations du bundle sont traductibles. Elles implémentent l'interface 
`EnumTranslatableInterface`, qui défini le prototype de la méthode `label()`. Cette méthode label
est définie dans le trait `TranslatableTrait`. Ces deux éléments sont utilisables dans vos propres énumérations.

Le trait `TranslatableTrait` ajoute la méthode `label()`, correspondant à l'interface `EnumTranslatableInterface`
et assurant la gestion du domaine par défaut pour l'énumération. Ce domaine apr défaut doit 
être défini dans une constante publique de type string nommée `TRANSLATION_DOMAIN`, sinon c'est le domaine 
`messages` qui sera pris en compte. Ce trait utilise également un système de cache "simple" des traductions qui évite
les multiples sollicitations du traducteur pour une même demande de libellé pendant une requête Symfony.

Exemple pour définir votre Enum personnalisée :
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

En utilisation, vous obtenez :
```php
use Symfony\Contracts\Translation\TranslatorInterface;


$myChoice = MyEnum::OPTION_1;

echo $myChoice->label(); // Affiche 'option_a'
echo $myChoice->label($translator); // Affiche la traduction depuis my_domain pour 'option_a' dans la langue courante
echo $myChoice->label($translator, null, 'de'); // Affiche la traduction depuis my_domain pour 'option_a' dans la langue allemande si définie sinon dans la langue par défaut de votre projet
echo $myChoice->label($translator, 'my_other_domain'); // Affiche la traduction depuis my_other_domain pour 'option_a'
```

### Enums vers tableaux

Toutes les énumérations proposées par ce bundle disposent des fonctionnalités de récupération en tableaux.
Ces fonctionnalités sont rassemblées dans le trait `EnumToArrayTrait`, que vous pouvez utiliser sur vos propres énumérations.
Ce trait propose 3 méthodes :
- `names(): array` : renvoie un tableau PHP des noms possibles pour l'énumération
- `values(): array` : renvoie un tableau PHP des valeurs possibles pour l'énumération
- `asArray(bool $valuesAsKeys = false): array` : renvoie un tableau PHP `[nom => valeur]`, ou `[valeur => nom]` pour l'énumération en fonction du booléen passé en argument.

Exemple pour définir votre Enum personnalisée :
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

En utilisation, vous obtenez :
```php
var_export(MyEnum::names()); // ['OPTION_1', 'OPTION_3', 'OPTION_3']
var_export(MyEnum::values()); // ['option_a', 'option_b', 'option_c']
var_export(MyEnum::asArray()); // ['OPTION_1' => 'option_a', 'OPTION_2' => 'option_b', 'OPTION_3' => 'option_c']
var_export(MyEnum::asArray(true)); // ['option_a' => 'OPTION_1', 'option_b' => 'OPTION_2', 'option_c' => 'OPTION_3']
```

## Enum Action

L'`enum` `Action` représente une série d'actions possibles, typiquement utilisées pour des interactions utilisateur ou des événements dans une application.

### Cases Disponibles

Les valeurs de l'`enum` `Action` incluent :

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

### Méthodes

Chaque `case` de l'`enum` `Action` dispose de plusieurs méthodes utilitaires :

- **`label(?TranslatorInterface $translator = null, ?string $translatableDomain = null, ?string $locale = null): string`**
  - Retourne un label traduit pour l'action en utilisant un service de traduction optionnel.
  - Si `$translator` est fourni, il est utilisé pour traduire le nom de l'action selon le domaine spécifié.
  - Si `$locale` est fourni, le libellé sera traduit dans cette locale (si disponible), sinon c'est la locale courante qui est utilisée.
  
- **`icon(bool $withSpan = true, string $magnifyClass = '', string $faPrefix = 'fa', ?TranslatorInterface $translator = null, string $translationDomain = 'messages'): string`**
  - Retourne la classe d'icône associée à l'action.
  - Peut inclure un `<span>` pour l’icône selon `$withSpan`.
  
- **`badge(bool $icon = false, bool $text = true, string $faPrefix = 'fa', string $colorScheme = 'primary', ?TranslatorInterface $translator = null, string $translationDomain = 'messages'): string`**
  - Génère un badge pour l'action, compatible avec Bootstrap ou d'autres frameworks CSS.

## Enum Day

L'`enum` `Day` représente les jours de la semaine. Elle est utile pour les fonctionnalités impliquant des jours spécifiques, comme des planifications ou des rappels.

### Cases Disponibles - Day

Les valeurs de l'`enum` `Day` incluent les jours de la semaine :

- `SUNDAY` - Dimanche
- `MONDAY` - Lundi
- `TUESDAY` - Mardi
- `WEDNESDAY` - Mercredi
- `THURSDAY` - Jeudi
- `FRIDAY` - Vendredi
- `SATURDAY` - Samedi

### Méthodes

Chaque `case` de l'`enum` `Action` dispose de plusieurs méthodes utilitaires :

- **`label(?TranslatorInterface $translator = null, ?string $translatableDomain = null, ?string $locale = null): string`**
  - Retourne un label traduit pour l'action en utilisant un service de traduction optionnel.
  - Si `$translator` est fourni, il est utilisé pour traduire le nom de l'action selon le domaine spécifié.
  - Si `$locale` est fourni, le libellé sera traduit dans cette locale (si disponible), sinon c'est la locale courante qui est utilisée.

## Enum Month

L'`enum` `Month` représente les mois de l'année, pratique pour les fonctionnalités de gestion de dates ou de filtres mensuels.

### Cases Disponibles - Month

Les valeurs de l'`enum` `Month` incluent les mois de l'année :

- `JANUARY` - Janvier
- `FEBRUARY` - Février
- `MARCH` - Mars
- `APRIL` - Avril
- `MAY` - Mai
- `JUNE` - Juin
- `JULY` - Juillet
- `AUGUST` - Août
- `SEPTEMBER` - Septembre
- `OCTOBER` - Octobre
- `NOVEMBER` - Novembre
- `DECEMBER` - Décembre

### Méthodes

Chaque `case` de l'`enum` `Action` dispose de plusieurs méthodes utilitaires :

- **`label(?TranslatorInterface $translator = null, ?string $translatableDomain = null, ?string $locale = null): string`**
  - Retourne un label traduit pour l'action en utilisant un service de traduction optionnel.
  - Si `$translator` est fourni, il est utilisé pour traduire le nom de l'action selon le domaine spécifié.
  - Si `$locale` est fourni, le libellé sera traduit dans cette locale (si disponible), sinon c'est la locale courante qui est utilisée.

