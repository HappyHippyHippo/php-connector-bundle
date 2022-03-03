<?php

namespace Hippy\Connector\Log;

use Hippy\Connector\Model\RequestModelInterface;
use Hippy\Connector\Model\ResponseModelInterface;
use Throwable;

interface LoggerHandlerInterface
{
    /**
     * @return LoggerAdapterInterface|null
     */
    public function getAdapter(): ?LoggerAdapterInterface;

    /**
     * @param RequestModelInterface $request
     * @return void
     */
    public function writeRequest(RequestModelInterface $request): void;

    /**
     * @param RequestModelInterface $request
     * @param ResponseModelInterface $response
     * @return void
     */
    public function writeResponse(RequestModelInterface $request, ResponseModelInterface $response): void;

    /**
     * @param RequestModelInterface $request
     * @param ResponseModelInterface $response
     * @return void
     */
    public function writeDryResponse(RequestModelInterface $request, ResponseModelInterface $response): void;

    /**
     * @param RequestModelInterface $request
     * @param ResponseModelInterface $response
     * @return void
     */
    public function writeCachedResponse(RequestModelInterface $request, ResponseModelInterface $response): void;

    /**
     * @param RequestModelInterface $request
     * @param Throwable $exception
     * @return void
     */
    public function writeException(RequestModelInterface $request, Throwable $exception): void;
}
