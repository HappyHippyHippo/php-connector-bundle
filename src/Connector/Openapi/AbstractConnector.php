<?php

namespace Hippy\Connector\Connector\Openapi;

use Hippy\Connector\Config\Endpoint;
use Hippy\Connector\Connector\AbstractCacheHandler;
use Hippy\Connector\Connector\AbstractConnector as BaseConnector;
use Hippy\Connector\Connector\AbstractLoggerHandler;
use Hippy\Connector\Connector\AbstractResponseHandler;
use Hippy\Connector\Exception\UnknownClientException;
use Hippy\Connector\Model\RequestModel;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractConnector extends BaseConnector
{
    /** @var int */
    public const CODE = 3;

    /** @var string */
    public const METHOD = 'GET';

    /** @var string */
    public const URI_PATTERN = '__openapi';

    /**
     * @param int $serviceCode
     * @param ClientInterface $client
     * @param Endpoint $config
     * @param AbstractResponseHandler|null $transformer
     * @param AbstractLoggerHandler|null $logger
     * @param AbstractCacheHandler|null $cache
     */
    public function __construct(
        int $serviceCode,
        ClientInterface $client,
        Endpoint $config,
        ?AbstractResponseHandler $transformer = null,
        ?AbstractLoggerHandler $logger = null,
        ?AbstractCacheHandler $cache = null
    ) {
        parent::__construct($serviceCode, self::CODE, $client, $config, $transformer, $logger, $cache);
    }

    /**
     * @param RequestModel $request
     * @return ResponseInterface
     * @throws GuzzleException
     */
    protected function execute(RequestModel $request): ResponseInterface
    {
        return $this->client->send(
            new Request(
                self::METHOD,
                self::URI_PATTERN,
                $request->getHeaders()
            )
        );
    }

    /**
     * @param RequestModel $request
     * @param string $message
     * @param int $statusCode
     * @return void
     */
    protected function handleFailure(RequestModel $request, string $message, int $statusCode): void
    {
        if ($statusCode == Response::HTTP_SERVICE_UNAVAILABLE) {
            return;
        }

        throw new UnknownClientException($request, $message, $statusCode);
    }
}
