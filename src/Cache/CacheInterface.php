<?php

namespace Hippy\Connector\Cache;

use DateTimeInterface;
use Hippy\Connector\Model\RequestModelInterface;
use Hippy\Connector\Model\ResponseModelInterface;

interface CacheInterface
{
    /**
     * @return CacheAdapterInterface
     */
    public function getAdapter(): CacheAdapterInterface;

    /**
     * @param RequestModelInterface $request
     * @return ResponseModelInterface|null
     */
    public function loadResponse(RequestModelInterface $request): ?ResponseModelInterface;

    /**
     * @param RequestModelInterface $request
     * @param ResponseModelInterface $response
     * @param DateTimeInterface $ttl
     * @return void
     */
    public function storeResponse(
        RequestModelInterface $request,
        ResponseModelInterface $response,
        DateTimeInterface $ttl
    ): void;
}
