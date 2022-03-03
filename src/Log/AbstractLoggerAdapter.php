<?php

namespace Hippy\Connector\Log;

use Hippy\Config\ConfigInterface as BaseConfigInterface;
use Hippy\Connector\Model\Config\ConfigInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractLoggerAdapter implements LoggerAdapterInterface
{
    /**
     * @param BaseConfigInterface $config
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        protected BaseConfigInterface $config,
        protected ?LoggerInterface $logger = null,
    ) {
    }

    /**
     * @return LoggerInterface|null
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
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

        $this->logger->{$this->getConfig()->getLogRequestLevel()}($message, $context);
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

        $this->logger->{$this->getConfig()->getLogResponseLevel()}($message, $context);
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

        $this->logger->{$this->getConfig()->getLogCachedResponseLevel()}($message, $context);
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

        $this->logger->{$this->getConfig()->getLogExceptionLevel()}($message, $context);
    }

    /**
     * @return ConfigInterface
     */
    abstract protected function getConfig(): ConfigInterface;
}
