<?php

namespace Hippy\Connector\Factory\Strategy;

use Hippy\Connector\Cache\CacheAdapterInterface;
use Hippy\Connector\Connector\ConnectorInterface;
use Hippy\Connector\Log\LoggerAdapterInterface;
use Hippy\Connector\Model\Config\EndpointInterface;
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
     * @param EndpointInterface $config
     * @param LoggerAdapterInterface|null $loggerAdapter
     * @param CacheAdapterInterface|null $cacheAdapter
     * @return ConnectorInterface
     */
    public function create(
        ClientInterface $client,
        EndpointInterface $config,
        ?LoggerAdapterInterface $loggerAdapter = null,
        ?CacheAdapterInterface $cacheAdapter = null
    ): ConnectorInterface;
}
