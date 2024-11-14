<?php

namespace Zhortein\SymfonyToolboxBundle\Service;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ServerMetric
{
    private const int BYTES_TO_MB = 1024;
    private const int|float BYTES_TO_GB = 1024 * 1024;
    private const string UNIT_PERCENTAGE = 'percentage';
    private const string UNIT_MB = 'MB';
    private const string UNIT_GB = 'GB';

    /**
     * Execute a system command using Symfony Process and return output lines.
     *
     * @param string $command The command to execute
     *
     * @return array<int, string>|null Array of output lines or null on failure
     */
    private function executeCommand(string $command): ?array
    {
        $process = Process::fromShellCommandline($command);
        try {
            $process->mustRun();

            return explode("\n", trim($process->getOutput()));
        } catch (ProcessFailedException) {
            return null;
        }
    }

    /**
     * Retrieves server CPU load percentage.
     */
    public function getServerLoadValue(): ?float
    {
        return false !== stripos(PHP_OS_FAMILY, 'win') ? $this->getWindowsCpuLoad() : $this->getLinuxCpuLoad();
    }

    private function getWindowsCpuLoad(): ?float
    {
        $cmd = 'wmic cpu get loadpercentage /all';
        @exec($cmd, $output);

        if ($output) {
            foreach ($output as $line) {
                if ($line && preg_match('/^[0-9]+$/', $line)) {
                    return (float) $line;
                }
            }
        }

        return null;
    }

    private function getLinuxCpuLoad(): ?float
    {
        $stat1 = $this->getServerLoadLinuxData();
        sleep(1);
        $stat2 = $this->getServerLoadLinuxData();

        if ($stat1 && $stat2) {
            [$user1, $nice1, $system1, $idle1] = $stat1;
            [$user2, $nice2, $system2, $idle2] = $stat2;
            $cpuTime = ($user2 - $user1) + ($nice2 - $nice1) + ($system2 - $system1) + ($idle2 - $idle1);
            $idleTime = $idle2 - $idle1;

            return 100 * (1 - ($idleTime / $cpuTime));
        }

        return null;
    }

    /**
     * Retrieves CPU load data from the Linux `/proc/stat` file.
     *
     * This function parses the contents of the `/proc/stat` file to extract
     * CPU usage statistics. It returns an array containing the user, nice,
     * system, and idle values if the file is readable and the data is valid,
     * otherwise, it returns null.
     *
     * @return int[]|null
     */
    protected function getServerLoadLinuxData(): ?array
    {
        if (!is_readable('/proc/stat')) {
            return null;
        }

        $stats = @file_get_contents('/proc/stat');
        if (!$stats) {
            return null;
        }

        $stats = preg_replace('/[[:blank:]]+/', ' ', $stats);
        if (null === $stats) {
            return null;
        }
        $lines = explode("\n", str_replace(["\r\n", "\n\r", "\r"], "\n", $stats));

        foreach ($lines as $statLine) {
            $data = explode(' ', trim($statLine));
            if ('cpu' === $data[0] && count($data) >= 5) {
                return [
                    (int) $data[1],
                    (int) $data[2],
                    (int) $data[3],
                    (int) $data[4],
                ];
            }
        }

        return null;
    }

    /**
     * Returns the disk free space for a specified directory in the given unit.
     */
    public function getDiskFreeSpace(string $directory, string $unit = self::UNIT_PERCENTAGE): ?float
    {
        try {
            $free = disk_free_space($directory);
            $total = disk_total_space($directory);

            return match ($unit) {
                self::UNIT_MB => $free / self::BYTES_TO_MB,
                self::UNIT_GB => $free / self::BYTES_TO_GB,
                self::UNIT_PERCENTAGE => $total > 0 ? (1 - ($free / $total)) * 100 : null,
                default => null,
            };
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Retrieves memory usage percentage based on the operating system.
     */
    public function getMemoryUsage(): ?float
    {
        return false !== stripos(PHP_OS_FAMILY, 'win') ? $this->getWindowsMemoryUsage() : $this->getLinuxMemoryUsage();
    }

    private function getWindowsMemoryUsage(): ?float
    {
        $totalMemoryOutput = $this->executeCommand('wmic ComputerSystem GET TotalPhysicalMemory');
        @exec('wmic ComputerSystem GET TotalPhysicalMemory', $totalMemoryOutput);
        if ($totalMemoryOutput) {
            foreach ($totalMemoryOutput as $line) {
                if ($line && preg_match('/^[0-9]+$/', $line)) {
                    $memTotal = (float) $line;
                    break;
                }
            }
        }

        $freeMemoryOutput = $this->executeCommand('wmic OS GET FreePhysicalMemory');
        @exec('wmic OS GET FreePhysicalMemory', $freeMemoryOutput);
        if ($freeMemoryOutput) {
            foreach ($freeMemoryOutput as $line) {
                if ($line && preg_match('/^[0-9]+$/', $line)) {
                    $memFree = ((float) $line) * 1024;
                    break;
                }
            }
        }

        if (isset($memTotal, $memFree)) {
            return $memTotal > 0 ? (1 - ($memFree / $memTotal)) * 100 : null;
        }

        return null;
    }

    private function getLinuxMemoryUsage(): ?float
    {
        if (!is_readable('/proc/meminfo')) {
            return null;
        }

        $memInfo = file('/proc/meminfo');
        if (false === $memInfo) {
            return null;
        }
        $memTotal = $memFree = 0;

        foreach ($memInfo as $line) {
            if (str_starts_with($line, 'MemTotal')) {
                $memTotal = (int) filter_var($line, FILTER_SANITIZE_NUMBER_INT);
            }
            if (str_starts_with($line, 'MemAvailable')) {
                $memFree = (int) filter_var($line, FILTER_SANITIZE_NUMBER_INT);
                break;
            }
        }

        $memUsed = $memTotal - $memFree;

        return $memTotal > 0 ? ($memUsed / $memTotal) * 100 : null;
    }
}
