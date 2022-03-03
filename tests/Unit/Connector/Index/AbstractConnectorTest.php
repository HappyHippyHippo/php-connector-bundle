<?php

namespace Hippy\Connector\Tests\Unit\Connector\Index;

use Hippy\Connector\Cache\CacheInterface;
use Hippy\Connector\Connector\Index\AbstractConnector;
use Hippy\Connector\Log\LoggerHandlerInterface;
use Hippy\Connector\Model\Config\EndpointInterface;
use Hippy\Connector\Model\RequestModel;
use Hippy\Connector\Transformer\ResponseTransformerInterface;
use Hippy\Connector\Tests\Unit\Connector\ConnectorTester;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use ReflectionException;
use ReflectionProperty;

/** @coversDefaultClass \Hippy\Connector\Connector\Index\AbstractConnector */
class AbstractConnectorTest extends ConnectorTester
{
    /** @var int */
    private const SERVICE_CODE = 123;

    /** @var ClientInterface&MockObject */
    private ClientInterface $client;

    /** @var EndpointInterface&MockObject */
    private EndpointInterface $config;

    /** @var ResponseTransformerInterface&MockObject */
    private ResponseTransformerInterface $transformer;

    /** @var LoggerHandlerInterface&MockObject */
    private LoggerHandlerInterface $logger;

    /** @var CacheInterface&MockObject */
    private CacheInterface $cache;

    /** @var RequestModel&MockObject */
    private RequestModel $requestModel;

    /** @var ResponseInterface&MockObject */
    private ResponseInterface $response;

    /** @var AbstractConnector&MockObject */
    private AbstractConnector $connector;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->config = $this->createMock(EndpointInterface::class);
        $this->transformer = $this->createMock(ResponseTransformerInterface::class);
        $this->logger = $this->createMock(LoggerHandlerInterface::class);
        $this->cache = $this->createMock(CacheInterface::class);

        $this->requestModel = $this->createMock(RequestModel::class);
        $this->response = $this->createMock(ResponseInterface::class);

        $this->connector = $this->getMockForAbstractClass(
            AbstractConnector::class,
            [self::SERVICE_CODE, $this->client, $this->config, $this->transformer, $this->logger, $this->cache]
        );
    }

    /**
     * @return void
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $check = function (AbstractConnector $instance, $check, string $property) {
            $prop = new ReflectionProperty(AbstractConnector::class, $property);
            $this->assertEquals($check, $prop->getValue($instance));
        };

        $check($this->connector, $this->client, 'client');
        $check($this->connector, $this->config, 'config');
        $check($this->connector, $this->transformer, 'transformer');
        $check($this->connector, $this->logger, 'logger');
        $check($this->connector, $this->cache, 'cache');
    }

    /**
     * @return void
     * @covers ::__construct
     */
    public function testEndpointCode(): void
    {
        $this->assertEquals(1, $this->connector->getEndpointCode());
    }

    /**
     * @return void
     * @throws ReflectionException
     * @covers ::execute
     */
    public function testExecuteReturnClientResponse(): void
    {
        $headers = ['header1' => '__dummy_header_value__'];
        $this->requestModel->expects($this->once())->method('getHeaders')->willReturn($headers);

        $this->executeReturnClientResponse(
            $this->client,
            $this->connector,
            $this->requestModel,
            $this->response,
            function (Request $request) {
                return $request->getMethod() == AbstractConnector::METHOD
                    && $request->getUri()->getPath() == AbstractConnector::URI_PATTERN
                    && $request->getHeaders() == ['header1' => ['__dummy_header_value__']];
            }
        );
    }
}
