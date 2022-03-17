<?php

namespace Hippy\Connector\Cache;

use DateTimeInterface;
use Hippy\Connector\Exception\CacheException;
use Hippy\Connector\Model\ResponseModel;
use Psr\Cache\InvalidArgumentException;

class CacheAdapter
{
    /**
     * @param CacheProxy|null $proxy
     */
    public function __construct(private ?CacheProxy $proxy = null)
    {
    }

    /**
     * @param string $key
     * @return ResponseModel|null
     * @throws CacheException
     * @throws InvalidArgumentException
     */
    public function loadResponse(string $key): ?ResponseModel
    {
        if (empty($this->proxy) || !$this->proxy->hasItem($key)) {
            return null;
        }

        return $this->proxy->getItem($key)->get();
    }

    /**
     * @param string $key
     * @param ResponseModel $response
     * @param DateTimeInterface $ttl
     * @return bool
     * @throws CacheException
     * @throws InvalidArgumentException
     */
    public function storeResponse(string $key, ResponseModel $response, DateTimeInterface $ttl): bool
    {
        if (empty($this->proxy)) {
            return false;
        }

        $item = $this->proxy->getItem($key);
        $item->set($response);
        $item->expiresAt($ttl);
        $this->proxy->save($item);

        return true;
    }
}
