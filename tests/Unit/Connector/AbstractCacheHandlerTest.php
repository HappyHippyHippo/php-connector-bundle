<?php

namespace Hippy\Connector\Tests\Unit\Connector;

use Hippy\Connector\Cache\CacheAdapter;
use Hippy\Connector\Connector\AbstractCacheHandler;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/** @coversDefaultClass \Hippy\Connector\Connector\AbstractCacheHandler */
class AbstractCacheHandlerTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $adapter = $this->createMock(CacheAdapter::class);
        $sut = $this->getMockBuilder(AbstractCacheHandler::class)
            ->setConstructorArgs([$adapter])
            ->getMock();

        $prop = new ReflectionProperty(AbstractCacheHandler::class, 'adapter');
        $this->assertSame($adapter, $prop->getValue($sut));
    }
}
