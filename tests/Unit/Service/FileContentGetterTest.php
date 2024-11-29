<?php

namespace Zhortein\SymfonyToolboxBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zhortein\SymfonyToolboxBundle\Service\DataTypeValidator;
use Zhortein\SymfonyToolboxBundle\Service\FileContentGetter;

class FileContentGetterTest extends TestCase
{
    private FileContentGetter $fileContentGetter;
    private HttpClientInterface $httpClient;
    private TranslatorInterface $translator;
    private LoggerInterface $logger;
    private ValidatorInterface $validatorService;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->validatorService = $this->createMock(ValidatorInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        // Instancie DataTypeValidator avec le mock de ValidatorInterface
        $dataTypeValidator = new DataTypeValidator($this->validatorService);

        $this->fileContentGetter = new FileContentGetter(
            validator: $dataTypeValidator,
            translator: $this->translator,
            logger: $this->logger,
            httpClient: $this->httpClient
        );
    }

    public function testGetContentFromUrlValidResponse(): void
    {
        $url = 'http://example.com';
        $contentType = 'application/json';

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(Response::HTTP_OK);
        $response->method('getContent')->willReturn('{"key": "value"}');

        // Crée un mock de ConstraintViolationListInterface qui est vide pour simuler une URL valide
        $violations = $this->createMock(ConstraintViolationListInterface::class);
        $violations->method('count')->willReturn(0); // Pas de violations, donc l'URL est considérée valide

        $this->validatorService->method('validate')->willReturn($violations);
        $this->httpClient->method('request')->willReturn($response);

        $content = $this->fileContentGetter->getContentFromUrl($contentType, $url);

        $this->assertIsArray($content);
        $this->assertArrayHasKey('key', $content);
        $this->assertEquals('value', $content['key']);
    }

    public function testGetContentFromUrlInvalidUrl(): void
    {
        $url = 'invalid-url';
        $contentType = 'application/json';

        // Crée un mock de ConstraintViolationListInterface qui contient une violation
        $violations = $this->createMock(ConstraintViolationListInterface::class);
        $violations->method('count')->willReturn(1); // La présence de violations rend l'URL invalide

        $this->validatorService->method('validate')->willReturn($violations);
        $this->translator->method('trans')->willReturn('Invalid URL');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URL');

        $this->fileContentGetter->getContentFromUrl($contentType, $url);
    }

    public function testGetContentFromUrlErrorResponse(): void
    {
        $url = 'http://example.com';
        $contentType = 'application/json';

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(Response::HTTP_NOT_FOUND);

        $violations = $this->createMock(ConstraintViolationListInterface::class);
        $violations->method('count')->willReturn(0); // URL considérée valide

        $this->validatorService->method('validate')->willReturn($violations);
        $this->httpClient->method('request')->willReturn($response);
        $this->translator->method('trans')->willReturn('URL unreachable');

        $content = $this->fileContentGetter->getContentFromUrl($contentType, $url);

        $this->assertFalse($content);
    }

    public function testGetContentFromUrlTransportException(): void
    {
        $url = 'http://example.com';
        $contentType = 'application/json';

        $violations = $this->createMock(ConstraintViolationListInterface::class);
        $violations->method('count')->willReturn(0); // URL considérée valide

        $this->validatorService->method('validate')->willReturn($violations);
        $this->httpClient->method('request')->willThrowException(new TransportException());

        $content = $this->fileContentGetter->getContentFromUrl($contentType, $url);

        $this->assertFalse($content);
    }
}
