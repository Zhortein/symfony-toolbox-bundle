<?php

namespace Zhortein\SymfonyToolboxBundle\Twig;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;
use Zhortein\SymfonyToolboxBundle\Datatables\DatatableService;

readonly class DatatableExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private DatatableService $datatableService,
        private TranslatorInterface $translator,
        private RouterInterface $router,
    ) {
    }

    public function renderDatatable(string $datatableId, array $options = []): string
    {
        $datatable = $this->datatableService->findDatatableById($datatableId);
        if (!$datatable) {
            throw new \InvalidArgumentException(sprintf('Datatable with ID "%s" not found.', $datatableId));
        }

        $controllerName = $datatable->getStimulusControllerName();
        $dataPrefix = 'data-'.$controllerName.'-';
        $attributes = sprintf(
            'data-controller="%s" %sid-value="%s" %smode-value="%s" %spagesize-value="%s" %surl-value="%s" %s',
            $controllerName,
            $dataPrefix,
            htmlspecialchars($datatableId, ENT_QUOTES),
            $dataPrefix,
            $datatable->getCssMode(),
            $dataPrefix,
            $datatable->getOptions()['defaultPageSize'],
            $dataPrefix,
            $this->router->generate('zhortein_datatable_fetch_data', ['datatableId' => htmlspecialchars($datatableId, ENT_QUOTES)]),
            implode(' ', array_map(
                static fn ($key, $value) => sprintf('%s%s-value="%s"', $dataPrefix, $key, htmlspecialchars($value, ENT_QUOTES)),
                array_keys($options),
                $options
            ))
        );

        $headers = '';
        foreach ($datatable->getColumns() as $rank => $column) {
            $sortableAttr = $column['sortable']
                ? sprintf('data-action="click->%s#sort" %ssort-value="%s"', $dataPrefix, $controllerName, htmlspecialchars($column['name'], ENT_QUOTES))
                : '';
            $headerCssClasses = match ($column['cssMode']) {
                'tailwind' => 0 === $rank ? 'py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0' : 'px-3 py-3.5 text-left text-sm font-semibold text-gray-900',
                default => '',
            };
            $headers .= sprintf('<th scope="col" class="%s" %s>%s</th>', $headerCssClasses, $sortableAttr, htmlspecialchars($column['label'], ENT_QUOTES));
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

        $hiddenCssClass = match ($datatable->getCssMode()) {
            'bootstrap' => 'd-none',
            default => 'hidden',
        };

        return <<<HTML
<div {$attributes} class="{$tableWrapperCssClasses}">
    <div {$dataPrefix}target="error" class="datatable-error {$hiddenCssClass}"></div>
    <div {$dataPrefix}target="search" class="datatable-search {$hiddenCssClass}"></div>
    <div {$dataPrefix}target="spinner" class="datatable-spinner {$hiddenCssClass}">{$loadingText}</div>
    <table class="{$tableCssClasses}">
        <thead>
            <tr>
                {$headers}
            </tr>
        </thead>
        <tbody class="{$tableTbodyCssClasses}" {$dataPrefix}target="table">
            <!-- Initial rows will be populated dynamically -->
        </tbody>
    </table>
    <nav class="datatable-pagination" {$dataPrefix}target="pagination">
        <!-- Pagination dynamically handled -->
    </nav>
</div>
HTML;
    }
}
