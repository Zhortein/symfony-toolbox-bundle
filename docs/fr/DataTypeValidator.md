# DataTypeValidator

## Introduction
La classe `DataTypeValidator` permet d'effectuer des validations de type (email, URL) de manière indépendante des mécanismes de validation des formulaires Symfony. Cela est particulièrement utile lors de l'importation de données ou dans tout autre contexte où les entités sont créées sans passer par des formulaires.

## Constructeur
```php
__construct(private ValidatorInterface $validator)
```
Le constructeur prend une instance de `ValidatorInterface` de Symfony en paramètre.

## Méthodes

### emailValid

Valide une adresse email.

#### Signature
```php
public function emailValid(string $email, string $mode = Assert\Email::VALIDATION_MODE_HTML5): bool
```

#### Paramètres
- `email` : L'adresse email à valider.
- `mode` : Le mode de validation (par défaut `Assert\Email::VALIDATION_MODE_HTML5`).

#### Retourne
- `bool` : `true` si l'email est valide, `false` sinon.

#### Exemple
```php
$validator = new DataTypeValidator($symfonyValidator);
$isEmailValid = $validator->emailValid('test@example.com'); // true
```

### urlHttpValid

Valide une URL avec les protocoles http et https.

#### Signature
```php
public function urlHttpValid(string $url, bool $requireTld = true): bool
```

#### Paramètres
- `url` : L'URL à valider.
- `requireTld` : Indique si l'extension de domaine est ou non obligatoire

#### Retourne
- `bool` : `true` si l'URL est valide, `false` sinon.

#### Exemple
```php
$validator = new DataTypeValidator($symfonyValidator);
$isUrlValid = $validator->urlHttpValid('example.com'); // true
$isUrlValid = $validator->urlHttpValid('example'); // false
$isUrlValid = $validator->urlHttpValid('example', false); // true
```

### urlValid

Valide une URL avec une liste de protocoles acceptés (http, https, ftp, sftp).

#### Signature
```php
public function urlValid(string $url, array $protocols = ['http', 'https', 'ftp', 'sftp'], bool $requireTld = true): bool
```

#### Paramètres
- `url` : L'URL à valider.
- `protocols` : Tableau des protocoles acceptés.
- `requireTld` : Indique si l'extension de domaine est ou non obligatoire.

#### Retourne
- `bool` : `true` si l'URL est valide, `false` sinon.

#### Exemple
```php
$validator = new DataTypeValidator($symfonyValidator);
$isUrlValid = $validator->urlValid('ftp://example.com'); // true
$isUrlValid = $validator->urlValid('https://example', ['http', 'https']); // false
$isUrlValid = $validator->urlValid('https://example', ['http', 'https'], false); // true
$isUrlValid = $validator->urlValid('ftp://example.com', ['http', 'https']); // false
```

### validate

Valide une valeur avec une contrainte personnalisée.

#### Signature
```php
private function validate(mixed $value, array $constraints): bool
```

#### Paramètres
- `value` : La valeur à valider.
- `constraints` : Un tableau de contraintes à appliquer sur la valeur.

#### Retourne
- `bool` : `true` si la valeur satisfait toutes les contraintes, `false` sinon.

#### Exemple
```php
use Symfony\Component\Validator\Constraints as Assert;

$validator = new DataTypeValidator($symfonyValidator);

// Valider une chaîne avec une longueur minimale de 2 et maximale de 255
$isValidLength = $validator->validate('exemple', [new Assert\Length(['min' => 2, 'max' => 255])]); // true

// Valider un entier pour vérifier s'il est positif ou zéro
$isPositiveOrZero = $validator->validate(10, [new Assert\PositiveOrZero()]); // true

$isNotValidLength = $validator->validate('e', [new Assert\Length(['min' => 2, 'max' => 255])]); // false
$isNotPositiveOrZero = $validator->validate(-5, [new Assert\PositiveOrZero()]); // false
```

## Employez les Validations dans les Entités

Voici comment vous pouvez utiliser la classe `DataTypeValidator` pour valider les données directement dans les setters de vos entités :

### Exemple d'Entité
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
        this->homepage = $url;

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
        $this->$age = $age;

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

La classe `DataTypeValidator` vous permet de valider facilement les adresses email, les URL, et d'autres types de données tout au long de votre application, qu'il s'agisse de données provenant de formulaires ou d'autres sources. En utilisant cette classe, vous pouvez assurer la cohérence et la validité de vos données en centralisant vos règles de validation.
