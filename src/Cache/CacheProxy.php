<?php

namespace Hippy\Connector\Cache;

use Hippy\Connector\Exception\CacheException;
use Hippy\Repository\RepositoryFactoryInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

class CacheProxy implements CacheItemPoolInterface
{
    /** @var CacheStrategyInterface[] */
    private array $strategies;

    /**
     * @param CacheStrategyInterface[] $strategies
     */
    public function __construct(iterable $strategies)
    {
        $this->strategies = [];
        foreach ($strategies as $strategy) {
            if ($strategy instanceof CacheStrategyInterface) {
                $this->strategies[] = $strategy;
            }
        }
    }

    /**
     * @param RepositoryFactoryInterface $repositoryFactory
     * @return $this
     */
    public function setRepositoryFactory(RepositoryFactoryInterface $repositoryFactory): self
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy instanceof DatabaseCacheStrategyInterface) {
                $strategy->setRepositoryFactory($repositoryFactory);
            }
        }
        return $this;
    }

    /**
     * @param string $key
     * @return CacheItemInterface
     * @throws CacheException
     * @throws InvalidArgumentException
     */
    public function getItem(string $key): CacheItemInterface
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($key)) {
                return $strategy->getItem($key);
            }
        }
        throw new CacheException("No cache strategy to handle '" . $key . "' item");
    }

    /**
     * @param string[] $keys
     * @return CacheItemInterface[]
     * @throws CacheException
     */
    public function getItems(array $keys = []): array
    {
        throw new CacheException("Not implemented");
    }

    /**
     * @param string $key
     * @return bool
     * @throws CacheException
     */
    public function hasItem(string $key): bool
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($key)) {
                return $strategy->hasItem($key);
            }
        }
        throw new CacheException("No cache strategy to handle '" . $key . "' item");
    }

    /**
     * @return bool
     * @throws CacheException
     */
    public function clear(): bool
    {
        throw new CacheException("Not implemented");
    }

    /**
     * @param string $key
     * @return bool
     * @throws CacheException
     */
    public function deleteItem(string $key): bool
    {
        throw new CacheException("Not implemented");
    }

    /**
     * @param string[] $keys
     * @return bool
     * @throws CacheException
     */
    public function deleteItems(array $keys): bool
    {
        throw new CacheException("Not implemented");
    }

    /**
     * @param CacheItemInterface $item
     * @return bool
     * @throws CacheException
     */
    public function save(CacheItemInterface $item): bool
    {
        $key = $item->getKey();
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($key)) {
                return $strategy->save($item);
            }
        }
        throw new CacheException("No cache strategy to handle '" . $key . "' item");
    }

    /**
     * @param CacheItemInterface $item
     * @return bool
     * @throws CacheException
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        throw new CacheException("Not implemented");
    }

    /**
     * @return bool
     * @throws CacheException
     */
    public function commit(): bool
    {
        throw new CacheException("Not implemented");
    }
}
