<?php

namespace Hippy\Connector\Connector;

use DateTime;
use Hippy\Connector\Config\Endpoint;
use Hippy\Connector\Exception\ConnectionException;
use Hippy\Connector\Exception\InvalidResponseContentException;
use Hippy\Connector\Exception\UnknownClientException;
use Hippy\Connector\Model\RequestModel;
use Hippy\Connector\Model\ResponseModel;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

abstract class AbstractConnector
{
    /**
     * @param int $serviceCode
     * @param int $endpointCode
     * @param ClientInterface $client
     * @param Endpoint $config
     * @param AbstractResponseHandler|null $transformer
     * @param AbstractLoggerHandler|null $logger
     * @param AbstractCacheHandler|null $cache
     */
    public function __construct(
        protected int $serviceCode,
        protected int $endpointCode,
        protected ClientInterface $client,
        protected Endpoint $config,
        protected ?AbstractResponseHandler $transformer = null,
        protected ?AbstractLoggerHandler $logger = null,
        protected ?AbstractCacheHandler $cache = null
    ) {
    }

    /**
     * @return int
     */
    public function getServiceCode(): int
    {
        return $this->serviceCode;
    }

    /**
     * @return int
     */
    public function getEndpointCode(): int
    {
        return $this->endpointCode;
    }

    /**
     * @return int
     */
    public function getExpectedStatusCode(): int
    {
        return Response::HTTP_OK;
    }

    /**
     * @param RequestModel $request
     * @return ResponseModel
     * @throws ConnectionException
     */
    public function request(RequestModel $request): ResponseModel
    {
        try {
            // log request
            $this->logRequest($request);

            // check if the response is in cache
            $response = $this->getCachedResponse($request);
            if (!empty($response)) {
                $this->logCachedResponse($request, $response);
                return $response;
            }

            // execute request
            $response = $this->execute($request);

            // handle error response
            $success = true;
            $statusCode = $response->getStatusCode();
            if ($statusCode != $this->getExpectedStatusCode()) {
                $success = false;
                $this->handleFailure($request, $response->getBody(), $statusCode);
            }

            // parse response
            $response = empty($this->transformer)
                ? new ResponseModel($response)
                : $this->transformer->transform($response);

            // log response
            $this->logResponse($success, $request, $response);

            // cache success response
            if ($success) {
                $this->cacheResponse($request, $response);
            }

            return $response;
        } catch (GuzzleException | InvalidResponseContentException $exception) {
            $this->logException($request, $exception);

            throw new ConnectionException($request, $exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param RequestModel $request
     * @return ResponseInterface
     * @throws GuzzleException
     */
    abstract protected function execute(RequestModel $request): ResponseInterface;

    /**
     * @param RequestModel $request
     * @param string $message
     * @param int $statusCode
     * @return void
     */
    protected function handleFailure(RequestModel $request, string $message, int $statusCode): void
    {
        throw new UnknownClientException($request, $message, $statusCode);
    }

    /**
     * @param RequestModel $request
     * @return void
     */
    private function logRequest(RequestModel $request): void
    {
        if (!empty($this->logger)) {
            $this->logger->writeRequest($request);
        }
    }

    /**
     * @param bool $success
     * @param RequestModel $request
     * @param ResponseModel $response
     * @return void
     */
    private function logResponse(bool $success, RequestModel $request, ResponseModel $response): void
    {
        if (!empty($this->logger)) {
            if ($success) {
                $this->logger->writeDryResponse($request, $response);
                return;
            }
            $this->logger->writeResponse($request, $response);
        }
    }

    /**
     * @param RequestModel $request
     * @param ResponseModel $response
     * @return void
     */
    private function logCachedResponse(RequestModel $request, ResponseModel $response): void
    {
        if (!empty($this->logger)) {
            $this->logger->writeCachedResponse($request, $response);
        }
    }

    /**
     * @param RequestModel $request
     * @param Throwable $exception
     * @return void
     */
    private function logException(RequestModel $request, Throwable $exception): void
    {
        if (!empty($this->logger)) {
            $this->logger->writeException($request, $exception);
        }
    }

    /**
     * @param RequestModel $request
     * @return ResponseModel|null
     */
    private function getCachedResponse(RequestModel $request): ?ResponseModel
    {
        if (empty($this->cache) || !$this->config->isCacheEnabled()) {
            return null;
        }

        return $this->cache->loadResponse($request);
    }

    /**
     * @param RequestModel $request
     * @param ResponseModel $response
     * @return void
     */
    private function cacheResponse(RequestModel $request, ResponseModel $response): void
    {
        if (empty($this->cache) || !$this->config->isCacheEnabled()) {
            return;
        }

        $ttl = new DateTime('9999-12-31 23:59:59');
        $seconds = $this->config->getCacheTTL();
        if ($seconds != 0) {
            $ttl->setTimestamp(time() + $seconds);
        }

        $this->cache->storeResponse($request, $response, $ttl);
    }
}
