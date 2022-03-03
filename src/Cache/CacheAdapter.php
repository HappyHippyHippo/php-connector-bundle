<?php

namespace Hippy\Connector\Cache;

use DateTimeInterface;
use Hippy\Connector\Exception\CacheException;
use Hippy\Connector\Model\ResponseModelInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

class CacheAdapter implements CacheAdapterInterface
{
    /**
     * @param CacheItemPoolInterface|null $cache
     */
    public function __construct(private ?CacheItemPoolInterface $cache = null)
    {
    }

    /**
     * @return CacheItemPoolInterface|null
     */
    public function getCache(): ?CacheItemPoolInterface
    {
        return $this->cache;
    }

    /**
     * @param string $key
     * @return ResponseModelInterface|null
     * @throws CacheException
     * @throws InvalidArgumentException
     */
    public function loadResponse(string $key): ?ResponseModelInterface
    {
        if (empty($this->cache) || !$this->cache->hasItem($key)) {
            return null;
        }

        return $this->cache->getItem($key)->get();
    }

    /**
     * @param string $key
     * @param ResponseModelInterface $response
     * @param DateTimeInterface $ttl
     * @return bool
     * @throws CacheException
     * @throws InvalidArgumentException
     */
    public function storeResponse(string $key, ResponseModelInterface $response, DateTimeInterface $ttl): bool
    {
        if (empty($this->cache)) {
            return false;
        }

        $item = $this->cache->getItem($key);
        $item->set($response);
        $item->expiresAt($ttl);
        $this->cache->save($item);

        return true;
    }
}
