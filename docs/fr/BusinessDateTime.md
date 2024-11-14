# BusinessDateTime Documentation

## Introduction

La classe `BusinessDateTime` est utilisée pour gérer les dates d'affaires, en tenant compte des jours ouvrés spécifiques et des jours fériés pour un pays donné. Elle permet de vérifier si une date donnée correspond à un jour ouvré ou non, et de déterminer quels jours sont fériés dans un pays spécifique.

## Propriétés

### $holidays

```php
/**
 * Stocke les paramètres actuels des jours fériés.
 *
 * @var array<int, \DateTime>
 */
protected array $holidays = [];
```

Cette propriété contient un tableau de dates représentant les jours fériés actuels.

### $workingDays

```php
/**
 * Stocke les jours ouvrés de la semaine.
 *
 * @var array<int, Day>
 */
protected array $workingDays = [Day::MONDAY, Day::TUESDAY, Day::WEDNESDAY, Day::THURSDAY, Day::FRIDAY];
```

Cette propriété contient un tableau des jours de la semaine considérés comme ouvrés (du lundi au vendredi par défaut).

## Constructeur

```php
public function __construct(
    protected readonly HolidayProviderManager $holidayProviderManager,
    protected ?LoggerInterface $logger = null,
    protected ?int $currentYear = null,
    protected ?string $currentCountry = null
)
```

Le constructeur initialise la classe avec les paramètres suivants :
- `HolidayProviderManager $holidayProviderManager`: Un gestionnaire des fournisseurs de jours fériés.
- `LoggerInterface $logger`: Un logger optionnel pour enregistrer les informations.
- `int $currentYear`: L'année courante (facultatif, prendra l'année courante si aucune année définie).
- `string $currentCountry`: Le pays courant (facultatif, prendra la France en référence si aucun pays défini).

À noter que l'année et le pays courant ne servent qu'à l'initialisation de l'outil, vous pouvez ensuite changer à votre guise.

Vous pouvez choisir le logger à utiliser via la méthode `setLogger(LoggerInterface)` et récupérer celui en place via `getLogger()`.

## Enumérations

Chaque énumération dispose d'une méthode label permettant de récupérer le libellé 
traduit, si le TranslatorInterface est fourni, ou le nom correspondant dans l'énumération.

```php
use Zhortein\SymfonyToolboxBundle\Enum\Day;
use Symfony\Contracts\Translation\TranslatorInterface;

class TestController extends AbstractController 
{
    public function testAction(TranslatorInterface $translator): Response
    {
        $monday = Day::MONDAY;
        
        echo $monday->label($translator);
        // Affichera : Lundi
        
        echo $monday->label();
        // Affichera : MONDAY
    }
}
```

### Jours de la semaine

Vous disposez d'une énumération des jours de la semaine : 
- `Day::MONDAY` = 1
- `Day::TUESDAY` = 2
- `Day::WEDNESDAY` = 3
- `Day::THURSDAY` = 4
- `Day::FRIDAY` = 5
- `Day::SATURDAY` = 6
- `Day::SUNDAY` = 7

### Mois de l'année
Vous disposez d'une énumération des mois dans l'année
- `Month::JANUARY` = 1
- `Month::FEBRUARY` = 2
- `Month::MARCH` = 3
- `Month::APRIL` = 4
- `Month::MAY` = 5
- `Month::JUNE` = 6
- `Month::JULY` = 7
- `Month::AUGUST` = 8
- `Month::SEPTEMBER` = 9
- `Month::OCTOBER` = 10
- `Month::NOVEMBER` = 11
- `Month::DECEMBER` = 12

## Méthodes

### Méthodes de vérification des jours

Les méthodes suivantes permettent de vérifier si une date donnée correspond à un jour spécifique de la semaine :

- `isMonday(\DateTimeInterface $myDate): bool`
- `isTuesday(\DateTimeInterface $myDate): bool`
- `isWednesday(\DateTimeInterface $myDate): bool`
- `isThursday(\DateTimeInterface $myDate): bool`
- `isFriday(\DateTimeInterface $myDate): bool`
- `isSaturday(\DateTimeInterface $myDate): bool`
- `isSunday(\DateTimeInterface $myDate): bool`

#### Exemple d'utilisation

```php
$holidayProviderManager = new HolidayProviderManager();
$logger = null; // ou une instance de LoggerInterface
$currentYear = 2023;
$currentCountry = 'FR';

$businessDateTime = new BusinessDateTime($holidayProviderManager, $logger, $currentYear, $currentCountry);

$date = new \DateTime('2023-12-25'); // Exemple de date

if ($businessDateTime->isMonday($date)) {
    echo "Le 25 décembre 2023 est un lundi.";
} else {
    echo "Le 25 décembre 2023 n'est pas un lundi.";
}
```

