<?php

namespace Hippy\Connector\Tests\Unit\Config;

use Hippy\Connector\Config\EndpointCollection;
use Hippy\Connector\Config\Endpoint;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/** @coversDefaultClass \Hippy\Connector\Config\EndpointCollection */
class EndpointCollectionTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructorWithoutArguments(): void
    {
        $sut = new EndpointCollection();

        $prop = new ReflectionProperty(EndpointCollection::class, 'type');
        $this->assertEquals(Endpoint::class, $prop->getValue($sut));
        $this->assertEquals([], $sut->getItems());
    }

    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructorWithArguments(): void
    {
        $endpoint1 = $this->createMock(Endpoint::class);
        $endpoint2 = $this->createMock(Endpoint::class);

        $sut = new EndpointCollection([$endpoint1, $endpoint2]);

        $prop = new ReflectionProperty(EndpointCollection::class, 'type');
        $this->assertEquals(Endpoint::class, $prop->getValue($sut));
        $this->assertEquals([$endpoint1, $endpoint2], $sut->getItems());
    }
}
