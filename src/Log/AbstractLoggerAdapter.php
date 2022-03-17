<?php

namespace Hippy\Connector\Log;

use Hippy\Config\Config as BaseConfig;
use Hippy\Connector\Config\Config;
use Psr\Log\LoggerInterface;

abstract class AbstractLoggerAdapter
{
    /**
     * @param BaseConfig $config
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        protected BaseConfig $config,
        protected ?LoggerInterface $logger = null,
    ) {
    }

    /**
     * @param string $message
     * @param array<int|string, mixed> $context
     * @return void
     */
    public function logRequest(string $message, array $context): void
    {
        if (empty($this->logger)) {
            return;
        }

        $this->logger->{$this->getConfig()->getLogLevelRequest()}($message, $context);
    }

    /**
     * @param string $message
     * @param array<int|string, mixed> $context
     * @return void
     */
    public function logResponse(string $message, array $context): void
    {
        if (empty($this->logger)) {
            return;
        }

        $this->logger->{$this->getConfig()->getLogLevelResponse()}($message, $context);
    }

    /**
     * @param string $message
     * @param array<int|string, mixed> $context
     * @return void
     */
    public function logCachedResponse(string $message, array $context): void
    {
        if (empty($this->logger)) {
            return;
        }

        $this->logger->{$this->getConfig()->getLogLevelCached()}($message, $context);
    }

    /**
     * @param string $message
     * @param array<int|string, mixed> $context
     * @return void
     */
    public function logException(string $message, array $context): void
    {
        if (empty($this->logger)) {
            return;
        }

        $this->logger->{$this->getConfig()->getLogLevelException()}($message, $context);
    }

    /**
     * @return Config
     */
    abstract protected function getConfig(): Config;
}
