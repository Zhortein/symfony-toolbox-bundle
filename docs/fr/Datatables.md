# Les Datatables

De nombreux projets avec des interfaces utilisateur reposent sur des tableaux de données, appelées datatables.
La plupart du temps ces datatables reposent sur les mêmes fonctionnalités et grands principes qui doivent sans cesse 
être réimplémentés, table après table. Les fonctionnalités de ce bundle ont pour objectif
de simplifier l'utilisation de ces datatables en proposant une génération et une gestion harmonisée.

Pour le moment, le bundle ne sait gérer que des Datatables liées à une entité Doctrine, servant
de base aux requêtes SQL. Ultérieurement d'autres types de datatables seront ajoutés.

## Configurer les datatables

Le bundle propose de configurer un certain nombre d'éléments pour vos datatables. Ces options seront celles
par défaut de vos tableaux de données et vous permettront de contrôler de manière uniforme certains aspects 
de l'ensemble de vos tables.

La configuration du bundle se trouve dans le fichier de configuration : ```config/packages/zhortein_symfony_toolbox.yaml```.

Voici un aperçu du fichier, pour sa section concernant les datatables :
```yaml
zhortein_symfony_toolbox:
  datatables:
    css_mode: 'bootstrap'
    items_per_page: 10
    paginator: 'custom'
    ux_icons: true
    ux_icons_options:
      icon_first: 'bi:chevron-double-left'
      icon_previous: 'bi:chevron-left'
      icon_next: 'bi:chevron-right'
      icon_last: 'bi:chevron-double-right'
      icon_search: 'bi:search'
      icon_true: 'bi:check'
      icon_false: 'bi:x'
      icon_sort_neutral: 'mdi:sort'
      icon_sort_asc: 'bi:sort-alpha-down'
      icon_sort_desc: 'bi:sort-alpha-up'
      icon_filter: 'mi:filter'
```

### Le mode "CSS"

Cette option vous permet de déterminer si vos datatables seront générées en utilisant 
Bootstrap, Tailwind ou des classes à personnaliser.
Les valeurs admises sont :
- 'bootstrap' : pour bootstrap (version 5.3 actuellement)
- 'tailwind' : pour tailwind
- 'custom' : pour des classes à personnaliser

### Le nombre d'items par page

Cette option vous permet de définir le nombre d'éléments affichés par défaut dans une page de vos tables.
Chaque table pourra par la suite définir une autre valeur, mais celle-ci sera prise en considération dès lors que
vous n'en spécifiez pas.

Par défaut la valeur est positionnée sur 10.

### Le "paginateur"

Cette option vous permet de choisir, actuellement, entre 2 types de pagination :
- ```knp``` : utilisera KnpPaginator
- ```custom``` : utilisera la pagination interne du bundle

D'autres pourront s'ajouter par la suite dans le bundle.

### Les icônes

Les fonctionnalités Datatables du bundle utilisent des icônes. Le bundle vous donne le choix 
entre utiliser des icônes issues du bundle symfony/ux-icons, ou des classes CSS qu'il vous faudra préciser.

Par défaut, ce sont des icônes de symfony/ux-icons qui sont prises en compte.

Le booléen ```ux_icons``` dans la configuration vous permet de basculer entre le mode 
symfony/ux-icons (true) et le mode classes CSS (false).

Les entrées dans la clé ```ux_icons_options``` vous permettent de préciser les icônes à utiliser :
- ```icon_first: 'bi:chevron-double-left'```: Icône de pagination pour indiquer le retour au premier élément
- ```icon_previous: 'bi:chevron-left'```: Icône de pagination pour indiquer le retour à l'élément précédent
- ```icon_next: 'bi:chevron-right'```: Icône de pagination pour indiquer le retour à l'élément suivant
- ```icon_last: 'bi:chevron-double-right'```: Icône de pagination pour indiquer le retour au dernier élément
- ```icon_search: 'bi:search'```: Icône pour symboliser la recherche
- ```icon_true: 'bi:check'```: Icône pour symboliser un élément coché, ou vrai
- ```icon_false: 'bi:x'```: Icône pour symboliser un élément non coché, ou faux
- ```icon_sort_neutral: 'mdi:sort'```: Icône pour symboliser une colonne triable
- ```icon_sort_asc: 'bi:sort-alpha-down'```: Icône pour symboliser un tri actif croissant
- ```icon_sort_desc: 'bi:sort-alpha-up'```: Icône pour symboliser un tri actif décroissant
- ```icon_filter: 'mi:filter'```: Icône symbolisant les filtres de recherche

