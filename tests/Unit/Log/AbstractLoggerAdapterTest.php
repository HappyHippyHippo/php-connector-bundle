<?php

namespace Hippy\Connector\Tests\Unit\Log;

use Hippy\Config\ConfigInterface as BaseConfigInterface;
use Hippy\Connector\Log\AbstractLoggerAdapter;
use Hippy\Connector\Model\Config\ConfigInterface;
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

    /** @var BaseConfigInterface&MockObject */
    private BaseConfigInterface $configInterface;

    /** @var ConfigInterface&MockObject */
    private ConfigInterface $config;

    /** @var LoggerInterface&MockObject */
    private LoggerInterface $logger;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->configInterface = $this->createMock(BaseConfigInterface::class);
        $this->config = $this->createMock(ConfigInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    /**
     * @return void
     * @covers ::__construct()
     * @covers ::getLogger()
     */
    public function testLoggerGetter(): void
    {
        $adapter = $this->sut([$this->configInterface, $this->logger]);
        $this->assertEquals($this->logger, $adapter->getLogger());
    }

    /**
     * @return void
     * @covers ::logRequest
     */
    public function testLogRequestWithNoLogger(): void
    {
        $this->config->expects($this->never())->method('getLogRequestLevel');
        $this->config->expects($this->never())->method('getLogResponseLevel');
        $this->config->expects($this->never())->method('getLogCachedResponseLevel');
        $this->config->expects($this->never())->method('getLogExceptionLevel');

        $adapter = $this->sut([$this->configInterface]);
        $adapter->logRequest(self::MESSAGE, self::CONTEXT);
    }

    /**
     * @return void
     * @covers ::logRequest
     */
    public function testLogRequest(): void
    {
        $level = 'info';
        $this->config->expects($this->once())->method('getLogRequestLevel')->willReturn($level);

        $this->logger
            ->expects($this->once())
            ->method($level)
            ->with(self::MESSAGE, self::CONTEXT);

        $adapter = $this->sut([$this->configInterface, $this->logger]);
        $adapter->logRequest(self::MESSAGE, self::CONTEXT);
    }

    /**
     * @return void
     * @covers ::logResponse
     */
    public function testLogResponseWithNoLogger(): void
    {
        $this->config->expects($this->never())->method('getLogRequestLevel');
        $this->config->expects($this->never())->method('getLogResponseLevel');
        $this->config->expects($this->never())->method('getLogCachedResponseLevel');
        $this->config->expects($this->never())->method('getLogExceptionLevel');

        $adapter = $this->sut([$this->configInterface]);
        $adapter->logResponse(self::MESSAGE, self::CONTEXT);
    }

    /**
     * @return void
     * @covers ::logResponse
     */
    public function testLogResponse(): void
    {
        $level = 'info';
        $this->config->expects($this->once())->method('getLogResponseLevel')->willReturn($level);

        $this->logger
            ->expects($this->once())
            ->method($level)
            ->with(self::MESSAGE, self::CONTEXT);

        $adapter = $this->sut([$this->configInterface, $this->logger]);
        $adapter->logResponse(self::MESSAGE, self::CONTEXT);
    }

    /**
     * @return void
     * @covers ::logCachedResponse
     */
    public function testLogCachedResponseWithNoLogger(): void
    {
        $this->config->expects($this->never())->method('getLogRequestLevel');
        $this->config->expects($this->never())->method('getLogResponseLevel');
        $this->config->expects($this->never())->method('getLogCachedResponseLevel');
        $this->config->expects($this->never())->method('getLogExceptionLevel');

        $adapter = $this->sut([$this->configInterface]);
        $adapter->logCachedResponse(self::MESSAGE, self::CONTEXT);
    }

    /**
     * @return void
     * @covers ::logCachedResponse
     */
    public function testLogCachedResponse(): void
    {
        $level = 'info';
        $this->config->expects($this->once())->method('getLogCachedResponseLevel')->willReturn($level);

        $this->logger
            ->expects($this->once())
            ->method($level)
            ->with(self::MESSAGE, self::CONTEXT);

        $adapter = $this->sut([$this->configInterface, $this->logger]);
        $adapter->logCachedResponse(self::MESSAGE, self::CONTEXT);
    }

    /**
     * @return void
     * @covers ::logException
     */
    public function testLogExceptionWithNoLogger(): void
    {
        $this->config->expects($this->never())->method('getLogRequestLevel');
        $this->config->expects($this->never())->method('getLogResponseLevel');
        $this->config->expects($this->never())->method('getLogCachedResponseLevel');
        $this->config->expects($this->never())->method('getLogExceptionLevel');

        $adapter = $this->sut([$this->configInterface]);
        $adapter->logException(self::MESSAGE, self::CONTEXT);
    }

    /**
     * @return void
     * @covers ::logException
     */
    public function testLogException(): void
    {
        $level = 'info';
        $this->config->expects($this->once())->method('getLogExceptionLevel')->willReturn($level);

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
