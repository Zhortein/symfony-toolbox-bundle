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
            $headers .= sprintf('<th %s>%s</th>', $sortableAttr, htmlspecialchars($column['label'], ENT_QUOTES));
        }

        $loadingText = $this->translator->trans('datatable.loading', [], 'zhortein_symfony_toolbox-datatable');

        return <<<HTML
<div {$attributes} class="datatable-wrapper">
    <div data-{$controllerName}-target="error" class="datatable-error hidden"></div>
    <div data-{$controllerName}-target="search" class="datatable-search hidden"></div>
    <div data-{$controllerName}-target="spinner" class="datatable-spinner hidden">{$loadingText}</div>
    <table class="table datatable">
        <thead>
            <tr>
                {$headers}
            </tr>
        </thead>
        <tbody data-{$controllerName}-target="table">
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
