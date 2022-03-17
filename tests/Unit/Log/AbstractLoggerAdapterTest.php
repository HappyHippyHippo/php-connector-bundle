<?php

namespace Hippy\Connector\Tests\Unit\Log;

use Hippy\Config\Config as BaseConfig;
use Hippy\Connector\Config\Config;
use Hippy\Connector\Log\AbstractLoggerAdapter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/** @coversDefaultClass \Hippy\Connector\Log\AbstractLoggerAdapter */
class AbstractLoggerAdapterTest extends TestCase
{
    /** @var string */
    private const MESSAGE = '__dummy_message__';

    /** @var string[] */
    private const CONTEXT = [
        'key1' => '__dummy_data_1__',
        'key2' => '__dummy_data_2__',
        'key3' => '__dummy_data_3__',
    ];

    /** @var BaseConfig&MockObject */
    private BaseConfig $configInterface;

    /** @var Config&MockObject */
    private Config $config;

    /** @var LoggerInterface&MockObject */
    private LoggerInterface $logger;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->configInterface = $this->createMock(BaseConfig::class);
        $this->config = $this->getMockBuilder(Config::class)
            ->addMethods(['getLogLevelRequest', 'getLogLevelResponse', 'getLogLevelCached', 'getLogLevelException'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    /**
     * @return void
     * @covers ::__construct()
     * @covers ::logRequest
     */
    public function testLogRequestWithNoLogger(): void
    {
        $this->config->expects($this->never())->method('getLogLevelRequest');
        $this->config->expects($this->never())->method('getLogLevelResponse');
        $this->config->expects($this->never())->method('getLogLevelCached');
        $this->config->expects($this->never())->method('getLogLevelException');

        $adapter = $this->sut([$this->configInterface]);
        $adapter->logRequest(self::MESSAGE, self::CONTEXT);
    }

    /**
     * @return void
     * @covers ::__construct()
     * @covers ::logRequest
     */
    public function testLogRequest(): void
    {
        $level = 'info';
        $this->config->expects($this->once())->method('getLogLevelRequest')->willReturn($level);

        $this->logger
            ->expects($this->once())
            ->method($level)
            ->with(self::MESSAGE, self::CONTEXT);

        $adapter = $this->sut([$this->configInterface, $this->logger]);
        $adapter->logRequest(self::MESSAGE, self::CONTEXT);
    }

    /**
     * @return void
     * @covers ::__construct()
     * @covers ::logResponse
     */
    public function testLogResponseWithNoLogger(): void
    {
        $this->config->expects($this->never())->method('getLogLevelRequest');
        $this->config->expects($this->never())->method('getLogLevelResponse');
        $this->config->expects($this->never())->method('getLogLevelCached');
        $this->config->expects($this->never())->method('getLogLevelException');

        $adapter = $this->sut([$this->configInterface]);
        $adapter->logResponse(self::MESSAGE, self::CONTEXT);
    }

    /**
     * @return void
     * @covers ::__construct()
     * @covers ::logResponse
     */
    public function testLogResponse(): void
    {
        $level = 'info';
        $this->config->expects($this->once())->method('getLogLevelResponse')->willReturn($level);

        $this->logger
            ->expects($this->once())
            ->method($level)
            ->with(self::MESSAGE, self::CONTEXT);

        $adapter = $this->sut([$this->configInterface, $this->logger]);
        $adapter->logResponse(self::MESSAGE, self::CONTEXT);
    }

    /**
     * @return void
     * @covers ::__construct()
     * @covers ::logCachedResponse
     */
    public function testLogCachedResponseWithNoLogger(): void
    {
        $this->config->expects($this->never())->method('getLogLevelRequest');
        $this->config->expects($this->never())->method('getLogLevelResponse');
        $this->config->expects($this->never())->method('getLogLevelCached');
        $this->config->expects($this->never())->method('getLogLevelException');

        $adapter = $this->sut([$this->configInterface]);
        $adapter->logCachedResponse(self::MESSAGE, self::CONTEXT);
    }

    /**
     * @return void
     * @covers ::__construct()
     * @covers ::logCachedResponse
     */
    public function testLogCachedResponse(): void
    {
        $level = 'info';
        $this->config->expects($this->once())->method('getLogLevelCached')->willReturn($level);

        $this->logger
            ->expects($this->once())
            ->method($level)
            ->with(self::MESSAGE, self::CONTEXT);

        $adapter = $this->sut([$this->configInterface, $this->logger]);
        $adapter->logCachedResponse(self::MESSAGE, self::CONTEXT);
    }

    /**
     * @return void
     * @covers ::__construct()
     * @covers ::logException
     */
    public function testLogExceptionWithNoLogger(): void
    {
        $this->config->expects($this->never())->method('getLogLevelRequest');
        $this->config->expects($this->never())->method('getLogLevelResponse');
        $this->config->expects($this->never())->method('getLogLevelCached');
        $this->config->expects($this->never())->method('getLogLevelException');

        $adapter = $this->sut([$this->configInterface]);
        $adapter->logException(self::MESSAGE, self::CONTEXT);
    }

    /**
     * @return void
     * @covers ::__construct()
     * @covers ::logException
     */
    public function testLogException(): void
    {
        $level = 'info';
        $this->config->expects($this->once())->method('getLogLevelException')->willReturn($level);

        $this->logger
            ->expects($this->once())
            ->method($level)
            ->with(self::MESSAGE, self::CONTEXT);

        $adapter = $this->sut([$this->configInterface, $this->logger]);
        $adapter->logException(self::MESSAGE, self::CONTEXT);
    }

    /**
     * @param array<int, mixed> $arguments
     * @return AbstractLoggerAdapter
     */
    private function sut(array $arguments = []): AbstractLoggerAdapter
    {
        $adapter = $this->getMockForAbstractClass(
            AbstractLoggerAdapter::class,
            $arguments,
            '',
            true,
            true,
            true,
            ['getConfig']
        );
        $adapter->method('getConfig')->willReturn($this->config);

        return $adapter;
    }
}