Vous pouvez retrouver l'intégralité des icônes disponibles pour symfony/ux_icons dans 
la [documentation officielle](https://ux.symfony.com/icons).

Si vous choisissez d'opter pour des classes CSS, vous devrez vous assurer que celles-ci sont chargées dans le contexte d'affichage de vos datatables.
Il suffira alors de positionner ```ux_icons: false``` dans la configuration et de remplacer chaque valeur des clés d'icônes par la classe souhaitée.
Par exemple : ```icon_true: 'fa fa-check'``` pour Font Awesome...

## Créer une Datatable

La mise en place d'une datatable repose sur la création d'une simple classe qui va regrouper les éléments nécessaires
en un unique endroit. Cette classe devra hériter de AbstractDatatable et être pourvue de l'attribut AsDatatable.

L'exemple suivant montre l définition d'une table de données pour une entité "Thing" avec une jointure sur une entité "OtherThing".
```php
<?php

namespace App\Datatables;

use App\Entity\Thing;
use Zhortein\SymfonyToolboxBundle\Attribute\AsDatatable;
use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;

#[AsDatatable(
    name: 'thing_datatable',
    columns: [
        [
            'name' => 'id', 
            'label' => 'Identifier', 
            'searchable' => false, 
            'sortable' => true, 
            'header' => [
                'keep_default_classes' => true,
                'class' => 'myCustomHeaderClass',
                'style' => 'color:red;',
                'data' => ['testh' => 'aaa', 'test2h' => 'bbb', 'test3h' => '123']
            ], 
            'dataset' => [
                'keep_default_classes' => true,
                'class' => 'myCustomDatasetClass',
                'style' => 'color:blue;font-weight:bold;',
                'data' => ['test' => 'aaa', 'test2' => 'bbb', 'test3' => '123']
            ], 
            'footer' => [
                'auto' => '',
                'css' => 'myCustomFooterClass',
                'keep_default_classes' => true,
                'class' => 'text-center',
            ]
        ],
        ['name' => 'name', 'label' => 'Name', 'searchable' => true, 'sortable' => true, 'template' => 'thing/datatable/name.html.twig'],
        ['name' => 'name', 'label' => 'Name of OT', 'searchable' => true, 'sortable' => true, 'alias' => 'ot', 'nameAs' => 'otName'],
        ['name' => 'reference', 'label' => 'Ref', 'searchable' => true, 'sortable' => false, 'alias' => 'ot'],
    ],
    defaultPageSize: 2,
    defaultSort: [['field' => 'id', 'order' => 'asc']],
    searchable: true,
    sortable: true,
    actionColumn: ['label' => 'Actions', 'template' => 'thing/datatable/actions.html.twig'],
    selectorColumn: ['label' => '#'],
    translationDomain: 'messages',
    options: ['thead' => ['class' => 'thead-dark']]
)]
class ThingDatatable extends AbstractDatatable
{
    public function setQueryBuilder(): self
    {
        $this->queryBuilder = $this->em->createQueryBuilder()
            ->select('t')
            ->from($this->getEntityClass(), $this->getMainAlias())->leftJoin('t.otherThings', 'ot');

        return $this;
    }

    public function configure(): self
    {
        return $this;
    }

    public function getEntityClass(): string
    {
        return Thing::class;
    }
}
```

### Paramétrage dans les options de l'attribut PHP AsDatatable

L'attribut AsDatatable permet de spécifier de nombreux éléments pour votre datatable.
Il est également possible en fonction de vos besoins, d'effectuer des paramétrages via la méthode ```configure()```
par exemple.

Les options proposées directement dans l'attribut PHP sont :
* ```name``` : obligatoire, un nom unique représentant votre datatable. C'est avec ce nom que vous appellerez par la suite la datatable dans vos vues Twig
* ```columns``` : le tableau des colonnes de la datatable. Chaque colonne peut avoir les éléments suivants dans son tableau de définition :
  * ```name``` : le nom du champ dans l'entité concernée
  * ```alias``` : l'alias à utiliser pour référencer la table de ce champ, si non précisé, on considère l'alias de la table principal
  * ```nameAs``` : le nom de l'alias à utiliser pour référencer ce champ (utile en cas de champs multiples partageant un nom identique), sera équivalent à name si non précisé
  * ```label``` : le libellé du champ, utilisé pour les entêtes de colonnes, sera traduit si un translationDomain est défini pour la datatable
  * ```searchable``` : true si la recherche peut se faire sur le champ, false sinon
  * ```sortable``` : true si le tri est proposé sur le champ, false sinon
  * ```template``` : la template Twig permettant le rendu du contenu de la colonne. Dans cette template sont disponibles les variables :
      * ```entityObject``` : l'entité principale liée à la ligne
      * ```row``` : les données de la ligne
      * ```fieldValue``` : la valeur du champ pour la ligne courante
  * ```header``` : tableau des options pour l'entête
    * ```translate``` : true si on tente la traduction via le translationDomain fourni 
    * ```keep_default_classes``` : true si on souhaite conserver les classes CSS par défaut sur le <th> généré, false sinon
    * ```class``` : classes CSS à ajouter au <th> généré
    * ```style``` : attribut style éventuel pour le <th> généré
    * ```data``` : attributs data-*, sous forme de tableau clé / valeur, si besoin
  * ```dataset``` : tableau des potions pour les données
      * ```translate``` : true si on tente la traduction via le translationDomain fourni
      * ```keep_default_classes``` : true si on souhaite conserver les classes CSS par défaut sur le <td> généré, false sinon
      * ```class``` : classes CSS à ajouter au <td> généré
      * ```style``` : attribut style éventuel pour le <td> généré
      * ```data``` : attributs data-*, sous forme de tableau clé / valeur, si besoin
  * ```footer``` : tableau des options pour le pied de tableau
      * ```translate``` : true si on tente la traduction via le translationDomain fourni
      * ```keep_default_classes``` : true si on souhaite conserver les classes CSS par défaut sur le <th> généré, false sinon
      * ```class``` : classes CSS à ajouter au <th> généré
      * ```style``` : attribut style éventuel pour le <th> généré
      * ```data``` : attributs data-*, sous forme de tableau clé / valeur, si besoin
      * ```auto``` : prévu pour une future fonctionnalité (somme auto…)
  * ```defaultPageSize``` : un entier pour définir le nombre d'items par défaut dans une page affichée, si omis sera celui par défaut du bundle
  * ```defaultSort``` : tableau des tris par défaut à appliquer. Il est possible de positionner un tri multi-niveaux. Chaque tri comporte 2 clés :
    * ```field``` : le nom nameAs du champ concerné
    * ```order``` : "asc" pour croissant, "desc" pour décroissant
  * ```searchable``` : true si la table permet des recherches, false sinon
  * ```sortable``` : true si la table propose des tris, false sinon
  * ```actionColumn``` : permet d'ajouter une colonne d'actions en précisant dans un tableau les clés : 
    * ```label``` : le libellé de la colonne d'actions, qui sera traduit si translationDomain est défini
    * ```template``` : la template Twig permettant le rendu du contenu de la colonne. Dans cette template sont disponibles les variables : 
      * ```entityObject``` : l'entité principale liée à la ligne
      * ```row``` : les données de la ligne
  * ```selectorColumn``` : permet d'ajouter une colonne de sélecteur de lignes en précisant dans un tableau les clés :
      * ```label``` : le libellé de la colonne, qui sera traduit si translationDomain est défini
      * ```template``` : (prochainement) la template Twig permettant le rendu du contenu de la colonne. Dans cette template sont disponibles les variables :
          * ```entityObject``` : l'entité principale liée à la ligne
          * ```row``` : les données de la ligne
  * ```translationDomain``` : si fourni les libellés des entêtes de colonnes seront traduits avec ce domaine, sinon les libellés seront ceux fournis
  * ```options``` : un tableau d'options
    * ```table``` : un tableau d'attributs pour table
      * ```keep_default_classes``` : true si on souhaite conserver les classes CSS par défaut, false sinon
      * ```class``` : classes CSS à ajouter
      * ```style``` : attribut style éventuel
      * ```data``` : attributs data-*, sous forme de tableau clé / valeur, si besoin
    * ```thead``` : un tableau d'attributs pour le thead
      * ```keep_default_classes``` : true si on souhaite conserver les classes CSS par défaut, false sinon
      * ```class``` : classes CSS à ajouter
      * ```style``` : attribut style éventuel
      * ```data``` : attributs data-*, sous forme de tableau clé / valeur, si besoin
    * ```tbody``` : un tableau d'attributs pour le tbody
      * ```keep_default_classes``` : true si on souhaite conserver les classes CSS par défaut, false sinon
      * ```class``` : classes CSS à ajouter
      * ```style``` : attribut style éventuel
      * ```data``` : attributs data-*, sous forme de tableau clé / valeur, si besoin
    * ```tfoot``` : un tableau d'attributs pour le tfoot
      * ```keep_default_classes``` : true si on souhaite conserver les classes CSS par défaut, false sinon
      * ```class``` : classes CSS à ajouter
      * ```style``` : attribut style éventuel
      * ```data``` : attributs data-*, sous forme de tableau clé / valeur, si besoin
    * ```pagination``` : un tableau d'attributs pour la pagination
      * ```keep_default_classes``` : true si on souhaite conserver les classes CSS par défaut, false sinon
      * ```class``` : classes CSS à ajouter
      * ```style``` : attribut style éventuel
      * ```data``` : attributs data-*, sous forme de tableau clé / valeur, si besoin

### Méthodes à définir

#### getEntityClass()

Cette méthode doit obligatoirement être définie. Elle doit retourner le nom complet de la
classe de l'entité de base pour la datatable.

#### configure()

Cette méthode doit obligatoirement être définie. Elle a pour objectif de vous laisser 
la possibilité d'agir de manière personnalisée sur votre datatable en tant que développeur.
Par défaut, simplement renvoyer $this.

```php
public function configure(): self
{
    return $this;
}
```

#### setQueryBuilder()

Cette méthode vous permet de définir la base de la requête devant être utilisée et notamment de préciser
les jointures souhaitées. Si cette méthode n'est pas définie, un QueryBuilder sera automatiquement
créé avec pour seule entité celle renvoyée par getEntityClass().

L'exemple suivant défini le QueryBuilder avec une jointure vers une seconde entité.
Attention, ```$this->queryBuilder``` doit obligatoirement être un QueryBuilder, donc pas d'appel à ```getQuery()```...
```php
public function setQueryBuilder(): self
{
    $this->queryBuilder = $this->em->createQueryBuilder()
        ->select('t')
        ->from($this->getEntityClass(), $this->getMainAlias())->leftJoin('t.otherThings', 'ot');

    return $this;
}
```

#### applyStaticFilters(QueryBuilder $queryBuilder)

Cette méthode, appelée pour tout chargement de données dans la table, permet
d'appliquer des filtres "permanents". Ce type de filtres est utile par exemple pour 
effectuer un filtre en fonction de l'utilisateur ou d'un contexte, peu importe la demande
de tris ou filtres de l'utilisateur. Le contenu de cette méthode est libre et totalement 
lié à votre QueryBuilder.

Exemple pour toujours afficher uniquement les résultats avec le champ deleted à false : 
```php
public function applyStaticFilters(QueryBuilder $queryBuilder): void
{
    $queryBuilder->andWhere('t.deleted = :deleted')->setParameter('deleted', false);
}
```

### Utilisation dans les templates Twig

Pour ajouter dans une template Twig votre datatable, il suffit de faire : 

```twig
{{ datatable('thing_datatable') }}
```

#### Exemple de template Twig pour la colonne d'Actions

```twig
<a href="{{ path('app_thing_show', { 'id': entityObject.id }) }}">Show</a> -
<a href="{{ path('app_thing_edit', { 'id': entityObject.id }) }}">Edit</a>
```