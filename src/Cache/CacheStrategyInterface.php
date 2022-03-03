<?php

namespace Hippy\Connector\Cache;

use Psr\Cache\CacheItemInterface;

interface CacheStrategyInterface
{
    /**
     * @param string $key
     * @return bool
     */
    public function supports(string $key): bool;

    /**
     * @param string $key
     * @return bool
     */
    public function hasItem(string $key): bool;

    /**
     * @param string $key
     * @return CacheItemInterface
     */
    public function getItem(string $key): CacheItemInterface;

    /**
     * @param CacheItemInterface $item
     * @return bool
     */
    public function save(CacheItemInterface $item): bool;
}
