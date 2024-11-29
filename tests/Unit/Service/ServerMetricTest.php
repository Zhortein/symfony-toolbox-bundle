<?php

namespace Zhortein\SymfonyToolboxBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Zhortein\SymfonyToolboxBundle\Service\ServerMetric;

class ServerMetricTest extends TestCase
{
    private ServerMetric $serverMetric;

    protected function setUp(): void
    {
        $this->serverMetric = new ServerMetric();
    }

    public function testGetServerLoadValueForLinux(): void
    {
        if (false === stripos(PHP_OS_FAMILY, 'Linux')) {
            $this->markTestSkipped('This test is specific to Linux.');
        }

        $result = $this->serverMetric->getServerLoadValue();
        $this->assertIsFloat($result);
        $this->assertGreaterThanOrEqual(0, $result);
        $this->assertLessThanOrEqual(100, $result);
    }

    public function testGetServerLoadValueForWindows(): void
    {
        if (false === stripos(PHP_OS_FAMILY, 'WIN')) {
            $this->markTestSkipped('This test is specific to Windows.');
        }

        $result = $this->serverMetric->getServerLoadValue();
        $this->assertIsFloat($result);
        $this->assertGreaterThanOrEqual(0, $result);
        $this->assertLessThanOrEqual(100, $result);
    }

    public function testGetDiskFreeSpaceInPercentage(): void
    {
        $directory = sys_get_temp_dir();
        $result = $this->serverMetric->getDiskFreeSpace($directory);
        $this->assertIsFloat($result);
        $this->assertGreaterThanOrEqual(0, $result);
        $this->assertLessThanOrEqual(100, $result);
    }

    public function testGetDiskFreeSpaceInMb(): void
    {
        $directory = sys_get_temp_dir();
        $result = $this->serverMetric->getDiskFreeSpace($directory, 'MB');
        $this->assertIsFloat($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function testGetDiskFreeSpaceInGb(): void
    {
        $directory = sys_get_temp_dir();
        $result = $this->serverMetric->getDiskFreeSpace($directory, 'GB');
        $this->assertIsFloat($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function testGetMemoryUsageForLinux(): void
    {
        if (false === stripos(PHP_OS_FAMILY, 'Linux')) {
            $this->markTestSkipped('This test is specific to Linux.');
        }

        $result = $this->serverMetric->getMemoryUsage();
        $this->assertIsFloat($result);
        $this->assertGreaterThanOrEqual(0, $result);
        $this->assertLessThanOrEqual(100, $result);
    }

    public function testGetMemoryUsageForWindows(): void
    {
        if (false === stripos(PHP_OS_FAMILY, 'WIN')) {
            $this->markTestSkipped('This test is specific to Windows.');
        }

        $result = $this->serverMetric->getMemoryUsage();
        $this->assertIsFloat($result);
        $this->assertGreaterThanOrEqual(0, $result);
        $this->assertLessThanOrEqual(100, $result);
    }

    public function testExecuteCommand(): void
    {
        $method = new \ReflectionMethod($this->serverMetric, 'executeCommand');
        $method->setAccessible(true);

        $command = 'echo test';
        $output = $method->invoke($this->serverMetric, $command);

        $this->assertIsArray($output);
        $this->assertContains('test', $output);
    }

    public function testGetServerLoadLinuxData(): void
    {
        if (false === stripos(PHP_OS_FAMILY, 'Linux')) {
            $this->markTestSkipped('This test is specific to Linux.');
        }

        $method = new \ReflectionMethod($this->serverMetric, 'getServerLoadLinuxData');
        $method->setAccessible(true);
        $data = $method->invoke($this->serverMetric);

        $this->assertIsArray($data);
        $this->assertCount(4, $data);
        foreach ($data as $value) {
            $this->assertIsInt($value);
        }
    }
}
