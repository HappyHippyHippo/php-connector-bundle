<?php

namespace Hippy\Connector\Factory;

use Hippy\Config\Config as BaseConfig;
use Hippy\Connector\Cache\CacheAdapter;
use Hippy\Connector\Config\Config;
use Hippy\Connector\Connector\AbstractConnector;
use Hippy\Connector\Factory\Strategy\CreateStrategyInterface;
use Hippy\Connector\Log\AbstractLoggerAdapter;
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
     * @param BaseConfig $config
     * @param AbstractLoggerAdapter|null $loggerAdapter
     * @param CacheAdapter|null $cacheAdapter
     */
    public function __construct(
        iterable $strategies,
        protected BaseConfig $config,
        protected ?AbstractLoggerAdapter $loggerAdapter = null,
        protected ?CacheAdapter $cacheAdapter = null
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
     * @return AbstractConnector|null
     */
    public function create(string $connectorId): ?AbstractConnector
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
     * @return Config
     */
    abstract protected function getConfig(): Config;
}
