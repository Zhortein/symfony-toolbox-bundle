# ServerMetric

The `ServerMetric` service provides utilities for gathering system metrics directly from the server. This includes metrics like CPU load, disk space usage, and memory consumption, making it particularly useful for monitoring and analyzing server performance within Symfony applications.

## Features

[CPU Load](#1-cpu-load) - [Disk Space Usage](#disk-space-usage) - [Memory Usage](#memory-usage)

### CPU Load

Retrieve the current CPU load as a percentage.

#### Method

```php
public function getServerLoadValue(): ?float
```

Returns the CPU load as a float. On Linux servers, it parses /proc/stat; on Windows, it relies on the wmic command.

#### Example

```php
$cpuLoad = $serverMetric->getServerLoadValue();
echo "CPU Load: " . ($cpuLoad !== null ? $cpuLoad . "%" : "Unavailable");
```

### Disk Space Usage
Check available disk space on a specified directory.

#### Method

```php
public function getDiskFreeSpace(string $directory, string $unit = 'percentage'): ?float
```

Parameters:
- `string $directory`: Directory to check for disk space.
- `string $unit`: Set to 'MB', 'GB', or 'percentage'.

#### Example

```php
$diskUsage = $serverMetric->getDiskFreeSpace('/var/www', 'GB');
echo "Disk Usage: " . ($diskUsage !== null ? $diskUsage . " GB" : "Unavailable");
```

### Memory Usage
Retrieve current memory usage on the server.

#### Method

```php
public function getMemoryUsage(): ?float
```

Returns the memory usage as a percentage. For Windows, it utilizes wmic; for Linux, it reads from /proc/meminfo.

#### Example

```php
$memoryUsage = $serverMetric->getMemoryUsage();
echo "Memory Usage: " . ($memoryUsage !== null ? $memoryUsage . "%" : "Unavailable");
```

## Notes

- **Compatibility**: The class adapts to Windows and Linux OS. MacOS is currently unsupported.
- **Error Handling**: Returns null if the metric is unavailable.