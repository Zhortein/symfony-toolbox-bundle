# Documentation for `FileToolBox`

## Class `FileToolBox`

The `FileToolBox` class provides tools for managing files and directories using the Symfony Filesystem component.

### Constructor

```php
__construct(?Filesystem $filesystem = null)
```

The constructor initializes the `FileToolBox` object. If a `Filesystem` object is not provided, a new one will be created.

### Methods

#### `rmAllDir(string $directory): void`
Removes the specified directory and its contents.

- **Parameter:**
    - `string $directory`: the path to the directory to be removed.

- **Exception:**
    - `\RuntimeException`: if the directory cannot be removed.

```php
public function rmAllDir(string $directory): void
```

#### `readHugeRawFile(string $filename): \Generator`
Reads a large file line by line using a generator.

- **Parameter:**
    - `string $filename`: path to the file.

- **Returns:**
    - `\Generator`: yields each line of the file.

- **Exception:**
    - `\RuntimeException`: if the file does not exist or is not readable.

```php
public function readHugeRawFile(string $filename): \Generator
```

#### `copyDirectory(string $source, string $destination): void`
Copies the contents of one directory to another.

- **Parameters:**
    - `string $source`: path of the source directory.
    - `string $destination`: path of the destination directory.

- **Exception:**
    - `\RuntimeException`: if the source directory does not exist or if an error occurs during copying.

```php
public function copyDirectory(string $source, string $destination): void
```

#### `hasSufficientSpace(string $directory, int $requiredSpace): bool`
Determines if the specified directory has the required free space.

- **Parameters:**
    - `string $directory`: the directory to check for free space.
    - `int $requiredSpace`: the required space in bytes.

- **Returns:**
    - `bool`: returns `true` if the free space is sufficient, otherwise `false`.

- **Exception:**
    - `\RuntimeException`: if the free space cannot be determined.

```php
public function hasSufficientSpace(string $directory, int $requiredSpace): bool
```

### Usage Example

```php
use Symfony\Component\Filesystem\Filesystem;
use Zhortein\SymfonyToolboxBundle\Service\FileToolBox;

$filesystem = new Filesystem();
$fileToolBox = new FileToolBox($filesystem);

try {
    $fileToolBox->rmAllDir('/path/to/directory');
    $fileToolBox->copyDirectory('/source/directory', '/destination/directory');

    foreach ($fileToolBox->readHugeRawFile('/path/to/file') as $lineNumber => $line) {
        echo "Line $lineNumber: $line\n";
    }

    if ($fileToolBox->hasSufficientSpace('/path/to/directory', 1048576)) {
        echo "Sufficient space.\n";
    } else {
        echo "Insufficient space.\n";
    }
} catch (\RuntimeException $e) {
    echo 'Error: ' . $e->getMessage();
}
```