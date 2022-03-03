<?php

namespace Hippy\Connector\Model\Config;

use Hippy\Model\ModelInterface;

interface EndpointInterface extends ModelInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return bool
     */
    public function isCacheEnabled(): bool;

    /**
     * @return int
     */
    public function getCacheTTL(): int;
}
