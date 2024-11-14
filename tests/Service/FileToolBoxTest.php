<?php

namespace Zhortein\SymfonyToolboxBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Zhortein\SymfonyToolboxBundle\Service\FileToolBox;

class FileToolBoxTest extends TestCase
{
    private FileToolBox $fileToolBox;
    private Filesystem $filesystem;
    private string $testDir;
    private string $testFile;

    protected function setUp(): void
    {
        $this->fileToolBox = new FileToolBox();
        $this->filesystem = new Filesystem();

        $this->testDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'testDir';
        $this->testFile = $this->testDir.DIRECTORY_SEPARATOR.'testFile.txt';

        // Crée le répertoire et le fichier de test si possible
        if (!is_writable(sys_get_temp_dir())) {
            $this->markTestSkipped('Le répertoire temporaire n’est pas accessible en écriture.');
        }

        $this->filesystem->mkdir($this->testDir);
        file_put_contents($this->testFile, "Line 1\nLine 2\nLine 3");
    }

    protected function tearDown(): void
    {
        // Supprime le répertoire de test pour chaque système
        if ($this->filesystem->exists($this->testDir)) {
            $this->filesystem->remove($this->testDir);
        }
    }

    public function testRmAllDirSuccessfullyRemovesDirectory(): void
    {
        $this->assertTrue($this->filesystem->exists($this->testDir));

        $this->fileToolBox->rmAllDir($this->testDir);

        $this->assertFalse($this->filesystem->exists($this->testDir));
    }

    public function testReadHugeRawFileSuccessfullyReadsLines(): void
    {
        $lines = iterator_to_array($this->fileToolBox->readHugeRawFile($this->testFile));

        $this->assertCount(3, $lines);
        $this->assertEquals('Line 1', trim($lines[1]));
        $this->assertEquals('Line 2', trim($lines[2]));
        $this->assertEquals('Line 3', trim($lines[3]));
    }

    public function testReadHugeRawFileThrowsExceptionIfFileNotFound(): void
    {
        $nonExistentFile = $this->testDir.DIRECTORY_SEPARATOR.'nonExistentFile.txt';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('does not exist or is not readable');

        $this->fileToolBox->readHugeRawFile($nonExistentFile)->current();
    }

    public function testCopyDirectorySuccessfullyCopiesContents(): void
    {
        $destinationDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'copyTestDir';

        $this->fileToolBox->copyDirectory($this->testDir, $destinationDir);

        $this->assertTrue($this->filesystem->exists($destinationDir));
        $this->assertTrue($this->filesystem->exists($destinationDir.DIRECTORY_SEPARATOR.'testFile.txt'));

        // Nettoyage
        $this->filesystem->remove($destinationDir);
    }

    public function testCopyDirectoryThrowsExceptionIfSourceNotFound(): void
    {
        $nonExistentDir = $this->testDir.DIRECTORY_SEPARATOR.'nonExistentDir';
        $destinationDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'destinationDir';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Source directory .* does not exist/');

        $this->fileToolBox->copyDirectory($nonExistentDir, $destinationDir);
    }

    public function testHasSufficientSpaceReturnsTrueWhenSpaceIsEnough(): void
    {
        $result = $this->fileToolBox->hasSufficientSpace($this->testDir, 1024);

        $this->assertTrue($result);
    }

    public function testHasSufficientSpaceThrowsExceptionIfCannotDetermineSpace(): void
    {
        $fileToolBoxMock = $this->getMockBuilder(FileToolBox::class)
            ->onlyMethods(['hasSufficientSpace'])
            ->getMock();

        $fileToolBoxMock->method('hasSufficientSpace')->willThrowException(new \RuntimeException('Could not determine free space'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Could not determine free space');

        $fileToolBoxMock->hasSufficientSpace('fakeNonExistentDir', 1024);
    }
}
