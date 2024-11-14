# Documentation pour `ColorTools`

## Classe `ColorTools`

La classe `ColorTools` fournit diverses méthodes pour manipuler et générer des couleurs.

---

### Propriétés

#### `$predefinedPalette`

```php
private static array $predefinedPalette = [
    '#9400D3', '#FF4500', '#6E0B14', '#095228', '#696969',
    '#00008B', '#B8860B', '#C60800', '#F0E68C', '#008000',
    '#FFD700', '#003366', '#A10684', '#4682B4', '#FFD700',
    '#00FF7F', '#FA8072', '#4B0082', '#DB7093', '#87CEFA',
    '#1FA055', '#FFD700', '#FF1493', '#5A5E6B', '#FFDAB9',
    '#9ACD32', '#00BFFF', '#BF3030', '#FFD700', '#C60800',
    '#00FF7F', '#689D71', '#2C75FF', '#FF00FF', '#F4661B',
    '#FFDAB9', '#FFFF00',
];
```

Une palette de couleurs prédéfinies.

---

### Méthodes

#### `hexToRgb`

```php
public static function hexToRgb(string $hex): array
```

Convertit un code couleur hexadécimal en ses composants RGB.

**Paramètres :**
- `string $hex` : Le code couleur en hexadécimal.

**Retourne :**
- `int[]` : Un tableau contenant les composants RGB.

**Exceptions :**
- `InvalidArgumentException` : Si le code couleur hexadécimal n'est pas valide.

---

#### `rgbToHex`

```php
public static function rgbToHex(array $rgb): string
```

Convertit une couleur RGB en code couleur hexadécimal.

**Paramètres :**
- `int[] $rgb` : Un tableau contenant les composants RGB.

**Retourne :**
- `string` : Le code couleur en hexadécimal.

**Exceptions :**
- `InvalidArgumentException` : Si le tableau RGB n'est pas valide.

---

#### `isValidHexColor`

```php
public static function isValidHexColor(string $color): bool
```

Valide si une chaîne donnée est un code couleur hexadécimal valide.

**Paramètres :**
- `string $color` : La chaîne à valider.

**Retourne :**
- `bool` : `true` si la chaîne est un code couleur hexadécimal valide, sinon `false`.

---

#### `mixColors`

```php
public static function mixColors(string $color1, string $color2): string
```

Mélange deux couleurs et retourne la couleur résultante.

**Paramètres :**
- `string $color1` : Le premier code couleur en hexadécimal.
- `string $color2` : Le second code couleur en hexadécimal.

**Retourne :**
- `string` : Le code couleur en hexadécimal de la couleur mélangée.

---

#### `generateUniqueColors`

```php
public static function generateUniqueColors(int $count): array
```

Génère des couleurs uniques.

**Paramètres :**
- `int $count` : Le nombre de couleurs à générer.

**Retourne :**
- `string[]` : Un tableau de codes couleurs en hexadécimal uniques.

---

#### `isDistinct`

```php
public static function isDistinct(array $rgb, array $colors): bool
```

Vérifie si une couleur est distincte par rapport à un tableau de couleurs existantes.

**Paramètres :**
- `array $rgb` : Les composants RGB de la couleur à vérifier.
- `string[] $colors` : Un tableau de couleurs existantes.

**Retourne :**
- `bool` : `true` si la couleur est distincte, sinon `false`.

---

#### `colorDistance`

```php
public static function colorDistance(array $rgb1, array $rgb2): float
```

Calcule la distance entre deux valeurs de couleurs RGB.

**Paramètres :**
- `array $rgb1` : Les composants RGB de la première couleur.
- `array $rgb2` : Les composants RGB de la deuxième couleur.

**Retourne :**
- `float` : La distance entre les deux couleurs.

---

#### `generateUniqueColorsPairs`

```php
public static function generateUniqueColorsPairs(int $count): array
```

Génère des paires de couleurs uniques (couleurs vives et pâles).

**Paramètres :**
- `int $count` : Le nombre de paires de couleurs à générer.

**Retourne :**
- `array` : Un tableau contenant les paires de couleurs uniques avec les clés `vivid` (couleurs vives) et `pale` (couleurs pâles).

---

Cette documentation fournit un aperçu détaillé des propriétés et des méthodes disponibles dans la classe `ColorTools` pour la manipulation des couleurs dans votre projet Symfony.