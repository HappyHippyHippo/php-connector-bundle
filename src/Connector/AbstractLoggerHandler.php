<?php

namespace Hippy\Connector\Connector;

use Hippy\Connector\Log\AbstractLoggerAdapter;
use Hippy\Connector\Model\RequestModel;
use Hippy\Connector\Model\ResponseModel;
use Throwable;

abstract class AbstractLoggerHandler
{
    /**
     * @param AbstractLoggerAdapter|null $adapter
     * @param string $requestMsg
     * @param string $responseMsg
     * @param string $cachedResponseMsg
     * @param string $exceptionMsg
     */
    public function __construct(
        protected ?AbstractLoggerAdapter $adapter = null,
        protected string $requestMsg = '',
        protected string $responseMsg = '',
        protected string $cachedResponseMsg = '',
        protected string $exceptionMsg = ''
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    abstract protected function createLogEntrySkeleton(): array;

    /**
     * @param RequestModel $request
     * @return void
     */
    public function writeRequest(RequestModel $request): void
    {
        if (empty($this->adapter)) {
            return;
        }

        $this->adapter->logRequest(
            $this->requestMsg,
            array_merge(
                $this->createLogEntrySkeleton(),
                [
                    'request' => [
                        'headers' => $this->flatHeaders($request->getHeaders()),
                        'params' => $request->jsonSerialize(),
                    ],
                ]
            )
        );
    }

    /**
     * @param RequestModel $request
     * @param ResponseModel $response
     * @return void
     */
    public function writeResponse(RequestModel $request, ResponseModel $response): void
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
     * @param RequestModel $request
     * @param ResponseModel $response
     * @return void
     */
    public function writeDryResponse(RequestModel $request, ResponseModel $response): void
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
     * @param RequestModel $request
     * @param ResponseModel $response
     * @return void
     */
    public function writeCachedResponse(RequestModel $request, ResponseModel $response): void
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
     * @param RequestModel $request
     * @param Throwable $exception
     * @return void
     */
    public function writeException(RequestModel $request, Throwable $exception): void
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
