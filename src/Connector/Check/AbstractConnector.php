<?php

namespace Hippy\Connector\Connector\Check;

use Hippy\Connector\Cache\CacheInterface;
use Hippy\Connector\Connector\AbstractConnector as BaseConnector;
use Hippy\Connector\Log\LoggerHandlerInterface;
use Hippy\Connector\Model\Config\EndpointInterface;
use Hippy\Connector\Model\RequestModelInterface;
use Hippy\Connector\Transformer\ResponseTransformerInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractConnector extends BaseConnector
{
    /** @var int */
    public const CODE = 2;

    /** @var string */
    public const METHOD = 'GET';

    /** @var string */
    public const URI_PATTERN = '__check?deep=%d';

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
        if (!($request instanceof RequestModel)) {
            throw new InvalidArgumentException('Invalid request model : ' . get_class($request));
        }

        return $this->client->send(
            new Request(
                self::METHOD,
                sprintf(self::URI_PATTERN, $request->isDeep()),
                $request->getHeaders()
            )
        );
    }
}
