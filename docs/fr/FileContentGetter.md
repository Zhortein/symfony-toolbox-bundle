# Documentation de la classe `FileContentGetter`

## Namespace

```php
namespace Zhortein\SymfonyToolboxBundle\Service;
```

## Importations

```php
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zhortein\SymfonyToolboxBundle\Enum\Action;
```

## Classe `FileContentGetter`

### Constantes

- `public const int DEFAULT_HTTPCLIENT_TIMEOUT = 1800;`

### Propriétés

- `protected HttpClientInterface $httpClient;`
- `protected static int $timeout = self::DEFAULT_HTTPCLIENT_TIMEOUT;`
- `protected readonly DataTypeValidator $validator;`
- `protected readonly TranslatorInterface $translator;`
- `protected ?LoggerInterface $logger = null;`

### Constructeur

```php
public function __construct(
    protected readonly DataTypeValidator $validator,
    protected readonly TranslatorInterface $translator,
    protected ?LoggerInterface $logger = null,
    ?HttpClientInterface $httpClient = null,
)
```

### Méthodes

#### `setLogger`

```php
public function setLogger(LoggerInterface $logger): void
```

- **Description** : Définit le logger.
- **Paramètres** :
  - `LoggerInterface $logger` : Le logger à utiliser.

#### `getLogger`

```php
public function getLogger(): ?LoggerInterface
```

- **Description** : Récupère le logger.
- **Retourne** : Le logger ou null s'il n'est pas défini.

#### `setTimeout`

```php
public static function setTimeout(int $timeout = self::DEFAULT_HTTPCLIENT_TIMEOUT): void
```

- **Description** : Définit le délai d'attente.
- **Paramètres** :
  - `int $timeout` : Le délai d'attente en secondes.

#### `getTimeout`

```php
public static function getTimeout(): int
```

- **Description** : Récupère le délai d'attente.
- **Retourne** : Le délai d'attente en secondes.

#### `getContentFromUrl`

```php
public function getContentFromUrl(string $contentType, ?string $url, bool $modeRaw = false, ?string $token = null): array|string|false
```

- **Description** : Récupère le contenu d'une URL.
- **Paramètres** :
  - `string $contentType` : Le type de contenu.
  - `?string $url` : L'URL à récupérer.
  - `bool $modeRaw` : Mode brut ou non.
  - `?string $token` : Le jeton d'authentification.
- **Retourne** : Le contenu sous forme de tableau (JSON décodé), de chaîne brute, ou `false` en cas d'échec.
- **Exceptions** :
  - `\InvalidArgumentException` : Si l'URL est invalide.
  - `TransportExceptionInterface|\Exception|\Throwable` : En cas d’erreur lors de la requête.

#### `getJsonContentFromUrl`

```php
public function getJsonContentFromUrl(string $contentType, ?string $url, ?string $token = null): array|string|false
```

- **Description** : Récupère le contenu JSON d'une URL.
- **Paramètres** :
  - `string $contentType` : Le type de contenu.
  - `?string $url` : L'URL à récupérer.
  - `?string $token` : Le jeton d'authentification.
- **Retourne** : Le contenu sous forme de tableau (JSON décodé) ou `false` en cas d'échec.

#### `getRawContentFromUrl`

```php
public function getRawContentFromUrl(string $contentType, ?string $url, ?string $token = null): array|string|false
```

- **Description** : Récupère le contenu brut d'une URL.
- **Paramètres** :
  - `string $contentType` : Le type de contenu.
  - `?string $url` : L'URL à récupérer.
  - `?string $token` : Le jeton d'authentification.
- **Retourne** : Le contenu brut sous forme de chaîne ou `false` en cas d'échec.

#### `getAndSaveRawContentFromUrl`

```php
public function getAndSaveRawContentFromUrl(string $contentType, ?string $url, string $downloadedFilename, ?object $object = null, ?string $token = null): bool
```

- **Description** : Récupère le contenu brut d'une URL et le sauvegarde localement.
- **Paramètres** :
  - `string $contentType` : Le type de contenu.
  - `?string $url` : L'URL à récupérer.
  - `string $downloadedFilename` : Le nom du fichier où le contenu sera sauvegardé.
  - `?object $object` : Un objet optionnel associé.
  - `?string $token` : Le jeton d'authentification.
- **Retourne** : `true` en cas de succès, `false` en cas d'échec.