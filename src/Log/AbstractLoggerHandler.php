<?php

namespace Hippy\Connector\Log;

use Hippy\Connector\Model\RequestModelInterface;
use Hippy\Connector\Model\ResponseModelInterface;
use Throwable;

abstract class AbstractLoggerHandler implements LoggerHandlerInterface
{
    /**
     * @param LoggerAdapterInterface|null $adapter
     * @param string $requestMsg
     * @param string $responseMsg
     * @param string $cachedResponseMsg
     * @param string $exceptionMsg
     */
    public function __construct(
        protected ?LoggerAdapterInterface $adapter = null,
        protected string $requestMsg = '',
        protected string $responseMsg = '',
        protected string $cachedResponseMsg = '',
        protected string $exceptionMsg = ''
    ) {
    }

    /**
     * @return LoggerAdapterInterface|null
     */
    public function getAdapter(): ?LoggerAdapterInterface
    {
        return $this->adapter;
    }

    /**
     * @return array<string, mixed>
     */
    abstract protected function createLogEntrySkeleton(): array;

    /**
     * @param RequestModelInterface $request
     * @return void
     */
    public function writeRequest(RequestModelInterface $request): void
    {
        if (empty($this->adapter)) {
            return;
        }

        $this->adapter->logRequest($this->requestMsg, $this->createLogEntrySkeleton());
    }

    /**
     * @param RequestModelInterface $request
     * @param ResponseModelInterface $response
     * @return void
     */
    public function writeResponse(RequestModelInterface $request, ResponseModelInterface $response): void
    {
        if (empty($this->adapter)) {
            return;
        }

        $this->adapter->logResponse(
            $this->responseMsg,
            array_merge(
                $this->createLogEntrySkeleton(),
                [
                    'statusCode' => $response->getStatusCode(),
                    'request' => [
                        'headers' => $this->flatHeaders($request->getHeaders()),
                        'params' => $request->jsonSerialize(),
                    ],
                    'response' => [
                        'headers' => $this->flatHeaders($response->getHeaders()),
                        'params' => $response->jsonSerialize(),
                    ],
                ]
            )
        );
    }

    /**
     * @param RequestModelInterface $request
     * @param ResponseModelInterface $response
     * @return void
     */
    public function writeDryResponse(RequestModelInterface $request, ResponseModelInterface $response): void
    {
        if (empty($this->adapter)) {
            return;
        }

        $this->adapter->logResponse(
            $this->responseMsg,
            array_merge(
                $this->createLogEntrySkeleton(),
                [
                    'statusCode' => $response->getStatusCode(),
                    'request' => [
                        'headers' => $this->flatHeaders($request->getHeaders()),
                        'params' => $request->jsonSerialize(),
                    ],
                ]
            )
        );
    }

    /**
     * @param RequestModelInterface $request
     * @param ResponseModelInterface $response
     * @return void
     */
    public function writeCachedResponse(RequestModelInterface $request, ResponseModelInterface $response): void
    {
        if (empty($this->adapter)) {
            return;
        }

        $this->adapter->logCachedResponse(
            $this->cachedResponseMsg,
            array_merge(
                $this->createLogEntrySkeleton(),
                [
                    'statusCode' => $response->getStatusCode(),
                    'request' => [
                        'headers' => $this->flatHeaders($request->getHeaders()),
                        'params' => $request->jsonSerialize(),
                    ],
                ]
            )
        );
    }

    /**
     * @param RequestModelInterface $request
     * @param Throwable $exception
     * @return void
     */
    public function writeException(RequestModelInterface $request, Throwable $exception): void
    {
        if (empty($this->adapter)) {
            return;
        }

        $this->adapter->logException(
            $this->exceptionMsg,
            array_merge(
                $this->createLogEntrySkeleton(),
                [
                    'request' => [
                        'headers' => $this->flatHeaders($request->getHeaders()),
                        'params' => $request->jsonSerialize(),
                    ],
                    'error' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                ]
            )
        );
    }

    /**
     * @param array<string, string|string[]> $headers
     * @return array<string, mixed>
     */
    private function flatHeaders(array $headers): array
    {
        foreach ($headers as $key => $value) {
            if (is_array($value) && count($value) === 1) {
                $headers[$key] = reset($value);
            }
        }
        return $headers;
    }
}
