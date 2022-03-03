<?php

namespace Hippy\Connector\Tests\Unit\Cache;

use DateTime;
use Hippy\Connector\Cache\CacheAdapter;
use Hippy\Connector\Model\ResponseModelInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

/** @coversDefaultClass \Hippy\Connector\Cache\CacheAdapter */
class CacheAdapterTest extends TestCase
{
    /** @var CacheItemPoolInterface&MockObject */
    private CacheItemPoolInterface $cache;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->cache = $this->createMock(CacheItemPoolInterface::class);
    }

    /**
     * @return void
     * @covers ::__construct()
     * @covers ::getCache()
     */
    public function testCacheGetter(): void
    {
        $adapter = new CacheAdapter($this->cache);
        $this->assertEquals($this->cache, $adapter->getCache());
    }

    /**
     * @return void
     * @throws InvalidArgumentException
     * @covers ::loadResponse
     */
    public function testLoadResponseReturnTheResponseIfPresentInPool(): void
    {
        $key = '__dummy_request_key__';
        $response = $this->createMock(ResponseModelInterface::class);

        $item = $this->createMock(CacheItemInterface::class);
        $item->expects($this->once())->method('get')->willReturn($response);
        $this->cache->expects($this->once())->method('hasItem')->with($key)->willReturn(true);
        $this->cache->expects($this->once())->method('getItem')->with($key)->willReturn($item);

        $adapter = new CacheAdapter($this->cache);
        $this->assertEquals($response, $adapter->loadResponse($key));
    }

    /**
     * @return void
     * @throws InvalidArgumentException
     * @covers ::loadResponse
     */
    public function testLoadResponseReturnNullIfNoCache(): void
    {
        $key = '__dummy_request_key__';

        $adapter = new CacheAdapter();
        $this->assertNull($adapter->loadResponse($key));
    }

    /**
     * @return void
     * @throws InvalidArgumentException
     * @covers ::loadResponse
     */
    public function testLoadResponseReturnNullOnCacheMiss(): void
    {
        $key = '__dummy_request_key__';

        $this->cache->expects($this->once())->method('hasItem')->with($key)->willReturn(false);

        $adapter = new CacheAdapter($this->cache);
        $this->assertNull($adapter->loadResponse($key));
    }

    /**
     * @return void
     * @throws InvalidArgumentException
     * @covers ::storeResponse
     */
    public function testStoreResponseWithNoCache(): void
    {
        $key = '__dummy_request_key__';
        $response = $this->createMock(ResponseModelInterface::class);
        $ttl = new DateTime();

        $adapter = new CacheAdapter();
        $this->assertFalse($adapter->storeResponse($key, $response, $ttl));
    }

    /**
     * @return void
     * @throws InvalidArgumentException
     * @covers ::storeResponse
     */
    public function testStoreResponse(): void
    {
        $key = '__dummy_request_key__';
        $response = $this->createMock(ResponseModelInterface::class);
        $ttl = new DateTime();

        $item = $this->createMock(CacheItemInterface::class);
        $item->expects($this->once())->method('set')->with($response);
        $item->expects($this->once())->method('expiresAt')->with($ttl);

        $this->cache->expects($this->once())->method('getItem')->with($key)->willReturn($item);
        $this->cache->expects($this->once())->method('save')->with($item);

        $adapter = new CacheAdapter($this->cache);
        $this->assertTrue($adapter->storeResponse($key, $response, $ttl));
    }
}
