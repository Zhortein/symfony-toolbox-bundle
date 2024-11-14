# Zhortein / Symfony Toolbox Bundle

[![CI](https://github.com/Zhortein/symfony-toolbox-bundle/actions/workflows/ci.yml/badge.svg)](https://github.com/Zhortein/symfony-toolbox-bundle/actions/workflows/ci.yml)

[English](#english-version) - [French](#version-française)

## English version

This Symfony bundle provides a set of utility services, enums, classes, and tools designed to enhance Symfony applications, making it easier to manage specific tasks such as server metrics, extended enum features, business date handling, and more. The toolbox offers optimized utilities for developers aiming to streamline their workflow with the latest Symfony features.

### Available features
- [Business DateTime handling](./docs/en/BusinessDateTime.md): holidays, working days, and more
- [Color Tools](./docs/en/ColorTools.md): RGB to Hex, Hex to RGB, color palette, etc.
- [Data Types Validation](./docs/en/DataTypeValidator.md): validate PHP data using Symfony constraint validators
- [DateInterval Tools](./docs/en/TimeToolBox.md): add, subtract, normalize, and more
- [DateTime Manipulation Tools](./docs/en/DateToolBox.md)
- [Enums and Enum Tools](./docs/en/Enums.md): Days, Months, Actions, Translatable Enums, and more
- [File Content Getter](./docs/en/FileContentGetter.md): retrieve and save file content locally
- [EXIF Info Handling](./docs/en/FileExifInfo.md): extract EXIF information from images
- [File Handling Tools](./docs/en/FileToolBox.md): recursive folder removal, large file reading, folder copying, etc.
- [Measure Converters](./docs/en/MeasureConverter.md): display human-readable file sizes, convert between km/miles, kg/pounds, etc.
- [Server Metrics](./docs/en/ServerMetric.md): retrieve various server metrics
- [String Manipulation Tools](./docs/en/StringTools.md): sanitize strings and filenames, remove diacritics, count words, truncate text, and more.

### Installation

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

#### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
composer require zhortein/symfony-toolbox-bundle
```

#### Applications that don't use Symfony Flex

##### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
composer require zhortein/symfony-toolbox-bundle
```

##### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Zhortein\SymfonyToolboxBundle\ZhorteinSymfonyToolboxBundle::class => ['all' => true],
];
```

### Usage

Each feature listed in the introduction above links to its dedicated documentation page. There, you’ll find detailed usage examples, configuration notes, and other information tailored for efficient integration in Symfony projects.

## Contributing

Contributions are welcome! If you’d like to contribute to this project, please:
1. Fork the repository.
2. Create a feature branch (`git checkout -b feature/my-feature`).
3. Commit your changes (`git commit -m "Add my feature"`).
4. Push to the branch (`git push origin feature/my-feature`).
5. Open a pull request.

All contributions should adhere to the project’s coding standards and follow the [conventional commit](https://www.conventionalcommits.org/) guidelines.

## License

This bundle is licensed under the [GPL-3.0 License](./LICENSE). You are free to use, modify, and distribute this bundle under the terms of this license.

## Credits

Developed and maintained by [David RENARD](https://github.com/Zhortein). Special thanks to contributors and the Symfony community for their ongoing support and resources.

---

## Version Française

Ce bundle Symfony fournit un ensemble de services utilitaires, d'enums, de classes et d'outils conçus pour améliorer les applications Symfony en facilitant la gestion de tâches spécifiques telles que les métriques serveur, les fonctionnalités étendues des enums, la gestion des dates d'affaires, et bien plus. La toolbox propose des utilitaires optimisés pour les développeurs souhaitant rationaliser leur flux de travail avec les dernières fonctionnalités de Symfony.

### Fonctionnalités disponibles
- [Gestion des dates d'affaires](./docs/fr/BusinessDateTime.md) : jours fériés, jours ouvrables, etc.
- [Outils de couleur](./docs/fr/ColorTools.md) : conversion RGB vers Hex, Hex vers RGB, palettes de couleurs, etc.
- [Validation des types de données](./docs/fr/DataTypeValidator.md) : validez vos données PHP en utilisant les validateurs de contraintes Symfony
- [Outils pour DateInterval](./docs/fr/TimeToolBox.md) : addition, soustraction, normalisation, etc.
- [Outils pour manipuler les DateTime](./docs/fr/DateToolBox.md)
- [Enums utiles et outils pour les enums](./docs/fr/Enums.md) : jours, mois, actions, enums traduisibles, etc.
- [Gestionnaire de contenu de fichiers](./docs/fr/FileContentGetter.md) : récupération et sauvegarde du contenu de fichiers en local
- [Gestion des informations EXIF](./docs/fr/FileExifInfo.md) : extraction des informations EXIF des images
- [Outils de gestion de fichiers](./docs/fr/FileToolBox.md) : suppression de dossiers de manière récursive, lecture de gros fichiers, copie de dossiers, etc.
- [Convertisseurs de mesures](./docs/fr/MeasureConverter.md) : affichage de la taille de fichiers en format lisible, conversion entre km et miles, kg et livres, etc.
- [Métriques serveur](./docs/fr/ServerMetric.md) : récupération de diverses métriques serveur
- [Outils de manipulation de chaînes](./docs/fr/StringTools.md) : assainissement des chaînes et des noms de fichiers, suppression des diacritiques, comptage de mots, troncature de texte, etc.

### Installation

Assurez-vous que Composer est installé globalement, comme expliqué dans le
[chapitre d'installation](https://getcomposer.org/doc/00-intro.md)
de la documentation de Composer.

#### Applications qui utilisent Symfony Flex

Ouvrez une console de commande, accédez au répertoire de votre projet, et exécutez :

```console
composer require zhortein/symfony-toolbox-bundle
```

#### Applications qui n'utilisent pas Symfony Flex

##### Etape 1: Téléchargez le bundle

Ouvrez une console de commande, accédez au répertoire de votre projet, et exécutez la
commande suivante pour télécharger la dernière version stable du bundle :

```console
composer require zhortein/symfony-toolbox-bundle
```

##### Etape 2: Activez le Bundle

Ensuite, activez le bundle en l'ajoutant dans la liste des bundles connus 
dans le fichier `config/bundles.php` de votre projet :

```php
// config/bundles.php

return [
    // ...
    Zhortein\SymfonyToolboxBundle\ZhorteinSymfonyToolboxBundle::class => ['all' => true],
];
```

### Utilisation

Chaque fonctionnalité listée dans l'introduction renvoie vers une page de documentation dédiée. Vous y trouverez des exemples détaillés d'utilisation, des notes de configuration, et d'autres informations pour une intégration efficace dans vos projets Symfony.

## Contribution

Les contributions sont les bienvenues ! Si vous souhaitez contribuer à ce projet, veuillez :
1. Faire un fork du dépôt.
2. Créer une branche pour votre fonctionnalité (`git checkout -b feature/ma-fonctionnalite`).
3. Commiter vos changements (`git commit -m "Ajout de ma fonctionnalité"`).
4. Pousser la branche (`git push origin feature/ma-fonctionnalite`).
5. Ouvrir une pull request.

Toutes les contributions doivent respecter les standards de codage du projet et suivre les directives des [commits conventionnels](https://www.conventionalcommits.org/).

## Licence

Ce bundle est sous licence [GPL-3.0](./LICENSE). Vous êtes libre d'utiliser, de modifier et de distribuer ce bundle selon les termes de cette licence.

## Crédits

Développé et maintenu par [David RENARD](https://github.com/Zhortein). Un grand merci aux contributeurs et à la communauté Symfony pour leur soutien et leurs ressources.

