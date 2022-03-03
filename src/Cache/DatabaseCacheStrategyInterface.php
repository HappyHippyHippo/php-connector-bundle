<?php

namespace Hippy\Connector\Cache;

use Hippy\Repository\RepositoryFactoryInterface;

interface DatabaseCacheStrategyInterface extends CacheStrategyInterface
{
    /**
     * @param RepositoryFactoryInterface $repositoryFactory
     * @return DatabaseCacheStrategyInterface
     */
    public function setRepositoryFactory(RepositoryFactoryInterface $repositoryFactory): DatabaseCacheStrategyInterface;
}
