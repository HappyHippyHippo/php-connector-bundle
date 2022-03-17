<?php

namespace Hippy\Connector\Tests\Unit\Cache;

use DateTime;
use Hippy\Connector\Cache\CacheAdapter;
use Hippy\Connector\Cache\CacheProxy;
use Hippy\Connector\Model\ResponseModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;

/** @coversDefaultClass \Hippy\Connector\Cache\CacheAdapter */
class CacheAdapterTest extends TestCase
{
    /** @var CacheProxy&MockObject */
    private CacheProxy $proxy;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->proxy = $this->createMock(CacheProxy::class);
    }

    /**
     * @return void
     * @throws InvalidArgumentException
     * @covers ::__construct
     * @covers ::loadResponse
     */
    public function testLoadResponseReturnTheResponseIfPresentInPool(): void
    {
        $key = '__dummy_request_key__';
        $response = $this->createMock(ResponseModel::class);

        $item = $this->createMock(CacheItemInterface::class);
        $item->expects($this->once())->method('get')->willReturn($response);
        $this->proxy->expects($this->once())->method('hasItem')->with($key)->willReturn(true);
        $this->proxy->expects($this->once())->method('getItem')->with($key)->willReturn($item);

        $adapter = new CacheAdapter($this->proxy);
        $this->assertEquals($response, $adapter->loadResponse($key));
    }

    /**
     * @return void
     * @throws InvalidArgumentException
     * @covers ::__construct
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
     * @covers ::__construct
     * @covers ::loadResponse
     */
    public function testLoadResponseReturnNullOnCacheMiss(): void
    {
        $key = '__dummy_request_key__';

        $this->proxy->expects($this->once())->method('hasItem')->with($key)->willReturn(false);

        $adapter = new CacheAdapter($this->proxy);
        $this->assertNull($adapter->loadResponse($key));
    }

    /**
     * @return void
     * @throws InvalidArgumentException
     * @covers ::__construct
     * @covers ::storeResponse
     */
    public function testStoreResponseWithNoCache(): void
    {
        $key = '__dummy_request_key__';
        $response = $this->createMock(ResponseModel::class);
        $ttl = new DateTime();

        $adapter = new CacheAdapter();
        $this->assertFalse($adapter->storeResponse($key, $response, $ttl));
    }

    /**
     * @return void
     * @throws InvalidArgumentException
     * @covers ::__construct
     * @covers ::storeResponse
     */
    public function testStoreResponse(): void
    {
        $key = '__dummy_request_key__';
        $response = $this->createMock(ResponseModel::class);
        $ttl = new DateTime();

        $item = $this->createMock(CacheItemInterface::class);
        $item->expects($this->once())->method('set')->with($response);
        $item->expects($this->once())->method('expiresAt')->with($ttl);

        $this->proxy->expects($this->once())->method('getItem')->with($key)->willReturn($item);
        $this->proxy->expects($this->once())->method('save')->with($item);

        $adapter = new CacheAdapter($this->proxy);
        $this->assertTrue($adapter->storeResponse($key, $response, $ttl));
    }
}
