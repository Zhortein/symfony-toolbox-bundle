<?php

namespace Zhortein\SymfonyToolboxBundle\Service;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class FileToolBox
{
    public function __construct(private ?Filesystem $filesystem = null)
    {
        $this->filesystem = $filesystem ?? new Filesystem();
    }

    /**
     * Remove the specified directory and its contents.
     *
     * @param string $directory the path to the directory to be removed
     *
     * @throws \RuntimeException if the directory cannot be removed
     */
    public function rmAllDir(string $directory): void
    {
        if (null === $this->filesystem) {
            return;
        }
        if ($this->filesystem->exists($directory)) {
            try {
                $this->filesystem->remove($directory);
            } catch (IOExceptionInterface) {
                // Log or handle the exception
                // @todo Traduire
                throw new \RuntimeException('Could not delete directory');
            }
        }
    }

    /**
     * Read a large file line by line using a generator.
     *
     * @param string $filename Path to the file
     *
     * @return \Generator Yields each line of the file
     */
    public function readHugeRawFile(string $filename): \Generator
    {
        if (null === $this->filesystem) {
            return;
        }

        if (!$this->filesystem->exists($filename) || !is_readable($filename)) {
            // @todo Traduire
            throw new \RuntimeException(sprintf('File %s does not exist or is not readable.', $filename));
        }

        $file = new \SplFileObject($filename, 'rb');
        $file->setFlags(\SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);

        foreach ($file as $row => $line) {
            yield $row + 1 => $line;
        }
    }

    /**
     * Copy the contents of one directory to another.
     *
     * @param string $source      the path of the source directory
     * @param string $destination the path of the destination directory
     *
     * @throws \RuntimeException if the source directory does not exist or if an error occurs during copying
     */
    public function copyDirectory(string $source, string $destination): void
    {
        if (null === $this->filesystem) {
            return;
        }

        if (!$this->filesystem->exists($source)) {
            // @todo Traduire
            throw new \RuntimeException(sprintf('Source directory %s does not exist.', $source));
        }

        try {
            $this->filesystem->mirror($source, $destination);
        } catch (IOExceptionInterface $exception) {
            // @todo Traduire
            throw new \RuntimeException(sprintf('Could not copy directory: %s', $exception->getMessage()));
        }
    }

    /**
     * Determines if the specified directory has the required free space.
     *
     * @param string $directory     the directory to check for free space
     * @param int    $requiredSpace the required space in bytes
     *
     * @return bool returns true if the free space is sufficient, otherwise false
     *
     * @throws \RuntimeException if the free space cannot be determined
     */
    public function hasSufficientSpace(string $directory, int $requiredSpace): bool
    {
        $freeSpace = disk_free_space($directory);
        if (false === $freeSpace) {
            // @todo Traduire
            throw new \RuntimeException(sprintf('Could not determine free space for directory %s', $directory));
        }

        return $freeSpace >= $requiredSpace;
    }
}
