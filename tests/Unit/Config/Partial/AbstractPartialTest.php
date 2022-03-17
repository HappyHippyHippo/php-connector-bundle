<?php

namespace Hippy\Connector\Tests\Unit\Config\Partial;

use Hippy\Connector\Config\Partial\AbstractPartial;
use Hippy\Connector\Config\Config;
use Hippy\Connector\Config\Endpoint;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Connector\Config\Partial\AbstractPartial */
class AbstractPartialTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $domain = '__dummy_domain__';
        $sut = $this->getMockForAbstractClass(AbstractPartial::class, [$domain]);
        $this->assertEquals(new Config($domain), $sut->get($domain . '.config'));
    }

    /**
     * @return void
     * @covers ::load
     */
    public function testLoadDoesNothingOnEmptyConfig(): void
    {
        $domain = '__dummy_domain__';
        $sut = $this->getMockForAbstractClass(AbstractPartial::class, [$domain]);

        $this->assertSame($sut, $sut->load([]));
        $this->assertEquals(new Config($domain), $sut->get($domain . '.config'));
    }

    /**
     * @return void
     * @covers ::load
     */
    public function testLoadDoesNothingOnNonDomainConfig(): void
    {
        $domain = '__dummy_domain__';
        $sut = $this->getMockForAbstractClass(AbstractPartial::class, [$domain]);

        $this->assertSame($sut, $sut->load([$domain => []]));
        $this->assertEquals(new Config($domain), $sut->get($domain . '.config'));
    }

    /**
     * @return void
     * @covers ::load
     */
    public function testLoadConfigAndLogLevels(): void
    {
        $domain = '__dummy_domain__';
        $config = [
            'client_config' => [
                'base_uri' => '__dummy_my_url__',
            ],
            'log_level_request' => 'error',
            'log_level_response' => 'error',
            'log_level_cached' => 'error',
            'log_level_exception' => 'fatal',
        ];
        $sut = $this->getMockForAbstractClass(AbstractPartial::class, [$domain]);

        $this->assertSame($sut, $sut->load([$domain . '.config' => $config]));

        $result = $sut->get($domain . '.config');
        $this->assertEquals([
            'base_uri' => $config['client_config']['base_uri'],
            'http_errors' => false,
            'allow_redirects' => false,
        ], $result->getClientConfig());
        $this->assertEquals($config['log_level_request'], $result->getLogLevelRequest());
        $this->assertEquals($config['log_level_response'], $result->getLogLevelResponse());
        $this->assertEquals($config['log_level_cached'], $result->getLogLevelCached());
        $this->assertEquals($config['log_level_exception'], $result->getLogLevelException());
    }

    /**
     * @return void
     * @covers ::load
     */
    public function testLoadEndpoints(): void
    {
        $domain = '__dummy_domain__';
        $config = [
            'endpoints' => [
                'endpoint1' => [],
                'endpoint2' => ['cached' => true],
                'endpoint3' => ['cached' => true, 'ttl' => 123],
            ]
        ];
        $sut = $this->getMockForAbstractClass(AbstractPartial::class, [$domain]);

        $this->assertSame($sut, $sut->load([$domain . '.config' => $config]));

        $result = $sut->get($domain . '.config');
        foreach ($config['endpoints'] as $name => $data) {
            $this->assertEquals(new Endpoint($name, $data), $result->getEndpoint($name));
        }
    }
}
