<?php

namespace Zhortein\SymfonyToolboxBundle\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zhortein\SymfonyToolboxBundle\Enum\Action;

class FileContentGetter
{
    public const int DEFAULT_HTTPCLIENT_TIMEOUT = 1800;

    protected HttpClientInterface $httpClient;
    protected static int $timeout = self::DEFAULT_HTTPCLIENT_TIMEOUT;

    public function __construct(
        protected readonly DataTypeValidator $validator,
        protected readonly ?TranslatorInterface $translator,
        protected ?LoggerInterface $logger = null,
        ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClient::create();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    public static function setTimeout(int $timeout = self::DEFAULT_HTTPCLIENT_TIMEOUT): void
    {
        self::$timeout = $timeout;
    }

    public static function getTimeout(): int
    {
        return self::$timeout;
    }

    /**
     * Retrieves content from a URL.
     *
     * @return mixed returns the content, or false on failure
     */
    public function getContentFromUrl(string $contentType, ?string $url, bool $modeRaw = false, ?string $token = null): mixed
    {
        if (null === $url || !$this->validator->urlValid($url)) {
            $message = $this->translator ? $this->translator->trans('error.invalid_url', [], 'zhortein_symfony_toolbox-errors') : 'Invalid URL';
            throw new \InvalidArgumentException($message);
        }

        $params = ['timeout' => self::$timeout];
        if (null !== $token) {
            $params['headers'] = ['Authorization' => 'Bearer '.$token];
        }

        try {
            $response = $this->httpClient->request('GET', $url, $params);

            if (Response::HTTP_OK !== $response->getStatusCode()) {
                $this->logError(
                    $contentType,
                    $url,
                    $this->translator ? $this->translator->trans('error.url_unreachable', [], 'zhortein_symfony_toolbox-errors') :
                    'URL unreachable'
                );

                return false;
            }

            return $modeRaw ? $response->getContent() : $this->decodeJsonContent($response->getContent(), $contentType);
        } catch (TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            $this->logException($e, $contentType, $url, __CLASS__.'->'.__METHOD__.'()', $token);

            return false;
        }
    }

    /**
     * Retrieves JSON content from a URL.
     *
     * @return mixed returns the content, or false on failure
     */
    public function getJsonContentFromUrl(string $contentType, ?string $url, ?string $token = null): mixed
    {
        return $this->getContentFromUrl($contentType, $url, false, $token);
    }

    /**
     * Retrieves raw content from a URL.
     *
     * @return mixed returns the content, or false on failure
     */
    public function getRawContentFromUrl(string $contentType, ?string $url, ?string $token = null): mixed
    {
        return $this->getContentFromUrl($contentType, $url, true, $token);
    }

    /**
     * Fetches raw content from a URL and saves it locally.
     */
    public function getAndSaveRawContentFromUrl(string $contentType, ?string $url, string $downloadedFilename, ?object $object = null, ?string $token = null): bool
    {
        $content = $this->getRawContentFromUrl($contentType, $url, $token);

        if (false === $content || false === file_put_contents($downloadedFilename, $content)) {
            $this->logError(
                $contentType,
                $url,
                $this->translator ? $this->translator->trans('error.download_failed', [], 'zhortein_symfony_toolbox-errors') : sprintf('Download failed for %s', $contentType),
                $object
            );

            return false;
        }

        $this->logger?->notice(sprintf('%s : Download successful', $contentType), [
            'action' => Action::ACT_DOWNLOAD->value,
            'title' => $this->translator ? $this->translator->trans('file_content_getter.download_successful', [], 'zhortein_symfony_toolbox-file-content-getter') : 'Download successful',
            'description' => $this->translator ? $this->translator->trans('file_content_getter.successfully_downloaded_file', ['%filename%' => $downloadedFilename], 'zhortein_symfony_toolbox-file-content-getter') : sprintf('Successfully downloaded file %s (%s)', $downloadedFilename, $contentType),
            'content_type' => $contentType,
            'url' => $url,
            'context' => __CLASS__.'->'.__METHOD__.'()',
            'object' => $object ? get_class($object) : null,
            'object_id' => (null !== $object && method_exists($object, 'getId')) ? $object->getId() : null,
        ]);

        return true;
    }

    /**
     * Decodes JSON content from a response, logging any decoding errors.
     *
     * @return mixed returns the content, or false on failure
     */
    private function decodeJsonContent(string $content, string $contentType): mixed
    {
        try {
            return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $title = $contentType.': ';
            $title .= $this->translator ? $this->translator->trans('error.json_decode_error', [], 'zhortein_symfony_toolbox-errors') : 'JSON decoding error';
            $this->logger?->error($title, [
                'title' => $title,
                'context' => __CLASS__.'->'.__METHOD__.'()',
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->logException($e, $contentType, null, __CLASS__.'->'.__METHOD__.'()');

            return false;
        }
    }

    /**
     * Logs an error message with structured data.
     */
    private function logError(string $contentType, ?string $url, string $message, ?object $object = null): void
    {
        $translatedDescription = $this->translator
            ? $this->translator->trans('error.fetch_url', ['%url%' => $url], 'zhortein_symfony_toolbox-errors')
            : '';

        $this->logger?->error($message, [
            'title' => $message,
            'description' => $translatedDescription,
            'content_type' => $contentType,
            'url' => $url,
            'context' => __CLASS__.'->'.__METHOD__.'()',
            'object' => $object ? get_class($object) : null,
            'object_id' => (null !== $object && method_exists($object, 'getId')) ? $object->getId() : null,
        ]);
    }

    /**
     * Logs exceptions with contextual data for debugging.
     */
    private function logException(\Exception|\Throwable $e, string $contentType, ?string $url, ?string $callerContext, ?string $token = null): void
    {
        $this->logger?->error($e->getMessage(), [
            'title' => $this->translator ? $this->translator->trans('error.exception_occurred', [], 'zhortein_symfony_toolbox-errors') : 'Exception',
            'url' => $url,
            'content_type' => $contentType,
            'token' => $token ? '***' : '',
            'context' => $callerContext,
            'exception' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
