<?php

namespace Hippy\Connector\Tests\Unit\Config;

use Hippy\Connector\Config\EndpointCollection;
use Hippy\Connector\Config\Endpoint;
use Hippy\Model\Model;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Connector\Config\EndpointCollection */
class EndpointCollectionTest extends TestCase
{
    /**
     * @return void
     * @covers ::add
     */
    public function testAddThrowsOnInvalidEndpointConfigurationType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $endpoint = $this->createMock(Model::class);

        $sut = new EndpointCollection();
        $sut->add($endpoint);
    }

    /**
     * @return void
     * @covers ::add
     */
    public function testAddStoreTheEndpointConfiguration(): void
    {
        $endpoint = $this->createMock(Endpoint::class);

        $sut = new EndpointCollection();
        $this->assertSame($sut, $sut->add($endpoint));
        $this->assertEquals(1, count($sut));
        $this->assertSame($endpoint, $sut[0]);
    }
}
