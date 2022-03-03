<?php

namespace Hippy\Connector\Model\Config;

use Hippy\Model\Collection;
use Hippy\Model\ModelInterface;
use InvalidArgumentException;

class EndpointCollection extends Collection
{
    /**
     * @param ModelInterface $item
     * @return $this
     * @throws InvalidArgumentException
     */
    public function add(ModelInterface $item): EndpointCollection
    {
        if (!($item instanceof EndpointInterface)) {
            throw new InvalidArgumentException('Invalid endpoint configuration instance');
        }

        $this->items[] = $item;
        return $this;
    }
}
