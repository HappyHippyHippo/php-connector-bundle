<?php

namespace Hippy\Connector\Tests\Functional\Connector\Mocks;

use Hippy\Connector\Config\Config as ConnectorConfig;
use Hippy\Connector\Log\AbstractLoggerAdapter;

class LoggerAdapter extends AbstractLoggerAdapter
{
    /**
     * @return ConnectorConfig
     */
    protected function getConfig(): ConnectorConfig
    {
        return $this->config->get('connector.config');
    }
}
