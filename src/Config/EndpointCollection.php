<?php

namespace Hippy\Connector\Config;

use Hippy\Model\Collection;
use Hippy\Model\Model;
use InvalidArgumentException;

class EndpointCollection extends Collection
{
    /**
     * @param Model $item
     * @return $this
     * @throws InvalidArgumentException
     */
    public function add(Model $item): self
    {
        if (!($item instanceof Endpoint)) {
            throw new InvalidArgumentException('Invalid endpoint configuration instance');
        }

        $this->items[] = $item;
        return $this;
    }
}
