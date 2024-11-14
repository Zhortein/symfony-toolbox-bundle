<?php

namespace Zhortein\SymfonyToolboxBundle\Service;

class FileExifInfo
{
    /**
     * Verify if the file exists and is an image.
     *
     * @param string $file path to the file
     *
     * @return bool true if the file exists and is an image, false otherwise
     */
    private function isImageFile(string $file): bool
    {
        return file_exists($file) && @is_array(getimagesize($file));
    }

    /**
     * Check if the ext_exif extension is loaded.
     */
    private function isExifExtensionLoaded(): bool
    {
        return function_exists('exif_read_data');
    }

    /**
     * Read exif data from file.
     *
     * @return array<string, mixed>|false Array if exif data was found else false
     */
    public function getExifData(string $file): bool|array
    {
        if (!$this->isExifExtensionLoaded() || !$this->isImageFile($file)) {
            return false;
        }

        $file = str_replace('/', DIRECTORY_SEPARATOR, $file);
        /** @var array<string, mixed>|false $exif */
        $exif = @exif_read_data($file, null, false);

        return is_array($exif) ? $exif : false;
    }

    /**
     * Returns an array with GPS coordinates (latitude, longitude).
     *
     * @param string $file path to the image file
     *
     * @return array<string, float> array with 'latitude' and 'longitude', empty if no GPS data available
     */
    public function getGpsPosition(string $file): array
    {
        $exif = $this->getExifData($file);
        if (false === $exif
            || !isset($exif['GPSLatitude'], $exif['GPSLongitude'], $exif['GPSLatitudeRef'], $exif['GPSLongitudeRef'])
            || !is_array($exif['GPSLatitude'])
            || !is_array($exif['GPSLongitude'])
        ) {
            return [];
        }

        // Latitude and Longitude Multiplier
        $latMultiplier = 'S' === $exif['GPSLatitudeRef'] ? -1 : 1;
        $longMultiplier = 'W' === $exif['GPSLongitudeRef'] ? -1 : 1;

        $value1 = $value2 = $value3 = null;
        foreach (['GPSLatitude', 'GPSLongitude'] as $type) {
            if (!is_array($exif[$type])) {
                return [];
            }

            foreach ($exif[$type] as $key => $value) {
                if (!is_int($key)) {
                    return [];
                }
                if (0 === $key && is_string($value)) {
                    $value1 = $value;
                }
                if (1 === $key && is_string($value)) {
                    $value2 = $value;
                }
                if (2 === $key && is_string($value)) {
                    $value3 = $value;
                }
            }
            if (null === $value1 || null === $value2 || null === $value3) {
                return [];
            }
            if ('GPSLatitude' === $type) {
                $latitude = $latMultiplier * $this->gpsToDecimal($value1, $value2, $value3);
            } else {
                $longitude = $longMultiplier * $this->gpsToDecimal($value1, $value2, $value3);
            }
        }

        return (isset($latitude, $longitude)) ? compact('latitude', 'longitude') : [];
    }

    private function gpsToDecimal(string $gpsDataDegree, string $gpsDataMinutes, string $gpsDataSeconds): float
    {
        $degrees = $this->fractionToFloat($gpsDataDegree);
        $minutes = $this->fractionToFloat($gpsDataMinutes);
        $seconds = $this->fractionToFloat($gpsDataSeconds);

        return $degrees + ($minutes / 60) + ($seconds / 3600);
    }

    /**
     * Convert a fractional GPS coordinate component to a float.
     *
     * @param string $fraction string representation of a fraction
     *
     * @return float decimal value of the fraction
     */
    private function fractionToFloat(string $fraction): float
    {
        $parts = explode('/', $fraction);

        return 2 === count($parts) ? (float) $parts[0] / (float) $parts[1] : (float) $parts[0];
    }

    /**
     * Generate HTML for embedding a Google map with the given coordinates.
     *
     * @param float $lat    latitude
     * @param float $long   longitude
     * @param int   $width  width of the Google map iframe
     * @param int   $height height of the Google map iframe
     *
     * @return string HTML iframe for embedding a Google map
     */
    public function getGmap(float $lat, float $long, int $width = 600, int $height = 350): string
    {
        $latitude = htmlspecialchars((string) $lat, ENT_QUOTES, 'UTF-8');
        $longitude = htmlspecialchars((string) $long, ENT_QUOTES, 'UTF-8');
        $width = max(1, (int) $width); // Prevent invalid width
        $height = max(1, (int) $height); // Prevent invalid height

        return <<<HTML
<div class="google_map">
<iframe 
width="$width" 
height="$height" 
src="https://maps.google.com/?ie=UTF8&amp;hq=&amp;t=h&amp;ll=$latitude,$longitude&amp;spn=0.016643,0.036478&amp;z=14&amp;output=embed"></iframe>
</div>
HTML;
    }

    /**
     * Get camera make and model metadata from the EXIF data.
     *
     * @param string $file path to the image file
     *
     * @return array<string, string> array with 'make' and 'model', or empty if not available
     */
    public function getCameraInfo(string $file): array
    {
        $exif = $this->getExifData($file);
        if (!$exif
            || !isset($exif['Make'], $exif['Model'])
            || !is_string($exif['Make'])
            || !is_string($exif['Model'])
        ) {
            return [];
        }

        return [
            'make' => $exif['Make'],
            'model' => $exif['Model'],
        ];
    }
}
