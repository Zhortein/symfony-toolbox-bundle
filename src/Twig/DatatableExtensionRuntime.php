<?php

namespace Zhortein\SymfonyToolboxBundle\Twig;

use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;
use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;
use Zhortein\SymfonyToolboxBundle\Datatables\DatatableService;

readonly class DatatableExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private DatatableService $datatableService,
    ) {
    }

    public function renderDatatable(Environment $environment, string $datatableId, ?string $translationDomain = null, array $options = []): string
    {
        $datatable = $this->datatableService->findDatatableById($datatableId);
        if (!$datatable) {
            throw new \InvalidArgumentException(sprintf('Datatable with ID "%s" not found.', $datatableId));
        }

        if (null === $translationDomain) {
            $translationDomain = AbstractDatatable::DEFAULT_TRANSLATION_DOMAIN;
        }

        $controllerName = $datatable->getStimulusControllerName();
        $dataPrefix = 'data-'.$controllerName.'-';
        $attributes = sprintf(
            '%s',
            implode(' ', array_map(
                static fn ($key, $value) => sprintf('%s%s-value="%s"', $dataPrefix, $key, htmlspecialchars($value, ENT_QUOTES)),
                array_keys($options),
                $options
            ))
        );

        return $environment->render('@ZhorteinSymfonyToolbox/datatables/_datatable-'.$datatable->getCssMode().'.html.twig', [
            'datatableId' => $datatableId,
            'datatable' => $datatable,
            'attributes' => $attributes,
            'transDomain' => $translationDomain,
        ]);
    }
}
