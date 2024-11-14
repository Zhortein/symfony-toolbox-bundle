# Documentation for `FileContentGetter`

## Namespace

```php
namespace Zhortein\SymfonyToolboxBundle\Service;
```

## Imports

```php
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zhortein\SymfonyToolboxBundle\Enum\Action;
```

## Class `FileContentGetter`

### Constants

- `public const int DEFAULT_HTTPCLIENT_TIMEOUT = 1800;`

### Properties

- `protected HttpClientInterface $httpClient;`
- `protected static int $timeout = self::DEFAULT_HTTPCLIENT_TIMEOUT;`
- `protected readonly DataTypeValidator $validator;`
- `protected readonly TranslatorInterface $translator;`
- `protected ?LoggerInterface $logger = null;`

### Constructor

```php
public function __construct(
    protected readonly DataTypeValidator $validator,
    protected readonly TranslatorInterface $translator,
    protected ?LoggerInterface $logger = null,
    ?HttpClientInterface $httpClient = null,
)
```

- **Parameters:**
    - `DataTypeValidator $validator`: The data type validator.
    - `TranslatorInterface $translator`: The translator.
    - `?LoggerInterface $logger`: The logger (optional).
    - `?HttpClientInterface $httpClient`: The HTTP client (optional).

### Methods

#### `setLogger`

```php
public function setLogger(LoggerInterface $logger): void
```

- **Description**: Sets the logger.
- **Parameters**:
    - `LoggerInterface $logger`: The logger to use.

#### `getLogger`

```php
public function getLogger(): ?LoggerInterface
```

- **Description**: Retrieves the logger.
- **Returns**: The logger or null if not set.

#### `setTimeout`

```php
public static function setTimeout(int $timeout = self::DEFAULT_HTTPCLIENT_TIMEOUT): void
```

- **Description**: Sets the timeout.
- **Parameters**:
    - `int $timeout`: The timeout duration in seconds.

#### `getTimeout`

```php
public static function getTimeout(): int
```

- **Description**: Gets the timeout.
- **Returns**: The timeout duration in seconds.

#### `getContentFromUrl`

```php
public function getContentFromUrl(string $contentType, ?string $url, bool $modeRaw = false, ?string $token = null): array|string|false
```

- **Description**: Retrieves content from a URL.
- **Parameters**:
    - `string $contentType`: The type of content.
    - `?string $url`: The URL to fetch.
    - `bool $modeRaw`: Whether to fetch raw content or not.
    - `?string $token`: The authentication token (optional).
- **Returns**: The content as an array (decoded JSON) or raw string, or false on failure.
- **Exceptions**:
    - `\InvalidArgumentException`: If the URL is invalid.
    - `TransportExceptionInterface|\Exception|\Throwable`: On request error.

#### `getJsonContentFromUrl`

```php
public function getJsonContentFromUrl(string $contentType, ?string $url, ?string $token = null): array|string|false
```

- **Description**: Retrieves JSON content from a URL.
- **Parameters**:
    - `string $contentType`: The type of content.
    - `?string $url`: The URL to fetch.
    - `?string $token`: The authentication token (optional).
- **Returns**: The content as an array (decoded JSON) or false on failure.

#### `getRawContentFromUrl`

```php
public function getRawContentFromUrl(string $contentType, ?string $url, ?string $token = null): array|string|false
```

- **Description**: Retrieves raw content from a URL.
- **Parameters**:
    - `string $contentType`: The type of content.
    - `?string $url`: The URL to fetch.
    - `?string $token`: The authentication token (optional).
- **Returns**: The raw content as a string or false on failure.

#### `getAndSaveRawContentFromUrl`

```php
public function getAndSaveRawContentFromUrl(string $contentType, ?string $url, string $downloadedFilename, ?object $object = null, ?string $token = null): bool
```

- **Description**: Fetches raw content from a URL and saves it locally.
- **Parameters**:
    - `string $contentType`: The type of content.
    - `?string $url`: The URL to fetch.
    - `string $downloadedFilename`: The filename to save the content to.
    - `?object $object`: An optional associated object.
    - `?string $token`: The authentication token (optional).
- **Returns**: True on success, false on failure.