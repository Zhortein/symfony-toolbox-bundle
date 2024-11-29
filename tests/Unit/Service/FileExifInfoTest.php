<?php

namespace Zhortein\SymfonyToolboxBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Zhortein\SymfonyToolboxBundle\Service\FileExifInfo;

class FileExifInfoTest extends TestCase
{
    private FileExifInfo $fileExifInfo;

    protected function setUp(): void
    {
        $this->fileExifInfo = new FileExifInfo();
    }

    public function testGetExifDataWithoutExifExtension(): void
    {
        if (function_exists('exif_read_data')) {
            $this->markTestSkipped('Test skipped because EXIF extension is loaded.');
        }

        $result = $this->fileExifInfo->getExifData('dummy.jpg');
        $this->assertFalse($result);
    }

    public function testGetExifDataWithInvalidFile(): void
    {
        $result = $this->fileExifInfo->getExifData('nonexistent.jpg');
        $this->assertFalse($result);
    }

    public function testGetExifDataWithValidFile(): void
    {
        if (!function_exists('exif_read_data')) {
            $this->markTestSkipped('Test requires the EXIF extension.');
        }

        // Simulate EXIF data using a mock if needed
        $result = $this->fileExifInfo->getExifData(__DIR__.'/sample.jpg'); // Use an actual image with EXIF data for this test
        $this->assertIsArray($result);
    }

    public function testGetGpsPositionWithNoGpsData(): void
    {
        $result = $this->fileExifInfo->getGpsPosition('dummy.jpg');
        $this->assertEmpty($result);
    }

    public function testGetGpsPositionWithValidGpsData(): void
    {
        $exifData = [
            'GPSLatitude' => ['40/1', '26/1', '4632/100'],
            'GPSLongitude' => ['79/1', '58/1', '5/1'],
            'GPSLatitudeRef' => 'N',
            'GPSLongitudeRef' => 'W',
        ];

        // Override getExifData to return mock data
        $fileExifInfoMock = $this->getMockBuilder(FileExifInfo::class)
            ->onlyMethods(['getExifData'])
            ->getMock();

        $fileExifInfoMock->method('getExifData')
            ->willReturn($exifData);

        $result = $fileExifInfoMock->getGpsPosition('dummy.jpg');
        $this->assertEqualsWithDelta(['latitude' => 40.4462, 'longitude' => -79.9681], $result, 0.0001);
    }

    public function testGetGmapGeneratesCorrectHtml(): void
    {
        $latitude = 40.4462;
        $longitude = -79.9681;
        $result = $this->fileExifInfo->getGmap($latitude, $longitude, 800, 600);

        $this->assertStringContainsString('<iframe', $result);
        $this->assertStringContainsString('width="800"', $result);
        $this->assertStringContainsString('height="600"', $result);
        $this->assertStringContainsString("ll=$latitude,$longitude", $result);
    }

    public function testGetCameraInfoWithNoExifData(): void
    {
        $result = $this->fileExifInfo->getCameraInfo('dummy.jpg');
        $this->assertEmpty($result);
    }

    public function testGetCameraInfoWithMakeAndModel(): void
    {
        $exifData = [
            'Make' => 'Canon',
            'Model' => 'EOS 80D',
        ];

        // Override getExifData to return mock data
        $fileExifInfoMock = $this->getMockBuilder(FileExifInfo::class)
            ->onlyMethods(['getExifData'])
            ->getMock();

        $fileExifInfoMock->method('getExifData')
            ->willReturn($exifData);

        $result = $fileExifInfoMock->getCameraInfo('dummy.jpg');
        $this->assertEquals(['make' => 'Canon', 'model' => 'EOS 80D'], $result);
    }
}
