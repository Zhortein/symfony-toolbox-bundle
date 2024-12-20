# Datatables

Many projects with user interfaces rely on data tables, also known as datatables.
Most of the time, these datatables follow the same principles and functionalities, requiring repetitive reimplementation for every table.
The features in this bundle aim to simplify datatable usage by providing a consistent and unified generation and management approach.

For now, the bundle only supports datatables linked to a Doctrine entity, serving as the base for SQL queries. Additional datatable types may be added later.

## Configuring Datatables

The bundle allows configuration of several elements for your datatables. These default settings provide uniform control over key aspects of your tables.

The configuration resides in the following file:
```config/packages/zhortein_symfony_toolbox.yaml```.

Here's an example configuration snippet focusing on datatables:
```yaml
zhortein_symfony_toolbox:
  datatables:
    css_mode: 'bootstrap'
    items_per_page: 10
    paginator: 'custom'
    export:
      enabled_by_default: true
      export_csv: true
      export_pdf: false
      export_excel: true
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
      icon_export_csv: 'bi:filetype-csv'
      icon_export_pdf: 'bi:filetype-pdf'
      icon_export_excel: 'bi:filetype-xlsx'
```

## CSS Mode

This option determines whether your datatables will be generated using Bootstrap, Tailwind, or custom classes.
Valid values are:
- 'bootstrap' : For Bootstrap (currently version 5.3).
- 'tailwind' : For TailwindCSS.
- 'custom' : For custom-defined CSS classes.

### Items per Page

This option sets the default number of items displayed per page in your tables.
You can override it for specific tables, but this value serves as the default.

The default value is 10.

### Paginator

This option allows you to choose between two types of pagination:
- ```knp```: Uses the KnpPaginator.
- ```custom```: Uses the internal pagination provided by the bundle.

More options may be added in the future.

### Icons

The bundle uses icons for its datatable features. You can choose between Symfony UX Icons
(```symfony/ux-icons```) or custom CSS classes for your icons.

By default, the Symfony UX Icons mode is enabled.

The  ```ux_icons``` boolean in the configuration toggles between:
* ```true```: Uses Symfony UX Icons.
* ```false```: Allows you to specify custom CSS classes.

The ```export``` key allow you to set the default behaviours for export features on your datatables:
- ```enabled_by_default```: true to enable by default export feature on datatables, false to disable by default (you still can enable it in the AsDatatable attribute for your datatable)
- ```export_csv```: true to enable CSV export by default
- ```export_pdf```: true to enable PDF export by default
- ```export_excel```: true to enable Excel export by default

The ```ux_icons_options``` keys allow you to specify the icons to use:
- ```icon_first: 'bi:chevron-double-left'```: Pagination icon for "First Page."
- ```icon_previous: 'bi:chevron-left'```: Pagination icon for "Previous Page."
- ```icon_next: 'bi:chevron-right'```: Pagination icon for "Next Page."
- ```icon_last: 'bi:chevron-double-right'```: Pagination icon for "Last Page."
- ```icon_search: 'bi:search'```: Icon for the search functionality.
- ```icon_true: 'bi:check'```: Icon representing "True" or "Checked."
- ```icon_false: 'bi:x'```: Icon representing "False" or "Unchecked."
- ```icon_sort_neutral: 'mdi:sort'```: Icon representing a sortable column.
- ```icon_sort_asc: 'bi:sort-alpha-down'```: Icon for active ascending sort.
- ```icon_sort_desc: 'bi:sort-alpha-up'```: Icon for active descending sort.
- ```icon_filter: 'mi:filter'```: Icon representing filters.
- ```icon_export_csv: 'bi:filetype-csv'```: Icon representing CSV export.
- ```icon_export_pdf: 'bi:filetype-pdf'```: Icon representing PDF export.
- ```icon_export_excel: 'bi:filetype-xlsx'```: Icon representing Excel Export.

