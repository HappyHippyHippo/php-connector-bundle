<?php

namespace Hippy\Connector\Connector\Check;

use Hippy\Connector\Config\Endpoint;
use Hippy\Connector\Connector\AbstractCacheHandler;
use Hippy\Connector\Connector\AbstractConnector as BaseConnector;
use Hippy\Connector\Connector\AbstractLoggerHandler;
use Hippy\Connector\Connector\AbstractResponseHandler;
use Hippy\Connector\Model\RequestModel as BaseRequestModel;
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
     * @param BaseRequestModel $request
     * @return ResponseInterface
     * @throws GuzzleException
     */
    protected function execute(BaseRequestModel $request): ResponseInterface
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
