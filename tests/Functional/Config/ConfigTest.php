<?php

namespace Hippy\Connector\Tests\Functional\Config;

use Hippy\Config\Config as Baseconfig;
use Hippy\Connector\Config\Config;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/** @coversDefaultClass \Hippy\Connector\Config\Partial\AbstractPartial */
class ConfigTest extends WebTestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testConnectorConfigInjection(): void
    {
        $value = '__dummy_value__';

        $container = static::getContainer();
        $config = $container->get(Baseconfig::class);
        if (!($config instanceof Baseconfig)) {
            $this->fail('unable to retrieve config object');
        }

        $this->assertNotNull($config->get('test_connector.config'));
        $this->assertInstanceOf(Config::class, $config->get('test_connector.config'));
    }
}
