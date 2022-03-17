<?php

namespace Hippy\Connector\Tests\Unit\Config;

use Hippy\Connector\Config\Endpoint;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Connector\Config\Endpoint */
class EndpointTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct()
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
