# DataTypeValidator

## Introduction
The `DataTypeValidator` class allows for type validations (email, URL) independent of Symfony's form validation mechanisms. This is particularly useful for importing data or other contexts where entities are created without passing through forms.

## Constructor
```php
__construct(private ValidatorInterface $validator)
```
The constructor takes an instance of Symfony's `ValidatorInterface`.

## Methods

### emailValid

Validates an email address.

#### Signature
```php
public function emailValid(string $email, string $mode = Assert\Email::VALIDATION_MODE_HTML5): bool
```

#### Parameters
- `email`: The email address to validate.
- `mode`: The validation mode (default is `Assert\Email::VALIDATION_MODE_HTML5`).

#### Returns
- `bool`: `true` if the email is valid, `false` otherwise.

#### Example
```php
$validator = new DataTypeValidator($symfonyValidator);
$isEmailValid = $validator->emailValid('test@example.com'); // true
```

### urlHttpValid

Validates a URL with http and https protocols.

#### Signature
```php
public function urlHttpValid(string $url, bool $requireTld = true): bool
```

#### Parameters
- `url`: The URL to validate.
- `requireTld` : Specify if domain extension is mandatory or not

#### Returns
- `bool`: `true` if the URL is valid, `false` otherwise.

#### Example
```php
$validator = new DataTypeValidator($symfonyValidator);
$isUrlValid = $validator->urlHttpValid('example.com'); // true
$isUrlValid = $validator->urlHttpValid('example'); // false
$isUrlValid = $validator->urlHttpValid('example', false); // true
```

### urlValid

Validates a URL with a list of accepted protocols (http, https, ftp, sftp).

#### Signature
```php
public function urlValid(string $url, array $protocols = ['http', 'https', 'ftp', 'sftp'], bool $requireTld = true): bool
```

#### Parameters
- `url`: The URL to validate.
- `protocols` : Array of accepted protocols.
- `requireTld` : Specify if domain extension is mandatory or not

#### Returns
- `bool`: `true` if the URL is valid, `false` otherwise.

#### Example
```php
$validator = new DataTypeValidator($symfonyValidator);
$isUrlValid = $validator->urlValid('ftp://example.com'); // true
$isUrlValid = $validator->urlValid('https://example', ['http', 'https']); // false
$isUrlValid = $validator->urlValid('https://example', ['http', 'https'], false); // true
$isUrlValid = $validator->urlValid('ftp://example.com', ['http', 'https']); // false
```

### validate

Validates a value with a custom constraint.

#### Signature
```php
private function validate(mixed $value, array $constraints): bool
```

#### Parameters
- `value`: The value to validate.
- `constraints`: An array of constraints to apply to the value.

#### Returns
- `bool`: `true` if the value satisfies all constraints, `false` otherwise.

#### Example
```php
use Symfony\Component\Validator\Constraints as Assert;

$validator = new DataTypeValidator($symfonyValidator);

// Validate a string with a minimum length of 2 and a maximum length of 255
$isValidLength = $validator->validate('example', [new Assert\Length(['min' => 2, 'max' => 255])]); // true

// Validate an integer to check if it's positive or zero
$isPositiveOrZero = $validator->validate(10, [new Assert\PositiveOrZero()]); // true

$isNotValidLength = $validator->validate('e', [new Assert\Length(['min' => 2, 'max' => 255])]); // false
$isNotPositiveOrZero = $validator->validate(-5, [new Assert\PositiveOrZero()]); // false
```

## Using Validations in Entities

Here is how you can use the `DataTypeValidator` class to validate data directly in your entity setters:

### Entity Example
```php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zhortein\SymfonyToolboxBundle\Service\DataTypeValidator;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * @ORM\Entity(repositoryClass=App\Repository\UserRepository::class)
 */
class User
{
    private string $email;

    private string $homepage;

    private int $age;

    private string $username;

    /** @var DataTypeValidator */
    private DataTypeValidator $validator;

    public function __construct(DataTypeValidator $validator)
    {
        $this->validator = $validator;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        if (!$this->validator->emailValid($email)) {
            throw new InvalidArgumentException('Invalid email address.');
        }
        $this->email = $email;

        return $this;
    }

    public function getHomepage(): string
    {
        return $this->homepage;
    }

    public function setHomepage(string $url): self
    {
        if (!$this->validator->urlValid($url)) {
            throw new InvalidArgumentException('Invalid URL.');
        }
        $this->homepage = $url;

        return $this;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        if (!$this->validator->validate($age, [new Assert\PositiveOrZero()])) {
            throw new InvalidArgumentException('Age must be positive or zero.');
        }
        $this->age = $age;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        if (!$this->validator->validate($username, [new Assert\Length(['min' => 2, 'max' => 255])])) {
            throw new InvalidArgumentException('Username must be between 2 and 255 characters.');
        }
        $this->username = $username;

        return $this;
    }
}
```

## Conclusion

The `DataTypeValidator` class allows you to easily validate email addresses, URLs, and other data types throughout your application, whether the data comes from forms or other sources. By using this class, you can ensure the consistency and validity of your data by centralizing your validation rules.
