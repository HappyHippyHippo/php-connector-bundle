<?php

namespace Hippy\Connector\Connector\Index;

use Hippy\Connector\Log\AbstractLoggerHandler as BaseLoggerHandler;

abstract class AbstractLoggerHandler extends BaseLoggerHandler
{
    /**
     * @return array<string, mixed>
     */
    protected function createLogEntrySkeleton(): array
    {
        return [
            'method' => AbstractConnector::METHOD,
            'uri' => AbstractConnector::URI_PATTERN,
        ];
    }
}
