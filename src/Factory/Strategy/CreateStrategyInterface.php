<?php

namespace Hippy\Connector\Factory\Strategy;

use Hippy\Connector\Cache\CacheAdapter;
use Hippy\Connector\Config\Endpoint;
use Hippy\Connector\Connector\AbstractConnector;
use Hippy\Connector\Log\AbstractLoggerAdapter;
use GuzzleHttp\ClientInterface;

interface CreateStrategyInterface
{
    /**
     * @param string $connectorId
     * @return bool
     */
    public function supports(string $connectorId): bool;

    /**
     * @param ClientInterface $client
     * @param Endpoint $config
     * @param AbstractLoggerAdapter|null $loggerAdapter
     * @param CacheAdapter|null $cacheAdapter
     * @return AbstractConnector
     */
    public function create(
        ClientInterface $client,
        Endpoint $config,
        ?AbstractLoggerAdapter $loggerAdapter = null,
        ?CacheAdapter $cacheAdapter = null
    ): AbstractConnector;
}
