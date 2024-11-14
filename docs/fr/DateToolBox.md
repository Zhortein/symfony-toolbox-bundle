
# Documentation pour `DateToolBox`

## Classe `DateToolBox`

La classe `DateToolBox` fournit diverses méthodes pour manipuler les dates, gérer les fuseaux horaires, et manipuler les noms de jours localisés en utilisant des fonctionnalités avancées comme `IntlDateFormatter`. Elle permet également de définir un logger pour enregistrer des messages de diagnostic.

---

### Propriétés

#### `$timezone`

```php
protected static ?\DateTimeZone $timezone = null;
```

Stocke le fuseau horaire actuellement défini pour l’application. Par défaut, `Europe/Paris` est utilisé si aucun fuseau horaire n'est spécifié.

#### `$logger`

```php
protected static ?LoggerInterface $logger = null;
```

Stocke une instance de `LoggerInterface` pour la gestion des messages de journalisation (logs).

---

### Méthodes

#### `setLogger`

```php
public static function setLogger(LoggerInterface $logger): void
```

Définit le logger pour enregistrer les messages de diagnostic.

- **logger** : Instance de `LoggerInterface` utilisée pour les logs.

#### `getLogger`

```php
public static function getLogger(): ?LoggerInterface
```

Récupère l’instance actuelle de logger.

- **Retourne** : Le logger défini ou `null` s'il n'est pas configuré.

#### `logWarning`

```php
private static function logWarning(string $message): void
```

Méthode privée pour enregistrer un message d’avertissement avec un format prédéfini.

- **message** : Le message d’avertissement à enregistrer.

#### `setTimeZone`

```php
public static function setTimeZone(\DateTimeZone|string $timezone): void
```

Définit le fuseau horaire de l’application. Si un identifiant de chaîne est fourni, il est converti en `DateTimeZone`.

- **timezone** : Le fuseau horaire sous forme d’objet `DateTimeZone` ou d’identifiant de chaîne.

#### `getTimeZone`

```php
public static function getTimeZone(): \DateTimeZone
```

Récupère le fuseau horaire actuel de l’application. Utilise `Europe/Paris` si aucun fuseau n’a été défini.

- **Retourne** : L’instance de `DateTimeZone` correspondant au fuseau horaire actuel.

#### `getDateFromExcel`

```php
public static function getDateFromExcel(mixed $excelDate): ?\DateTime
```

Convertit une date Excel en objet `DateTime`.

- **excelDate** : La date Excel à convertir.
- **Retourne** : L’objet `DateTime` converti ou `null` si la conversion échoue.

#### `getDayEnumFromName`

```php
public static function getDayEnumFromName(string $name, string $locale = 'fr'): ?Day
```

Convertit un nom de jour localisé en son équivalent `Day` (BackedEnum).

- **name** : Le nom localisé du jour.
- **locale** : La langue utilisée pour la conversion. Par défaut, `fr`.
- **Retourne** : L’enum `Day` correspondant ou `null` si aucun jour ne correspond.

#### `getLastMonthsList`

```php
public static function getLastMonthsList(int $nbMonths, string $format = 'n/Y'): array
```

Génère une liste des N derniers mois dans un format spécifique.

- **nbMonths** : Le nombre de mois à inclure dans la liste.
- **format** : Le format d’affichage des mois (par défaut `n/Y`).
- **Retourne** : Un tableau des derniers mois dans l’ordre inverse.

#### `getLastMonthsListBetween`

```php
public static function getLastMonthsListBetween(\DateTimeInterface $start, \DateTimeInterface $end, string $format = 'n/Y'): array
```

Génère une liste de mois entre deux dates.

- **start** : La date de début.
- **end** : La date de fin.
- **format** : Le format des mois (par défaut `n/Y`).
- **Retourne** : Un tableau des mois formatés entre les deux dates.

#### `getMonthsListBetweenDates`

```php
public static function getMonthsListBetweenDates(?\DateTimeInterface $dateStart = null, ?\DateTimeInterface $dateEnd = null, string $format = 'n/Y'): array
```

Génère une liste des mois entre deux dates dans un ordre chronologique inverse.

- **dateStart** : La date de début (par défaut, date actuelle).
- **dateEnd** : La date de fin (par défaut, retourne un tableau vide si non définie).
- **format** : Le format des mois (par défaut `n/Y`).
- **Retourne** : Un tableau de mois formatés entre les deux dates, dans un ordre chronologique inverse.

---
