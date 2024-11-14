# ServerMetric

Le service `ServerMetric` fournit des utilitaires pour collecter des métriques système directement depuis le serveur. Cela inclut des métriques telles que la charge CPU, l'utilisation de l'espace disque, et la consommation de mémoire, ce qui le rend particulièrement utile pour surveiller et analyser la performance des serveurs dans les applications Symfony.

## Fonctionnalités

[Charge CPU](#charge-cpu) - [Utilisation de l'espace disque](#utilisation-de-lespace-disque) - [Utilisation de la mémoire](#utilisation-de-la-mémoire)

### Charge CPU

Récupère la charge actuelle du processeur en pourcentage.

#### Méthode

```php
public function getServerLoadValue(): ?float
```

Renvoie la charge CPU sous forme de valeur flottante. Sur les serveurs Linux, elle analyse /proc/stat ; sur Windows, elle s'appuie sur la commande wmic.
#### Exemple

```php
$cpuLoad = $serverMetric->getServerLoadValue();
echo "Charge CPU : " . ($cpuLoad !== null ? $cpuLoad . "%" : "Unavailable");
```

### Utilisation de l'espace disque
Vérifie l'espace disque disponible sur un répertoire spécifié.

#### Méthode

```php
public function getDiskFreeSpace(string $directory, string $unit = 'percentage'): ?float
```

Paramètres :
- `string $directory`: Répertoire à vérifier pour l'espace disque.
- `string $unit`: Définit l'unité en 'MB', 'GB' ou 'percentage'.

#### Exemple

```php
$diskUsage = $serverMetric->getDiskFreeSpace('/var/www', 'GB');
echo "Utilisation de l'espace disque : " . ($diskUsage !== null ? $diskUsage . " GB" : "Unavailable");
```

### Utilisation de la mémoire
Récupère l'utilisation actuelle de la mémoire sur le serveur.

#### Méthode

```php
public function getMemoryUsage(): ?float
```

Renvoie l'utilisation de la mémoire en pourcentage. Pour Windows, il utilise wmic ; pour Linux, il lit depuis /proc/meminfo.

#### Exemple

```php
$memoryUsage = $serverMetric->getMemoryUsage();
echo "Utilisation de la mémoire : " . ($memoryUsage !== null ? $memoryUsage . "%" : "Unavailable");
```

## Notes

- **Compatibilité** : La classe s'adapte aux systèmes d'exploitation Windows et Linux. MacOS n'est actuellement pas pris en charge.
- **Gestion des erreurs** : Renvoie null si la métrique est indisponible.