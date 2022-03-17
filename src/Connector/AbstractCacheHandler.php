<?php

namespace Hippy\Connector\Connector;

use DateTimeInterface;
use Hippy\Connector\Cache\CacheAdapter;
use Hippy\Connector\Model\RequestModel;
use Hippy\Connector\Model\ResponseModel;

abstract class AbstractCacheHandler
{
    /**
     * @param CacheAdapter $adapter
     */
    public function __construct(protected CacheAdapter $adapter)
    {
    }

    /**
     * @param RequestModel $request
     * @return ResponseModel|null
     */
    abstract public function loadResponse(RequestModel $request): ?ResponseModel;

    /**
     * @param RequestModel $request
     * @param ResponseModel $response
     * @param DateTimeInterface $ttl
     * @return void
     */
    abstract public function storeResponse(
        RequestModel $request,
        ResponseModel $response,
        DateTimeInterface $ttl
    ): void;
}
