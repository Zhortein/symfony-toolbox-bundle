# Class Documentation: FileExifInfo

## Overview
The `FileExifInfo` class is a service that allows manipulation of EXIF data from an image file.
It provides functionalities to check the existence of an image, read EXIF metadata, extract GPS coordinates,
generate a Google Maps link based on these coordinates, and retrieve device information (make and model) from the metadata.

## Methods

### 1. `isImageFile(string $file): bool`
**Description**: Checks whether the specified file exists and is indeed an image.

**Parameter**:
- `$file` (string) - Path to the file.

**Returns**:
- `bool` - Returns `true` if the file exists and is an image, otherwise `false`.

---

### 2. `isExifExtensionLoaded(): bool`
**Description**: Checks if the EXIF extension is enabled.

**Returns**:
- `bool` - Returns `true` if the EXIF extension is loaded, otherwise `false`.

---

### 3. `getExifData(string $file): bool|array`
**Description**: Reads the EXIF data from an image file.

**Parameter**:
- `$file` (string) - Path to the file.

**Returns**:
- `array<string, mixed>` - Array of EXIF data if available.
- `false` - If EXIF data is not available or the image is not valid.

---

### 4. `getGpsPosition(string $file): array`
**Description**: Extracts the GPS coordinates from an image, if available, and returns them in decimal format.

**Parameter**:
- `$file` (string) - Path to the image file.

**Returns**:
- `array<string, float>` - Array with keys `latitude` and `longitude` (or an empty array if GPS data is not available).

---

### 5. `gpsToDecimal(array $gpsData): float`
**Description**: Converts GPS data from EXIF format to decimal format.

**Parameter**:
- `$gpsData` (array<int, string>) - Array containing degrees, minutes, and seconds.

**Returns**:
- `float` - The decimal degree representation of the GPS coordinates.

---

### 6. `fractionToFloat(string $fraction): float`
**Description**: Converts a fraction in GPS format to a decimal number.

**Parameter**:
- `$fraction` (string) - String representation of the fraction (e.g., `40/1`).

**Returns**:
- `float` - Decimal value of the fraction.

---

### 7. `getGmap(float $lat, float $long, int $width = 600, int $height = 350): string`
**Description**: Generates HTML code to embed a Google Map with the provided coordinates.

**Parameters**:
- `$lat` (float) - Latitude.
- `$long` (float) - Longitude.
- `$width` (int) - Map width (default: 600).
- `$height` (int) - Map height (default: 350).

**Returns**:
- `string` - HTML code of the Google Maps iframe.

---

### 8. `getCameraInfo(string $file): array`
**Description**: Retrieves the camera make (`Make`) and model (`Model`) from the EXIF data.

**Parameter**:
- `$file` (string) - Path to the image file.

**Returns**:
- `array<string, string>` - Array containing keys `make` and `model`, or an empty array if the information is not available.

## Example Usage

```php
use Zhortein\\SymfonyToolboxBundle\\Service\\FileExifInfo;

$fileExifInfo = new FileExifInfo();
$file = 'path/to/image.jpg';

// Check if the file is an image
if ($fileExifInfo->isImageFile($file)) {
    // Retrieve EXIF data
    $exifData = $fileExifInfo->getExifData($file);

    // Extract GPS position
    $gpsPosition = $fileExifInfo->getGpsPosition($file);

    // Retrieve camera information
    $cameraInfo = $fileExifInfo->getCameraInfo($file);

    // Generate a Google Maps map
    echo $fileExifInfo->getGmap($gpsPosition['latitude'], $gpsPosition['longitude']);
}
```