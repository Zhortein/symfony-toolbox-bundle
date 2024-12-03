<?php

namespace Zhortein\SymfonyToolboxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Zhortein\SymfonyToolboxBundle\Datatables\DatatableService;

class DatatableController extends AbstractController
{
    public function __construct(
        private readonly DatatableService $datatableService,
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
}
