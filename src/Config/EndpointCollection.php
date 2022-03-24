<?php

namespace Hippy\Connector\Config;

use Hippy\Model\Collection;

class EndpointCollection extends Collection
{
    /**
     * @param Endpoint[] $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct(Endpoint::class, $items);
    }
}