### Gestion des jours de travail

Par défaut, l'outil considère la semaine de travail du lundi au vendredi, à l'exclusion des week-ends.
Vous pouvez modifier ce comportement en utilisant les fonctions de gestion des jours travaillés :

- `setWorkingDays(int[]) : void` : vous passez un tableau d'entiers correspondant aux jours ouvrés. Ces entiers sont ceux de la fonction date() de PHP.
- `setWorkingDays(Day[]) : void` : vous passez un tableau avec les valeurs de l'énumération "Day" correspondant aux jours ouvrés. Ces entiers sont ceux de la fonction date() de PHP.
- `getWorkingDays(bool $asIntegers = false) : array` : renvoie le tableau des jours de la semaine, soit sous forme d'entiers (si $asIntegers = true), soit sous forme d'énumérations "Day".

Ces jours de travail influent sur le fonctionnement de l'outil et notamment sur les fonctions suivantes :

- `isWorkingDay(\DateTimeInterface $myDate): bool` : Renvoie true si la date fournie est un jour de travail.
- `addBusinessDays(\DateTimeInterface $myDate, int $nbToAdd): \DateTimeInterface` : Renvoie la date calculée pour l'ajout du nombre de jours de travail $nbToAdd à la date fournie.
- `getNbBusinessDays(\DateTimeInterface $start, \DateTimeInterface $end): int` : Renvoie le nombre de jours de travail entre les deux dates fournies.
- `getBusinessDays(\DateTimeInterface $start, \DateTimeInterface $end): array` : Renvoie le tableau des dates des jours de travail entre les deux dates fournies.

Ces fonctions récupèrent de manière automatique les fériés des années supplémentaires nécessaire lorsque les interrogations se font sur une plage au delà de l'année courante.

### La gestion des congés / fériés

L'outil inclut une gestion des jours fériés et des congés. Les jours fériés sont fournis par des 
classes dédiées à un pays en particulier, ce sont des fournisseurs de jours fériés.

Les pays disponibles sont (d'autres s'ajouteront par la suite, mais vous pouvez définir vos propres fournisseurs) : 
- FR : France
- BE : Belgique
- EN : Angleterre
- US : Etats-Unis
- ES : Espagne
- DE : Allemagne

Pour utiliser un fournisseur de jours fériés, il suffit de définir le jeu de données souhaité via 
l'appel à la méthode `setHolidays`. Vous pouvez également enregistrer vos propres jours de congés à 
ajouter aux fériés en utilisant `addHoliday`.

### Méthodes

- `setHolidays(\DateTimeInterface|int $myDate, string $countryCode = 'FR'): array` : Initialise le tableau des congés avec fériés pour le pays et l'année donnés, l'année pouvant être extraite du DateTimeInterface fourni.
- `getHolidays(\DateTimeInterface|int $myDate, string $countryCode = 'FR'): array` : Est un alias pour `setHolidays`.
- `addHoliday(\DateTimeInterface $myDate): array` : Permet d'ajouter une date dans la liste des congés / fériés. Utile pour des fériés locaux, des congés spécifiques à l'applicaiton...
- `addHolidaysForYear(int $year): array` : Ajoute au tableau courant des congés les fériés de l'année demandée.
- `isHolidayListEmpty(): bool` : Renvoie true si la liste des congés est vide / non initialisée
- `emptyHolidays(): void` : Vide la liste des congés.
- `isHoliday(\DateTimeInterface $myDate, string $countryCode = 'FR'): bool` : renvoie true si la date fournie est un jour de congé défini pour le pays. Si le pays fourni ne correspond pas à ceux en cours d'utilisation, le tableau des congés est recalculé avec le nouveau pays.


## Création de fournisseur de jours fériés pour d'autres langues

Pour créer un fournisseur de jours fériés pour une autre langue, vous devez définir une classe et l'annoter avec `AsHolidayProvider`.

### Exemple

```php
<?php

namespace App\HolidayProvider;

use Zhortein\SymfonyToolboxBundle\Annotation\AsHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Provider\HolidayProviderInterface;

#[AsHolidayProvider(country="ES")]
class SpainHolidayProvider implements HolidayProviderInterface
{
    public function getHolidays(int $year): array
    {
        // Retourner un tableau de DateTime représentant les jours fériés en Espagne pour l'année donnée
        return [
            new \DateTime("$year-01-01"), // Nouvel An
            new \DateTime("$year-01-06"), // Épiphanie
            // Ajouter d'autres jours fériés
        ];
    }
}
```

En ajoutant sur la classe l'attribut <code>#[AsHolidayProvider(country="ES")]</code>, elle sera automatiquement reconnue comme fournisseur de jours fériés pour l'Espagne.
