<?php

namespace Hippy\Connector\Factory;

use Hippy\Config\ConfigInterface as BaseConfigInterface;
use Hippy\Connector\Cache\CacheAdapterInterface;
use Hippy\Connector\Connector\ConnectorInterface;
use Hippy\Connector\Factory\Strategy\CreateStrategyInterface;
use Hippy\Connector\Log\LoggerAdapterInterface;
use Hippy\Connector\Model\Config\ConfigInterface;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

abstract class AbstractConnectorFactory
{
    /** @var CreateStrategyInterface[] */
    protected array $strategies;

    /** @var ClientInterface|null */
    protected ?ClientInterface $client;

    /**
     * @param CreateStrategyInterface[] $strategies
     * @param BaseConfigInterface $config
     * @param LoggerAdapterInterface|null $loggerAdapter
     * @param CacheAdapterInterface|null $cacheAdapter
     */
    public function __construct(
        iterable $strategies,
        protected BaseConfigInterface $config,
        protected ?LoggerAdapterInterface $loggerAdapter = null,
        protected ?CacheAdapterInterface $cacheAdapter = null
    ) {
        $this->strategies = [];
        foreach ($strategies as $strategy) {
            if ($strategy instanceof CreateStrategyInterface) {
                $this->strategies[] = $strategy;
            }
        }

        $this->client = null;
    }

    /**
     * @param string $connectorId
     * @return ConnectorInterface|null
     */
    public function create(string $connectorId): ?ConnectorInterface
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($connectorId)) {
                return $strategy->create(
                    $this->getClient(),
                    $this->getConfig()->getEndpoint($connectorId),
                    $this->loggerAdapter,
                    $this->cacheAdapter
                );
            }
        }

        return null;
    }

    /**
     * @return ClientInterface
     */
    protected function getClient(): ClientInterface
    {
        if ($this->client == null) {
            $this->client = new Client($this->getConfig()->getClientConfig());
        }

        return $this->client;
    }

    /**
     * @return ConfigInterface
     */
    abstract protected function getConfig(): ConfigInterface;
}
