<?php

namespace Zhortein\SymfonyToolboxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Zhortein\SymfonyToolboxBundle\Datatables\DatatableService;
use Zhortein\SymfonyToolboxBundle\Enum\EnumTranslatableInterface;

class DatatableController extends AbstractController
{
    /**
     * @var array<string, array<int, array{key: string|int, label: string}>|null>
     */
    private static array $enumCache = [];

    public function __construct(
        private readonly DatatableService $datatableService,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws NotFoundHttpException
     * @throws \JsonException
     */
    public function fetchData(string $datatableId, Request $request): Response
    {
        $datatable = $this->datatableService->findDatatableById($datatableId);

        if (!$datatable) {
            throw $this->createNotFoundException(sprintf('Datatable with ID "%s" not found.', $datatableId));
        }

        return $this->datatableService->render($datatable, $request);
    }

    public function export(string $datatableId, Request $request): Response
    {
        $datatable = $this->datatableService->findDatatableById($datatableId);

        if (!$datatable) {
            throw $this->createNotFoundException(sprintf('Datatable with ID "%s" not found.', $datatableId));
        }

        // Get the export type from the Request
        $exportType = $request->query->getString('type', 'csv');

        return $this->datatableService->export($datatable, $request, $exportType);
    }

    public function getFiltersInfo(string $datatableId): JsonResponse
    {
        // Obtenir l'objet Datatable à partir de l'id
        $datatable = $this->datatableService->findDatatableById($datatableId);

        if (!$datatable) {
            throw $this->createNotFoundException(sprintf('Datatable with ID "%s" not found.', $datatableId));
        }

        $columns = $datatable->getColumns();

        $response = [];
        foreach ($columns as $column) {
            $dataType = $datatable->getDatatypeForFilters($column->datatype);
            $response[] = [
                'name' => $datatable->getFullyQualifiedColumnFromNameAs((string) $column->nameAs),
                'label' => $column->label,
                'type' => $dataType,
                'filters' => $this->getAvailableFilters($dataType),
                'values' => $column->isEnum && null !== $column->enumClass ? $this->getEnumValues($column->enumClass) : null,
            ];
        }

        return new JsonResponse([
            'columns' => $response,
        ], Response::HTTP_OK, [
            'Cache-Control' => 'max-age=3600, public',
        ]);
    }

    /**
     * @return array<int, array{id: string, label: string}>
     */
    private function getAvailableFilters(string $type): array
    {
        $transDomain = 'zhortein_symfony_toolbox-datatable-filters';

        return match ($type) {
            'string' => [
                ['id' => 'equal', 'label' => $this->translator->trans('datatable.filter.equal', [], $transDomain)],
                ['id' => 'not_equal', 'label' => $this->translator->trans('datatable.filter.not_equal', [], $transDomain)],
                ['id' => 'contains', 'label' => $this->translator->trans('datatable.filter.contains', [], $transDomain)],
                ['id' => 'not_contains', 'label' => $this->translator->trans('datatable.filter.not_contains', [], $transDomain)],
                ['id' => 'starts_with', 'label' => $this->translator->trans('datatable.filter.starts_with', [], $transDomain)],
                ['id' => 'not_starts_with', 'label' => $this->translator->trans('datatable.filter.not_starts_with', [], $transDomain)],
                ['id' => 'ends_with', 'label' => $this->translator->trans('datatable.filter.ends_with', [], $transDomain)],
                ['id' => 'not_ends_with', 'label' => $this->translator->trans('datatable.filter.not_ends_with', [], $transDomain)],
                ['id' => 'is_null', 'label' => $this->translator->trans('datatable.filter.is_null', [], $transDomain)],
                ['id' => 'is_not_null', 'label' => $this->translator->trans('datatable.filter.is_not_null', [], $transDomain)],
            ],
            'date' => [
                ['id' => 'equal', 'label' => $this->translator->trans('datatable.filter.equal', [], $transDomain)],
                ['id' => 'not_equal', 'label' => $this->translator->trans('datatable.filter.not_equal', [], $transDomain)],
                ['id' => 'before', 'label' => $this->translator->trans('datatable.filter.before', [], $transDomain)],
                ['id' => 'after', 'label' => $this->translator->trans('datatable.filter.after', [], $transDomain)],
                ['id' => 'between', 'label' => $this->translator->trans('datatable.filter.between', [], $transDomain)],
                ['id' => 'not_between', 'label' => $this->translator->trans('datatable.filter.not_between', [], $transDomain)],
                ['id' => 'is_null', 'label' => $this->translator->trans('datatable.filter.is_null', [], $transDomain)],
                ['id' => 'is_not_null', 'label' => $this->translator->trans('datatable.filter.is_not_null', [], $transDomain)],
            ],
            'number' => [
                ['id' => 'equal', 'label' => $this->translator->trans('datatable.filter.equal', [], $transDomain)],
                ['id' => 'not_equal', 'label' => $this->translator->trans('datatable.filter.not_equal', [], $transDomain)],
                ['id' => 'greater_than', 'label' => $this->translator->trans('datatable.filter.greater_than', [], $transDomain)],
                ['id' => 'greater_or_equal_than', 'label' => $this->translator->trans('datatable.filter.greater_or_equal_than', [], $transDomain)],
                ['id' => 'less_than', 'label' => $this->translator->trans('datatable.filter.less_than', [], $transDomain)],
                ['id' => 'less_or_equal_than', 'label' => $this->translator->trans('datatable.filter.less_or_equal_than', [], $transDomain)],
                ['id' => 'between', 'label' => $this->translator->trans('datatable.filter.between', [], $transDomain)],
                ['id' => 'not_between', 'label' => $this->translator->trans('datatable.filter.not_between', [], $transDomain)],
                ['id' => 'is_null', 'label' => $this->translator->trans('datatable.filter.is_null', [], $transDomain)],
                ['id' => 'is_not_null', 'label' => $this->translator->trans('datatable.filter.is_not_null', [], $transDomain)],
            ],
            'boolean' => [
                ['id' => 'is_true', 'label' => $this->translator->trans('datatable.filter.is_true', [], $transDomain)],
                ['id' => 'is_false', 'label' => $this->translator->trans('datatable.filter.is_false', [], $transDomain)],
            ],
            'enum' => [
                ['id' => 'in', 'label' => $this->translator->trans('datatable.filter.in', [], $transDomain)],
                ['id' => 'not_in', 'label' => $this->translator->trans('datatable.filter.not_in', [], $transDomain)],
            ],
            default => [],
        };
    }

    /**
     * @param class-string<\BackedEnum> $enumClass
     *
     * @return array<int, array{key: string|int, label: string}>|null
     */
    private function getEnumValues(string $enumClass): ?array
    {
        if (!isset(self::$enumCache[$enumClass])) {
            if (!enum_exists($enumClass)) {
                self::$enumCache[$enumClass] = null;
            } else {
                self::$enumCache[$enumClass] = array_map(function (\BackedEnum $value): array {
                    return [
                        'key' => $value->value,
                        'label' => $this->getLabelForEnumValue($value),
                    ];
                }, $enumClass::cases());
            }
        }

        return self::$enumCache[$enumClass];
    }

    private function getLabelForEnumValue(\BackedEnum $enumValue): string
    {
        if ($enumValue instanceof EnumTranslatableInterface) {
            return (string) $enumValue->label($this->translator);
        }

        return (string) $enumValue->value;
    }
}
