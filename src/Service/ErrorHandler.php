<?php

namespace Zhortein\SymfonyToolboxBundle\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ErrorHandler
{
    public function __construct(
        protected readonly TranslatorInterface $translator,
        protected ?LoggerInterface $logger = null,
    ) {
    }

    public function setLogger(?LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