Visit the [official Symfony UX Icons documentation](https://ux.symfony.com/icons) for a full list of available icons.

If using custom CSS classes, ensure they are loaded within the datatable's context.
Set ```ux_icons: false``` in the configuration and replace the icon values with your 
CSS classes (e.g., ```icon_true: 'fa fa-check'``` for Font Awesome).

## Create a Datatable

The implementation of a datatable is based on creating a simple class that groups the 
necessary elements in a single place. This class should inherit from ```AbstractDatatable``` and 
be provided with the ```AsDatatable``` attribute.

The following example shows the definition of a data table for a "Thing" entity with a join on an "OtherThing" entity.
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
    exportable: true,
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

### Settings in the PHP AsDatatable attribute options

The AsDatatable attribute allows you to specify many elements for your datatable.
It is also possible, depending on your needs, to make settings via the ```configure()``` method.
For example.

The options offered directly in the PHP attribute are:
* ```name```: mandatory, a unique name representing your datatable. It is with this name that you will subsequently call the datatable in your Twig views
* ```columns```: the table of columns of the datatable. Each column can have the following elements in its definition table:
    * ```name```: the name of the field in the concerned entity
    * ```alias```: the alias to use to reference the table of this field, if not specified, we consider the alias of the main table
    * ```nameAs```: the name of the alias to use to reference this field (useful in case of multiple fields sharing an identical name), will be equivalent to name if not specified
    * ```label```: the field label, used for column headers, will be translated if a translationDomain is defined for the datatable
    * ```searchable```: true if the search can be done immediately, false otherwise
    * ```sortable```: true if sorting is offered immediately, false otherwise
    * ```template```: the Twig template allowing the rendering of the column content. In this template the variables are available:
        * ```entityObject```: the main entity linked to the line
        * ```row```: line data
        * ```fieldValue```: the value of the field for the current line
    * ```header```: header options table
        * ```translate```: true if we attempt the translation via the translationDomain provided
        * ```keep_default_classes```: true if you want to keep the default CSS classes, false otherwise
        * ```class```: CSS classes to add
        * ```style```: possible style attribute
        * ```data```: data-* attributes, in key/value array form, if needed
    * ```dataset```: dataset options table
        * ```translate```: true if we attempt the translation via the translationDomain provided
        * ```keep_default_classes```: true if you want to keep the default CSS classes, false otherwise
        * ```class```: CSS classes to add
        * ```style```: possible style attribute
        * ```data```: data-* attributes, in key/value array form, if needed
    * ```footer```: footer options table
        * ```translate```: true if we attempt the translation via the translationDomain provided
        * ```keep_default_classes```: true if you want to keep the default CSS classes, false otherwise
        * ```class```: CSS classes to add
        * ```style```: possible style attribute
        * ```data```: data-* attributes, in key/value array form, if needed
        * ```auto```: planned for a future functionality (auto sum…)
    * ```defaultPageSize```: an integer to define the default number of items in a displayed page, if omitted will be the default of the bundle
    * ```defaultSort```: default sorting table to apply. It is possible to position a multi-level sorting. Each sort has 2 keys:
        * ```field```: the nameAs of the field concerned
        * ```order```: “asc” for ascending, “desc” for descending
    * ```searchable```: true if the table allows searches, false otherwise
    * ```sortable```: true if the table offers sorting, false otherwise
    * ```exportable```: true if the table offers exports, false otherwise
    * ```actionColumn```: allows you to add a column of actions by specifying the keys in a table:
        * ```label```: the label of the actions column, which will be translated if translationDomain is defined
        * ```template```: the Twig template allowing the rendering of the column content. In this template the variables are available:
            * ```entityObject```: the main entity linked to the line
            * ```row```: line data
    * ```selectorColumn```: allows you to add a row selector column by specifying the keys in a table:
        * ```label```: the column label, which will be translated if translationDomain is defined
        * ```template```: (coming soon) the Twig template allowing the rendering of column content. In this template the variables are available:
            * ```entityObject```: the main entity linked to the line
            * ```row```: line data
    * ```translationDomain```: if provided, the labels of the column headers will be translated with this domain, otherwise the labels will be those provided
    * ```options```: an array of options
        * ```table```: an array of attributes for table
            * ```keep_default_classes```: true if you want to keep the default CSS classes, false otherwise
            * ```class```: CSS classes to add
            * ```style```: possible style attribute
            * ```data```: data-* attributes, in key/value array form, if needed
        * ```thead```: an array of attributes for thead
            * ```keep_default_classes```: true if you want to keep the default CSS classes, false otherwise
            * ```class```: CSS classes to add
            * ```style```: possible style attribute
            * ```data```: data-* attributes, in key/value array form, if needed
        * ```tbody```: an array of attributes for tbody
            * ```keep_default_classes```: true if you want to keep the default CSS classes, false otherwise
            * ```class```: CSS classes to add
            * ```style```: possible style attribute
            * ```data```: data-* attributes, in key/value array form, if needed
        * ```tfoot```: an array of attributes for tfoot
            * ```keep_default_classes```: true if you want to keep the default CSS classes, false otherwise
            * ```class```: CSS classes to add
            * ```style```: possible style attribute
            * ```data```: data-* attributes, in key/value array form, if needed
        * ```pagination```: an array of attributes for pagination
            * ```keep_default_classes```: true if you want to keep the default CSS classes, false otherwise
            * ```class```: CSS classes to add
            * ```style```: possible style attribute
            * ```data```: data-* attributes, in key/value array form, if needed

### Methods to define

#### getEntityClass()

This method must be defined. It must return the full name of the
Base entity class for the datatable.

#### configure()

This method must be defined. Its aim is to leave you
the ability to act in a personalized way on your datatable as a developer.
By default, just return $this.

```php
public function configure(): self
{
    return $this;
}
```

#### setQueryBuilder()

This method allows you to define the basis of the query to be used and in particular to specify
the desired joins. If this method is not defined, a QueryBuilder will automatically be
created with the only entity returned by getEntityClass().

The following example defines the QueryBuilder with a join to a second entity.
Please note, ```$this->queryBuilder``` must be a QueryBuilder, so no call to ```getQuery()```...
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

This method, called for any data loading into the table, allows
to apply "permanent" filters. This type of filter is useful for example for
perform a filter based on user or context, regardless of the request
user sorting or filtering. The content of this method is free and completely
linked to your QueryBuilder.

Example to always display only results with the deleted field set to false:
```php
public function applyStaticFilters(QueryBuilder $queryBuilder): void
{
    $queryBuilder->andWhere('t.deleted = :deleted')->setParameter('deleted', false);
}
```

### Use in Twig templates

To add your data to a Twig template, simply do:

```twig
{{ datatable('thing_datatable') }}
```

#### Example of Twig template for the Actions column

```twig
<a href="{{ path('app_thing_show', { 'id': entityObject.id }) }}">Show</a> -
<a href="{{ path('app_thing_edit', { 'id': entityObject.id }) }}">Edit</a>
```