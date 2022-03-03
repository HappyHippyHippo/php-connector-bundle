<?php

namespace Hippy\Connector\Log;

use Psr\Log\LoggerInterface;

interface LoggerAdapterInterface
{
    /**
     * @return LoggerInterface|null
     */
    public function getLogger(): ?LoggerInterface;

    /**
     * @param string $message
     * @param array<int|string, mixed> $context
     * @return void
     */
    public function logRequest(string $message, array $context): void;

    /**
     * @param string $message
     * @param array<int|string, mixed> $context
     * @return void
     */
    public function logResponse(string $message, array $context): void;

    /**
     * @param string $message
     * @param array<int|string, mixed> $context
     * @return void
     */
    public function logCachedResponse(string $message, array $context): void;

    /**
     * @param string $message
     * @param array<int|string, mixed> $context
     * @return void
     */
    public function logException(string $message, array $context): void;
}
