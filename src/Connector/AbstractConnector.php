<?php

namespace Hippy\Connector\Connector;

use DateTime;
use Hippy\Connector\Cache\CacheInterface;
use Hippy\Connector\Exception\ConnectionException;
use Hippy\Connector\Exception\InvalidResponseContentException;
use Hippy\Connector\Exception\UnknownClientException;
use Hippy\Connector\Log\LoggerHandlerInterface;
use Hippy\Connector\Model\Config\EndpointInterface;
use Hippy\Connector\Model\RequestModelInterface;
use Hippy\Connector\Model\ResponseModel;
use Hippy\Connector\Model\ResponseModelInterface;
use Hippy\Connector\Transformer\ResponseTransformerInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/** @SuppressWarnings(PHPMD.CouplingBetweenObjects) */
abstract class AbstractConnector implements ConnectorInterface
{
    /** @var int */
    protected int $serviceCode;

    /** @var int */
    protected int $endpointCode;

    /** @var ClientInterface */
    protected ClientInterface $client;

    /** @var EndpointInterface */
    protected EndpointInterface $config;

    /** @var ResponseTransformerInterface|null */
    protected ?ResponseTransformerInterface $transformer;

    /** @var LoggerHandlerInterface|null */
    protected ?LoggerHandlerInterface $logger;

    /** @var CacheInterface|null */
    protected ?CacheInterface $cache;

    /**
     * @param int $serviceCode
     * @param int $endpointCode
     * @param ClientInterface $client
     * @param EndpointInterface $config
     * @param ResponseTransformerInterface|null $transformer
     * @param LoggerHandlerInterface|null $logger
     * @param CacheInterface|null $cache
     */
    public function __construct(
        int $serviceCode,
        int $endpointCode,
        ClientInterface $client,
        EndpointInterface $config,
        ?ResponseTransformerInterface $transformer = null,
        ?LoggerHandlerInterface $logger = null,
        ?CacheInterface $cache = null
    ) {
        $this->serviceCode = $serviceCode;
        $this->endpointCode = $endpointCode;
        $this->client = $client;
        $this->config = $config;
        $this->transformer = $transformer;
        $this->logger = $logger;
        $this->cache = $cache;
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
     * @param RequestModelInterface $request
     * @return ResponseModelInterface
     * @throws ConnectionException
     */
    public function request(RequestModelInterface $request): ResponseModelInterface
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
     * @param RequestModelInterface $request
     * @return ResponseInterface
     * @throws GuzzleException
     */
    abstract protected function execute(RequestModelInterface $request): ResponseInterface;

    /**
     * @param RequestModelInterface $request
     * @param string $message
     * @param int $statusCode
     * @return void
     */
    protected function handleFailure(RequestModelInterface $request, string $message, int $statusCode): void
    {
        throw new UnknownClientException($request, $message, $statusCode);
    }

    /**
     * @param RequestModelInterface $request
     * @return void
     */
    private function logRequest(RequestModelInterface $request): void
    {
        if (!empty($this->logger)) {
            $this->logger->writeRequest($request);
        }
    }

    /**
     * @param bool $success
     * @param RequestModelInterface $request
     * @param ResponseModelInterface $response
     * @return void
     */
    private function logResponse(bool $success, RequestModelInterface $request, ResponseModelInterface $response): void
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
     * @param RequestModelInterface $request
     * @param ResponseModelInterface $response
     * @return void
     */
    private function logCachedResponse(RequestModelInterface $request, ResponseModelInterface $response): void
    {
        if (!empty($this->logger)) {
            $this->logger->writeCachedResponse($request, $response);
        }
    }

    /**
     * @param RequestModelInterface $request
     * @param Throwable $exception
     * @return void
     */
    private function logException(RequestModelInterface $request, Throwable $exception): void
    {
        if (!empty($this->logger)) {
            $this->logger->writeException($request, $exception);
        }
    }

    /**
     * @param RequestModelInterface $request
     * @return ResponseModelInterface|null
     */
    private function getCachedResponse(RequestModelInterface $request): ?ResponseModelInterface
    {
        if (empty($this->cache) || !$this->config->isCacheEnabled()) {
            return null;
        }

        return $this->cache->loadResponse($request);
    }

    /**
     * @param RequestModelInterface $request
     * @param ResponseModelInterface $response
     * @return void
     */
    private function cacheResponse(RequestModelInterface $request, ResponseModelInterface $response): void
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
