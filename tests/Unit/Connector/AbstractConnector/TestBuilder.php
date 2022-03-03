<?php

namespace Hippy\Connector\Tests\Unit\Connector\AbstractConnector;

use Hippy\Connector\Cache\CacheInterface;
use Hippy\Connector\Connector\AbstractConnector;
use Hippy\Connector\Log\LoggerHandlerInterface;
use Hippy\Connector\Model\Config\EndpointInterface;
use Hippy\Connector\Model\RequestModelInterface;
use Hippy\Connector\Transformer\ResponseTransformerInterface;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

abstract class TestBuilder extends TestCase
{
    /** @var ClientInterface&MockObject */
    protected ClientInterface $client;

    /** @var EndpointInterface&MockObject */
    protected EndpointInterface $config;

    /** @var ResponseTransformerInterface&MockObject */
    protected ResponseTransformerInterface $transformer;

    /** @var RequestModelInterface&MockObject */
    protected RequestModelInterface $requestModel;

    /** @var ResponseInterface&MockObject */
    protected ResponseInterface $response;

    /** @var LoggerHandlerInterface&MockObject */
    protected LoggerHandlerInterface $logger;

    /** @var CacheInterface&MockObject */
    protected CacheInterface $cache;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->config = $this->createMock(EndpointInterface::class);
        $this->transformer = $this->createMock(ResponseTransformerInterface::class);
        $this->requestModel = $this->createMock(RequestModelInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
        $this->logger = $this->createMock(LoggerHandlerInterface::class);
        $this->cache = $this->createMock(CacheInterface::class);
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
