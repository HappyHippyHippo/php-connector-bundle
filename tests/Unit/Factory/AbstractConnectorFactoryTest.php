<?php

namespace Hippy\Connector\Tests\Unit\Factory  ;

use Hippy\Config\ConfigInterface as BaseConfigInterface;
use Hippy\Connector\Cache\CacheAdapterInterface;
use Hippy\Connector\Connector\ConnectorInterface;
use Hippy\Connector\Factory\AbstractConnectorFactory;
use Hippy\Connector\Factory\Strategy\CreateStrategyInterface;
use Hippy\Connector\Log\LoggerAdapterInterface;
use Hippy\Connector\Model\Config\ConfigInterface;
use Hippy\Connector\Model\Config\EndpointInterface;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;

/** @coversDefaultClass \Hippy\Connector\Factory\AbstractConnectorFactory */
class AbstractConnectorFactoryTest extends TestCase
{
    /** @var BaseConfigInterface&MockObject */
    private BaseConfigInterface $configInterface;

    /** @var ConfigInterface&MockObject */
    private ConfigInterface $config;

    /** @var LoggerAdapterInterface&MockObject */
    private LoggerAdapterInterface $loggerAdapter;

    /** @var CacheAdapterInterface&MockObject */
    private CacheAdapterInterface $cacheAdapter;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->configInterface = $this->createMock(BaseConfigInterface::class);
        $this->config = $this->createMock(ConfigInterface::class);
        $this->loggerAdapter = $this->createMock(LoggerAdapterInterface::class);
        $this->cacheAdapter = $this->createMock(CacheAdapterInterface::class);
    }

    /**
     * @return void
     * @throws ReflectionException
     * @covers ::__construct
     * @covers ::create
     */
    public function testCreateReturnNullIfNoStrategyCanCreateTheRequestedConnector(): void
    {
        $endpointName = '__dummy_endpoint_name__';

        $strategy = $this->createMock(CreateStrategyInterface::class);
        $strategy->expects($this->once())->method('supports')->with($endpointName)->willReturn(false);
        $sut = $this->sut([$strategy]);

        $method = new ReflectionMethod(AbstractConnectorFactory::class, 'create');
        $this->assertNull($method->invoke($sut, $endpointName));
    }

    /**
     * @return void
     * @throws ReflectionException
     * @covers ::__construct
     * @covers ::create
     * @covers ::getConfig
     * @covers ::getClient
     */
    public function testCreateReturnCreatedConnector(): void
    {
        $endpointName = '__dummy_endpoint_name__';

        $config = $this->createMock(EndpointInterface::class);
        $this->config
            ->expects($this->once())
            ->method('getEndpoint')
            ->with($endpointName)
            ->willReturn($config);

        $client = $this->createMock(ClientInterface::class);

        $connector = $this->createMock(ConnectorInterface::class);
        $strategy = $this->createMock(CreateStrategyInterface::class);
        $strategy->expects($this->once())->method('supports')->with($endpointName)->willReturn(true);
        $strategy
            ->expects($this->once())
            ->method('create')
            ->with($client, $config, $this->loggerAdapter, $this->cacheAdapter)
            ->willReturn($connector);

        $sut = $this->sut([$strategy]);

        $property = new ReflectionProperty(AbstractConnectorFactory::class, 'client');
        $property->setValue($sut, $client);

        $method = new ReflectionMethod(AbstractConnectorFactory::class, 'create');
        $this->assertSame($connector, $method->invoke($sut, $endpointName));
    }

    /**
     * @return void
     * @throws ReflectionException
     * @covers ::getClient
     */
    public function testGetClientCreatesOnlyOneInstance(): void
    {
        $config = [
            'base_uri' => 'http://test.domain.com/',
            'http_errors' => true,
            'allow_redirects' => true,
        ];

        $sut = $this->sut();

        $property = new ReflectionProperty(AbstractConnectorFactory::class, 'client');
        $property->setValue($sut, null);

        $this->config->expects($this->once())->method('getClientConfig')->willReturn($config);

        $method = new ReflectionMethod(AbstractConnectorFactory::class, 'getClient');

        $resultFirstCall = $method->invoke($sut, $this->config);
        $resultSecondCall = $method->invoke($sut, $this->config);

        $this->assertEquals($resultFirstCall, $resultSecondCall);

        $this->assertEquals($config['base_uri'], $resultFirstCall->getConfig('base_uri'));
        $this->assertEquals($config['http_errors'], $resultFirstCall->getConfig('http_errors'));
        $this->assertEquals($config['allow_redirects'], $resultFirstCall->getConfig('allow_redirects'));
    }

    /**
     * @param CreateStrategyInterface[] $strategies
     * @return AbstractConnectorFactory
     */
    private function sut(array $strategies = []): AbstractConnectorFactory
    {
        $sut = $this->getMockForAbstractClass(
            AbstractConnectorFactory::class,
            [$strategies, $this->configInterface, $this->loggerAdapter, $this->cacheAdapter],
            '',
            true,
            true,
            true,
            ['getConfig']
        );

        $sut->method('getConfig')->willReturn($this->config);

        return $sut;
    }
}
