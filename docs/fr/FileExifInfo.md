# Documentation de la classe FileExifInfo

## Vue d'ensemble
La classe `FileExifInfo` est un service qui permet de manipuler les données EXIF d'un fichier image.
Elle fournit des fonctionnalités pour vérifier l'existence d'une image, lire les métadonnées EXIF, extraire les coordonnées GPS,
générer un lien Google Maps basé sur ces coordonnées et récupérer les informations de l'appareil (marque et modèle) à partir des métadonnées.

## Méthodes

### 1. `isImageFile(string $file): bool`
**Description** : Vérifie si le fichier spécifié existe et est bien une image.

**Paramètre** :
- `$file` (string) - Chemin vers le fichier.

**Retour** :
- `bool` - Renvoie `true` si le fichier existe et est une image, sinon `false`.

---

### 2. `isExifExtensionLoaded(): bool`
**Description** : Vérifie si l'extension EXIF est activée.

**Retour** :
- `bool` - Renvoie `true` si l'extension EXIF est chargée, sinon `false`.

---

### 3. `getExifData(string $file): bool|array`
**Description** : Lit les données EXIF d'un fichier image.

**Paramètre** :
- `$file` (string) - Chemin vers le fichier.

**Retour** :
- `array<string, mixed>` - Tableau des données EXIF si elles sont disponibles.
- `false` - Si les données EXIF ne sont pas disponibles ou si l'image n'est pas valide.

---

### 4. `getGpsPosition(string $file): array`
**Description** : Extrait les coordonnées GPS d'une image, si disponibles, et les retourne en format décimal.

**Paramètre** :
- `$file` (string) - Chemin vers le fichier image.

**Retour** :
- `array<string, float>` - Tableau avec les clés `latitude` et `longitude` (ou un tableau vide si les données GPS ne sont pas disponibles).

---

### 5. `gpsToDecimal(array $gpsData): float`
**Description** : Convertit les données GPS du format EXIF en format décimal.

**Paramètre** :
- `$gpsData` (array<int, string>) - Tableau contenant les degrés, minutes et secondes.

**Retour** :
- `float` - La représentation en degrés décimaux des coordonnées GPS.

---

### 6. `fractionToFloat(string $fraction): float`
**Description** : Convertit une fraction en format GPS en un nombre décimal.

**Paramètre** :
- `$fraction` (string) - Représentation en chaîne de la fraction (ex. `40/1`).

**Retour** :
- `float` - Valeur décimale de la fraction.

---

### 7. `getGmap(float $lat, float $long, int $width = 600, int $height = 350): string`
**Description** : Génère un code HTML pour intégrer une carte Google Maps avec les coordonnées fournies.

**Paramètres** :
- `$lat` (float) - Latitude.
- `$long` (float) - Longitude.
- `$width` (int) - Largeur de la carte (par défaut : 600).
- `$height` (int) - Hauteur de la carte (par défaut : 350).

**Retour** :
- `string` - Code HTML de l'iframe Google Maps.

---

### 8. `getCameraInfo(string $file): array`
**Description** : Récupère la marque (`Make`) et le modèle (`Model`) de l'appareil photo à partir des données EXIF.

**Paramètre** :
- `$file` (string) - Chemin vers le fichier image.

**Retour** :
- `array<string, string>` - Tableau contenant les clés `make` et `model`, ou un tableau vide si les informations ne sont pas disponibles.

## Exemple d'utilisation

```php
use Zhortein\\SymfonyToolboxBundle\\Service\\FileExifInfo;

$fileExifInfo = new FileExifInfo();
$file = 'path/to/image.jpg';

// Vérifier si le fichier est une image
if ($fileExifInfo->isImageFile($file)) {
    // Récupérer les données EXIF
    $exifData = $fileExifInfo->getExifData($file);

    // Extraire la position GPS
    $gpsPosition = $fileExifInfo->getGpsPosition($file);

    // Récupérer les informations de l'appareil photo
    $cameraInfo = $fileExifInfo->getCameraInfo($file);

    // Générer une carte Google Maps
    echo $fileExifInfo->getGmap($gpsPosition['latitude'], $gpsPosition['longitude']);
}
