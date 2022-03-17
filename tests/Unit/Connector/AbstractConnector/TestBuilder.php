<?php

namespace Hippy\Connector\Tests\Unit\Connector\AbstractConnector;

use Hippy\Connector\Config\Endpoint;
use Hippy\Connector\Connector\AbstractCacheHandler;
use Hippy\Connector\Connector\AbstractConnector;
use Hippy\Connector\Connector\AbstractLoggerHandler;
use Hippy\Connector\Connector\AbstractResponseHandler;
use Hippy\Connector\Model\RequestModel;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

abstract class TestBuilder extends TestCase
{
    /** @var ClientInterface&MockObject */
    protected ClientInterface $client;

    /** @var Endpoint&MockObject */
    protected Endpoint $config;

    /** @var AbstractResponseHandler&MockObject */
    protected AbstractResponseHandler $transformer;

    /** @var RequestModel&MockObject */
    protected RequestModel $requestModel;

    /** @var ResponseInterface&MockObject */
    protected ResponseInterface $response;

    /** @var AbstractLoggerHandler&MockObject */
    protected AbstractLoggerHandler $logger;

    /** @var AbstractCacheHandler&MockObject */
    protected AbstractCacheHandler $cache;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->config = $this->getMockBuilder(Endpoint::class)
            ->addMethods(['isCacheEnabled', 'getCacheTTL'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->transformer = $this->createMock(AbstractResponseHandler::class);
        $this->requestModel = $this->createMock(RequestModel::class);
        $this->response = $this->createMock(ResponseInterface::class);
        $this->logger = $this->createMock(AbstractLoggerHandler::class);
        $this->cache = $this->createMock(AbstractCacheHandler::class);
    }

    /**
     * @param array<int, mixed> $args
     * @param callable|null $config
     * @param string[] $methods
     * @return AbstractConnector&MockObject
     */
    protected function getConnector(
        array $args,
        ?callable $config = null,
        array $methods = []
    ): AbstractConnector & MockObject {
        $connector = $this->getMockForAbstractClass(
            AbstractConnector::class,
            $args,
            '',
            true,
            true,
            true,
            $methods
        );

        if (!empty($config)) {
            $config($connector);
        }

        return $connector;
    }
}
