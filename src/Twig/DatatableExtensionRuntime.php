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
        $datatable = $this->datatableService->findDatatableById($datatableId);
        if (!$datatable) {
            throw new \InvalidArgumentException(sprintf('Datatable with ID "%s" not found.', $datatableId));
        }

        $attributes = sprintf(
            'data-controller="datatable" data-datatable-id-value="%s" %s',
            htmlspecialchars($datatableId, ENT_QUOTES),
            implode(' ', array_map(
                static fn ($key, $value) => sprintf('data-datatable-%s-value="%s"', $key, htmlspecialchars($value, ENT_QUOTES)),
                array_keys($options),
                $options
            ))
        );

        $headers = '';
        foreach ($datatable->getColumns() as $column) {
            $sortableAttr = $column['orderable']
                ? sprintf('data-action="click->datatable#sort" data-datatable-sort-value="%s"', htmlspecialchars($column['name'], ENT_QUOTES))
                : '';
            $headers .= sprintf('<th %s>%s</th>', $sortableAttr, htmlspecialchars($column['label'], ENT_QUOTES));
        }

        $loadingText = $this->translator->trans('datatable.loading', [], 'zhortein_symfony_toolbox-datatable');

        return <<<HTML
<div {$attributes} class="datatable-wrapper">
    <div data-datatable-target="error" class="datatable-error hidden"></div>
    <div data-datatable-target="search" class="datatable-search hidden"></div>
    <div data-datatable-target="spinner" class="datatable-spinner hidden">{$loadingText}</div>
    <table class="table datatable">
        <thead>
            <tr>
                {$headers}
            </tr>
        </thead>
        <tbody data-datatable-target="table">
            <!-- Initial rows will be populated dynamically -->
        </tbody>
    </table>
    <nav class="datatable-pagination" data-datatable-target="pagination">
        <!-- Pagination dynamically handled -->
    </nav>
</div>
HTML;
    }
}
