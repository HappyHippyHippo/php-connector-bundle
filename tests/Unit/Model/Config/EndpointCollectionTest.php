<?php

namespace Hippy\Connector\Tests\Unit\Model\Config;

use Hippy\Connector\Model\Config\EndpointCollection;
use Hippy\Connector\Model\Config\EndpointInterface;
use Hippy\Model\ModelInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Connector\Model\Config\EndpointCollection */
class EndpointCollectionTest extends TestCase
{
    /**
     * @return void
     * @covers ::add
     */
    public function testAddThrowsOnInvalidEndpointConfigurationType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $endpoint = $this->createMock(ModelInterface::class);

        $sut = new EndpointCollection();
        $sut->add($endpoint);
    }

    /**
     * @return void
     * @covers ::add
     */
    public function testAddStoreTheEndpointConfiguration(): void
    {
        $endpoint = $this->createMock(EndpointInterface::class);

        $sut = new EndpointCollection();
        $this->assertSame($sut, $sut->add($endpoint));
        $this->assertEquals(1, count($sut));
        $this->assertSame($endpoint, $sut[0]);
    }
}
