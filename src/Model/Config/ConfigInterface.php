<?php

namespace Hippy\Connector\Model\Config;

use Hippy\Model\ModelInterface;

interface ConfigInterface extends ModelInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getClientConfig(): array;

    /**
     * @return string
     */
    public function getLogRequestLevel(): string;

    /**
     * @return string
     */
    public function getLogResponseLevel(): string;

    /**
     * @return string
     */
    public function getLogCachedResponseLevel(): string;

    /**
     * @return string
     */
    public function getLogExceptionLevel(): string;

    /**
     * @param string $name
     * @return EndpointInterface
     */
    public function getEndpoint(string $name): EndpointInterface;
}
