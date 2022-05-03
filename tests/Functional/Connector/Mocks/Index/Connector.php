<?php

namespace Hippy\Connector\Tests\Functional\Connector\Mocks\Index;

use GuzzleHttp\ClientInterface;
use Hippy\Connector\Config\Endpoint;
use Hippy\Connector\Connector\AbstractCacheHandler;
use Hippy\Connector\Connector\AbstractLoggerHandler;
use Hippy\Connector\Connector\AbstractResponseHandler;
use Hippy\Connector\Connector\Index\AbstractConnector;

class Connector extends AbstractConnector
{
    public function __construct(
        ClientInterface $client,
        Endpoint $config,
        ?AbstractResponseHandler $transformer = null,
        ?AbstractLoggerHandler $logger = null,
        ?AbstractCacheHandler $cache = null
    ) {
        parent::__construct(100, $client, $config, $transformer, $logger, $cache);
    }
}
