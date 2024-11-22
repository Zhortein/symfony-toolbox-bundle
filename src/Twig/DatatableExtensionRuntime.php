<?php

namespace Zhortein\SymfonyToolboxBundle\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;
use Zhortein\SymfonyToolboxBundle\Datatables\DatatableService;

readonly class DatatableExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private DatatableService $datatableService,
        private TranslatorInterface $translator)
    {
    }

    public function renderDatatable(string $datatableId, array $options = []): string
    {
        $controllerName = 'zhortein--symfony-toolbox-bundle--datatable';
        $datatable = $this->datatableService->findDatatableById($datatableId);

        if (!$datatable) {
            throw new \InvalidArgumentException(sprintf('Datatable with ID "%s" not found.', $datatableId));
        }

        $attributes = sprintf(
            'data-controller="%s" data-%s-id-value="%s" %s',
            $controllerName,
            $controllerName,
            htmlspecialchars($datatableId, ENT_QUOTES),
            implode(' ', array_map(
                static fn ($key, $value) => sprintf('data-%s-%s-value="%s"', $controllerName, $key, htmlspecialchars($value, ENT_QUOTES)),
                array_keys($options),
                $options
            ))
        );

        $headers = '';
        foreach ($datatable->getColumns() as $column) {
            $sortableAttr = $column['sortable']
                ? sprintf('data-action="click->%s#sort" data-%s-sort-value="%s"', $controllerName, $controllerName, htmlspecialchars($column['name'], ENT_QUOTES))
                : '';
            $headers .= sprintf('<th scope="col" %s>%s</th>', $sortableAttr, htmlspecialchars($column['label'], ENT_QUOTES));
        }

        $loadingText = $this->translator->trans('datatable.loading', [], 'zhortein_symfony_toolbox-datatable');

        $tableWrapperCssClasses = 'datatable-wrapper '.match ($datatable->getCssMode()) {
            'bootstrap' => 'table-responsive',
            'tailwind' => 'inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8',
            default => '',
        };

        $tableCssClasses = 'datatable '.match ($datatable->getCssMode()) {
            'bootstrap' => 'table table-striped table-bordered',
            'tailwind' => 'min-w-full divide-y divide-gray-300',
            default => 'table',
        };

        $tableTbodyCssClasses = match ($datatable->getCssMode()) {
            'bootstrap' => 'table-hover',
            'tailwind' => 'divide-y divide-gray-200 hover:bg-gray-100',
            default => '',
        };

        return <<<HTML
<div {$attributes} class="{$tableWrapperCssClasses}">
    <div data-{$controllerName}-target="error" class="datatable-error hidden"></div>
    <div data-{$controllerName}-target="search" class="datatable-search hidden"></div>
    <div data-{$controllerName}-target="spinner" class="datatable-spinner hidden">{$loadingText}</div>
    <table class="{$tableCssClasses}">
        <thead>
            <tr>
                {$headers}
            </tr>
        </thead>
        <tbody class="{$tableTbodyCssClasses}" data-{$controllerName}-target="table">
            <!-- Initial rows will be populated dynamically -->
        </tbody>
    </table>
    <nav class="datatable-pagination" data-{$controllerName}-target="pagination">
        <!-- Pagination dynamically handled -->
    </nav>
</div>
HTML;
    }
}
