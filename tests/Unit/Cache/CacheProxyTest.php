<?php

namespace Hippy\Connector\Tests\Unit\Cache;

use Hippy\Connector\Cache\CacheProxy;
use Hippy\Connector\Cache\CacheStrategyInterface;
use Hippy\Connector\Cache\DatabaseCacheStrategyInterface;
use Hippy\Repository\RepositoryFactoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use ReflectionProperty;
use RuntimeException;

/** @coversDefaultClass \Hippy\Connector\Cache\CacheProxy */
class CacheProxyTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $strategy = $this->createMock(CacheStrategyInterface::class);
        $arguments = ["string", $strategy, 123];
        $expected = [$strategy];

        $sut = new CacheProxy($arguments); // @phpstan-ignore-line

        $prop = new ReflectionProperty(CacheProxy::class, 'strategies');
        $this->assertEquals($expected, $prop->getValue($sut));
    }

    /**
     * @return void
     * @covers ::setRepositoryFactory
     */
    public function testSetRepositoryFactory(): void
    {
        $factory = $this->createMock(RepositoryFactoryInterface::class);

        $strategy1 = $this->createMock(CacheStrategyInterface::class);
        $strategy2 = $this->createMock(DatabaseCacheStrategyInterface::class);
        $strategy2->expects($this->once())->method('setRepositoryFactory')->with($factory);
        $sut = new CacheProxy([$strategy1, $strategy2]);

        $this->assertSame($sut, $sut->setRepositoryFactory($factory));
    }

    /**
     * @return void
     * @covers ::getItem
     */
    public function testGetItemThrowsIfNoStrategySupports(): void
    {
        $key = '__dummy_key__';

        $strategy = $this->createMock(CacheStrategyInterface::class);
        $strategy->expects($this->once())->method('supports')->with($key)->willReturn(false);
        $sut = new CacheProxy([$strategy]);

        $this->expectExceptionObject(new RuntimeException("No cache strategy to handle '" . $key . "' item"));

        $sut->getItem($key);
    }

    /**
     * @return void
     * @covers ::getItem
     */
    public function testGetItem(): void
    {
        $key = '__dummy_key__';
        $item = $this->createMock(CacheItemInterface::class);

        $strategy = $this->createMock(CacheStrategyInterface::class);
        $strategy->expects($this->once())->method('supports')->with($key)->willReturn(true);
        $strategy->expects($this->once())->method('getItem')->with($key)->willReturn($item);
        $sut = new CacheProxy([$strategy]);

        $this->assertSame($item, $sut->getItem($key));
    }

    /**
     * @return void
     * @covers ::getItems
     */
    public function testGetItemsThrows(): void
    {
        $this->expectExceptionObject(new RuntimeException("Not implemented"));

        $sut = new CacheProxy([]);
        $sut->getItems([]);
    }

    /**
     * @return void
     * @covers ::hasItem
     */
    public function testHasItemThrowsIfNoStrategySupports(): void
    {
        $key = '__dummy_key__';

        $strategy = $this->createMock(CacheStrategyInterface::class);
        $strategy->expects($this->once())->method('supports')->with($key)->willReturn(false);
        $sut = new CacheProxy([$strategy]);

        $this->expectExceptionObject(new RuntimeException("No cache strategy to handle '" . $key . "' item"));

        $sut->hasItem($key);
    }

    /**
     * @return void
     * @covers ::hasItem
     */
    public function testHasItem(): void
    {
        $key = '__dummy_key__';

        $strategy = $this->createMock(CacheStrategyInterface::class);
        $strategy->expects($this->once())->method('supports')->with($key)->willReturn(true);
        $strategy->expects($this->once())->method('hasItem')->with($key)->willReturn(true);
        $sut = new CacheProxy([$strategy]);

        $this->assertTrue($sut->hasItem($key));
    }

    /**
     * @return void
     * @covers ::clear
     */
    public function testClearThrows(): void
    {
        $this->expectExceptionObject(new RuntimeException("Not implemented"));

        $sut = new CacheProxy([]);
        $sut->clear();
    }

    /**
     * @return void
     * @covers ::deleteItem
     */
    public function testDeleteItemThrows(): void
    {
        $this->expectExceptionObject(new RuntimeException("Not implemented"));

        $sut = new CacheProxy([]);
        $sut->deleteItem('__dummy_key__');
    }

    /**
     * @return void
     * @covers ::deleteItems
     */
    public function testDeleteItemsThrows(): void
    {
        $this->expectExceptionObject(new RuntimeException("Not implemented"));

        $sut = new CacheProxy([]);
        $sut->deleteItems(['__dummy_key__']);
    }

    /**
     * @return void
     * @covers ::save
     */
    public function testSaveThrowsIfNoStrategySupports(): void
    {
        $key = '__dummy_key__';
        $item = $this->createMock(CacheItemInterface::class);
        $item->expects($this->once())->method('getKey')->willReturn($key);

        $strategy = $this->createMock(CacheStrategyInterface::class);
        $strategy->expects($this->once())->method('supports')->with($key)->willReturn(false);
        $sut = new CacheProxy([$strategy]);

        $this->expectExceptionObject(new RuntimeException("No cache strategy to handle '" . $key . "' item"));

        $sut->save($item);
    }

    /**
     * @return void
     * @covers ::save
     */
    public function testSave(): void
    {
        $key = '__dummy_key__';
        $item = $this->createMock(CacheItemInterface::class);
        $item->expects($this->once())->method('getKey')->willReturn($key);

        $strategy = $this->createMock(CacheStrategyInterface::class);
        $strategy->expects($this->once())->method('supports')->with($key)->willReturn(true);
        $strategy->expects($this->once())->method('save')->with($item)->willReturn(true);
        $sut = new CacheProxy([$strategy]);

        $this->assertTrue($sut->save($item));
    }

    /**
     * @return void
     * @covers ::saveDeferred
     */
    public function testSaveDeferredThrows(): void
    {
        $item = $this->createMock(CacheItemInterface::class);

        $this->expectExceptionObject(new RuntimeException("Not implemented"));

        $sut = new CacheProxy([]);
        $sut->saveDeferred($item);
    }

    /**
     * @return void
     * @covers ::commit
     */
    public function testCommitThrows(): void
    {
        $this->expectExceptionObject(new RuntimeException("Not implemented"));

        $sut = new CacheProxy([]);
        $sut->commit();
    }
}
