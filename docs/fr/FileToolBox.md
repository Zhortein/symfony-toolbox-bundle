# Documentation de `FileToolBox`

## Classe `FileToolBox`

La classe `FileToolBox` fournit des outils pour la gestion des fichiers et des répertoires en utilisant le composant Filesystem de Symfony.

### Constructeur

```php
__construct(?Filesystem $filesystem = null)
```

Le constructeur initialise l'objet `FileToolBox`. Si un objet `Filesystem` n'est pas fourni, un nouveau sera créé.

### Méthodes

#### `rmAllDir(string $directory): void`
Supprime le répertoire spécifié et son contenu.

- **Paramètre :**
    - `string $directory`: le chemin vers le répertoire à supprimer.

- **Exception :**
    - `\RuntimeException`: si le répertoire ne peut pas être supprimé.

```php
public function rmAllDir(string $directory): void
```

#### `readHugeRawFile(string $filename): \Generator`
Lire un fichier volumineux ligne par ligne en utilisant un générateur.

- **Paramètre :**
    - `string $filename`: chemin vers le fichier.

- **Retourne :**
    - `\Generator`: génère chaque ligne du fichier.

- **Exception :**
    - `\RuntimeException`: si le fichier n'existe pas ou n'est pas lisible.

```php
public function readHugeRawFile(string $filename): \Generator
```

#### `copyDirectory(string $source, string $destination): void`
Copie le contenu d'un répertoire vers un autre.

- **Paramètres :**
    - `string $source`: chemin du répertoire source.
    - `string $destination`: chemin du répertoire de destination.

- **Exception :**
    - `\RuntimeException`: si le répertoire source n'existe pas ou si une erreur se produit lors de la copie.

```php
public function copyDirectory(string $source, string $destination): void
```

#### `hasSufficientSpace(string $directory, int $requiredSpace): bool`
Détermine si le répertoire spécifié dispose de l'espace libre requis.

- **Paramètres :**
    - `string $directory`: le répertoire à vérifier pour l'espace libre.
    - `int $requiredSpace`: l'espace requis en octets.

- **Retourne :**
    - `bool`: retourne `true` si l'espace libre est suffisant, sinon `false`.

- **Exception :**
    - `\RuntimeException`: si l'espace libre ne peut pas être déterminé.

```php
public function hasSufficientSpace(string $directory, int $requiredSpace): bool
```

### Exemple d'utilisation

```php
use Symfony\Component\Filesystem\Filesystem;
use Zhortein\SymfonyToolboxBundle\Service\FileToolBox;

$filesystem = new Filesystem();
$fileToolBox = new FileToolBox($filesystem);

try {
    $fileToolBox->rmAllDir('/chemin/vers/repertoire');
    $fileToolBox->copyDirectory('/source/repertoire', '/destination/repertoire');

    foreach ($fileToolBox->readHugeRawFile('/chemin/vers/fichier') as $lineNumber => $line) {
        echo "Ligne $lineNumber: $line\n";
    }

    if ($fileToolBox->hasSufficientSpace('/chemin/vers/repertoire', 1048576)) {
        echo "Espace suffisant.\n";
    } else {
        echo "Espace insuffisant.\n";
    }
} catch (\RuntimeException $e) {
    echo 'Erreur: ' . $e->getMessage();
}
```