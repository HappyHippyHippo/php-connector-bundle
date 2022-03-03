<?php

namespace Hippy\Connector\Tests\Unit\Model\Config;

use Hippy\Connector\Model\Config\Endpoint;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Connector\Model\Config\Endpoint */
class EndpointTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct()
     * @covers ::getName()
     * @covers ::isCacheEnabled
     * @covers ::getCacheTTL
     */
    public function testConstructor(): void
    {
        $name = '__TEST_NAME__';
        $cacheTTL = 101;

        $config = new Endpoint($name, ['cache' => ['enabled' => true, 'ttl' => $cacheTTL]]);

        $this->assertEquals($name, $config->getName());
        $this->assertTrue($config->isCacheEnabled());
        $this->assertEquals($cacheTTL, $config->getCacheTTL());
    }
}
