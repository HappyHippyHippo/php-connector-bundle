<?php

namespace Hippy\Connector\Tests\Unit\Connector\Check;

use Hippy\Connector\Config\Endpoint;
use Hippy\Connector\Connector\AbstractCacheHandler;
use Hippy\Connector\Connector\AbstractLoggerHandler;
use Hippy\Connector\Connector\AbstractResponseHandler;
use Hippy\Connector\Connector\Check\AbstractConnector;
use Hippy\Connector\Connector\Check\RequestModel;
use Hippy\Connector\Model\RequestModel as BaseRequestModel;
use Hippy\Connector\Tests\Unit\Connector\ConnectorTester;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use ReflectionException;
use ReflectionProperty;

/** @coversDefaultClass \Hippy\Connector\Connector\Check\AbstractConnector */
class AbstractConnectorTest extends ConnectorTester
{
    /** @var int */
    private const SERVICE_CODE = 123;

    /** @var ClientInterface&MockObject */
    private ClientInterface $client;

    /** @var Endpoint&MockObject */
    private Endpoint $config;

    /** @var AbstractResponseHandler&MockObject */
    private AbstractResponseHandler $transformer;

    /** @var AbstractLoggerHandler&MockObject */
    private AbstractLoggerHandler $logger;

    /** @var AbstractCacheHandler&MockObject */
    private AbstractCacheHandler $cache;

    /** @var AbstractConnector&MockObject */
    private AbstractConnector $connector;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->config = $this->createMock(Endpoint::class);
        $this->transformer = $this->createMock(AbstractResponseHandler::class);
        $this->logger = $this->createMock(AbstractLoggerHandler::class);
        $this->cache = $this->createMock(AbstractCacheHandler::class);

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
        $this->assertEquals(2, $this->connector->getEndpointCode());
    }

    /**
     * @return void
     * @throws ReflectionException
     * @covers ::execute
     */
    public function testExecuteThrowsOnInvalidRequestModel(): void
    {
        $requestModel = $this->createMock(BaseRequestModel::class);
        $response = $this->createMock(ResponseInterface::class);

        $this->expectException(InvalidArgumentException::class);

        $this->executeReturnClientResponse(
            $this->client,
            $this->connector,
            $requestModel,
            $response,
            function () {
            }
        );
    }

    /**
     * @return void
     * @throws ReflectionException
     * @covers ::execute
     */
    public function testExecuteReturnClientResponse(): void
    {
        $headers = ['header1' => '__dummy_header_value__'];

        $requestModel = new RequestModel(true, $headers);
        $response = $this->createMock(ResponseInterface::class);

        $this->executeReturnClientResponse(
            $this->client,
            $this->connector,
            $requestModel,
            $response,
            function (Request $request) {
                $path = $request->getUri()->getPath() . '?' . $request->getUri()->getQuery();

                return $request->getMethod() == AbstractConnector::METHOD
                    && $path == sprintf(AbstractConnector::URI_PATTERN, true)
                    && $request->getHeaders() == ['header1' => ['__dummy_header_value__']];
            }
        );
    }
}
