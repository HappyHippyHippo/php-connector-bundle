<?php

namespace Hippy\Connector\Tests\Unit\Model\Config;

use Hippy\Connector\Model\Config\Config;
use Hippy\Connector\Model\Config\Endpoint;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

/** @coversDefaultClass \Hippy\Connector\Model\Config\Config */
class ConfigTest extends TestCase
{
    /**
     * @param array<string, mixed> $expected
     * @return void
     * @covers ::__construct
     * @covers ::getClientConfig
     * @covers ::getLogRequestLevel
     * @covers ::getLogResponseLevel
     * @covers ::getLogCachedResponseLevel
     * @covers ::getLogExceptionLevel
     * @dataProvider getProvider
     */
    public function testConstruct(array $expected): void
    {
        $domain = '__dummy_domain__';
        $sut = new Config($domain);

        $this->assertEquals($expected['client_config'], $sut->getClientConfig());
        $this->assertEquals($expected['log_level_request'], $sut->getLogRequestLevel());
        $this->assertEquals($expected['log_level_response'], $sut->getLogResponseLevel());
        $this->assertEquals($expected['log_level_cached'], $sut->getLogCachedResponseLevel());
        $this->assertEquals($expected['log_level_exception'], $sut->getLogExceptionLevel());
    }

    /**
     * @param string $domain
     * @param array<string, mixed> $config
     * @param array<string, mixed> $envs
     * @param array<string, mixed> $expected
     * @return void
     * @covers ::load
     * @covers ::getLogEnv
     * @covers ::getEnv
     * @dataProvider getProvider
     */
    public function testLoad(string $domain, array $config, array $envs, array $expected): void
    {
        foreach ($envs as $name => $env) {
            putenv($name . '=' . $env);
        }

        $sut = new Config($domain);
        $sut->load($config);

        foreach ($envs as $name => $env) {
            putenv($name . '=');
        }

        $this->assertEquals($expected['client_config'], $sut->getClientConfig());
        $this->assertEquals($expected['log_level_request'], $sut->getLogRequestLevel());
        $this->assertEquals($expected['log_level_response'], $sut->getLogResponseLevel());
        $this->assertEquals($expected['log_level_cached'], $sut->getLogCachedResponseLevel());
        $this->assertEquals($expected['log_level_exception'], $sut->getLogExceptionLevel());
    }

    /**
     * @return void
     * @covers ::load
     * @covers ::getEndpoint
     */
    public function testGetEndpoints(): void
    {
        $domain = '__dummy_domain__';
        $config = [
            'endpoints' => [
                'endpoint1' => [],
                'endpoint2' => ['cached' => true],
                'endpoint3' => ['cached' => true, 'ttl' => 123],
            ]
        ];
        $sut = new Config($domain);
        $sut->load($config);

        foreach ($config['endpoints'] as $name => $data) {
            $this->assertEquals(new Endpoint($name, $data), $sut->getEndpoint($name));
        }
        $this->assertEquals(new Endpoint('__dummy_other_endpoint__'), $sut->getEndpoint('__dummy_other_endpoint__'));
    }

    /**
     * @param string $provider
     * @return array<string, mixed>
     */
    public function getProvider(string $provider): array
    {
        $providers = Yaml::parseFile(sprintf('%s/%s.provider.yaml', dirname(__FILE__), basename(__FILE__, '.php')));
        if (!is_array($providers) || !isset($providers[$provider]) || !is_array($providers[$provider])) {
            $this->fail("invalid provider");
        }

        return $providers[$provider];
    }
}
