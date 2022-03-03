<?php

namespace Hippy\Connector\Cache;

use DateTimeInterface;
use Hippy\Connector\Exception\CacheException;
use Hippy\Connector\Model\ResponseModelInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

interface CacheAdapterInterface
{
    /**
     * @return CacheItemPoolInterface|null
     */
    public function getCache(): ?CacheItemPoolInterface;

    /**
     * @param string $key
     * @return ResponseModelInterface|null
     * @throws CacheException
     * @throws InvalidArgumentException
     */
    public function loadResponse(string $key): ?ResponseModelInterface;

    /**
     * @param string $key
     * @param ResponseModelInterface $response
     * @param DateTimeInterface $ttl
     * @return bool
     * @throws CacheException
     * @throws InvalidArgumentException
     */
    public function storeResponse(string $key, ResponseModelInterface $response, DateTimeInterface $ttl): bool;
}
