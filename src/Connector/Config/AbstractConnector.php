<?php

namespace Hippy\Connector\Connector\Config;

use Hippy\Connector\Cache\CacheInterface;
use Hippy\Connector\Connector\AbstractConnector as BaseConnector;
use Hippy\Connector\Exception\UnknownClientException;
use Hippy\Connector\Log\LoggerHandlerInterface;
use Hippy\Connector\Model\Config\EndpointInterface;
use Hippy\Connector\Model\RequestModelInterface;
use Hippy\Connector\Transformer\ResponseTransformerInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractConnector extends BaseConnector
{
    /** @var int */
    public const CODE = 4;

    /** @var string */
    public const METHOD = 'GET';

    /** @var string */
    public const URI_PATTERN = '__config';

    /**
     * @param int $serviceCode
     * @param ClientInterface $client
     * @param EndpointInterface $config
     * @param ResponseTransformerInterface|null $transformer
     * @param LoggerHandlerInterface|null $logger
     * @param CacheInterface|null $cache
     */
    public function __construct(
        int $serviceCode,
        ClientInterface $client,
        EndpointInterface $config,
        ?ResponseTransformerInterface $transformer = null,
        ?LoggerHandlerInterface $logger = null,
        ?CacheInterface $cache = null
    ) {
        parent::__construct($serviceCode, self::CODE, $client, $config, $transformer, $logger, $cache);
    }

    /**
     * @param RequestModelInterface $request
     * @return ResponseInterface
     * @throws GuzzleException
     */
    protected function execute(RequestModelInterface $request): ResponseInterface
    {
        return $this->client->send(new Request(self::METHOD, self::URI_PATTERN, $request->getHeaders()));
    }

    /**
     * @param RequestModelInterface $request
     * @param string $message
     * @param int $statusCode
     * @return void
     * @throws UnknownClientException
     */
    protected function handleFailure(RequestModelInterface $request, string $message, int $statusCode): void
    {
        if ($statusCode == Response::HTTP_SERVICE_UNAVAILABLE) {
            return;
        }

        throw new UnknownClientException($request, $message, $statusCode);
    }
}
