<?php

namespace Hippy\Connector\Model\Config;

use Hippy\Model\Model;

class Config extends Model implements ConfigInterface
{
    /** @var array<string, mixed> */
    protected const CLIENT_CONFIG_DEFAULT = [
        'base_uri' => '',
        'http_errors' => false,
        'allow_redirects' => false,
    ];

    /** @var array<string, mixed> */
    protected array $config;

    /** @var string */
    protected string $logLevelRequest;

    /** @var string */
    protected string $logLevelResponse;

    /** @var string */
    protected string $logLevelCached;

    /** @var string */
    protected string $logLevelException;

    /** @var EndpointCollection */
    protected EndpointCollection $endpoints;

    /**
     * @param string $domain
     */
    public function __construct(protected string $domain)
    {
        parent::__construct([
            'config' => self::CLIENT_CONFIG_DEFAULT,
            'logLevelRequest' => 'info',
            'logLevelResponse' => 'info',
            'logLevelCached' => 'info',
            'logLevelException' => 'error',
            'endpoints' => new EndpointCollection()
        ]);
    }

    /**
     * @param array<string, mixed> $config
     * @return void
     */
    public function load(array $config = [])
    {
        $this->config = array_merge($this->config, $config['client_config'] ?? []);
        $this->config['base_uri'] = $this->getEnv('BASE_URL', $this->config['base_uri']);

        $this->logLevelRequest = $this->getLogEnv('request', $config);
        $this->logLevelResponse = $this->getLogEnv('response', $config);
        $this->logLevelCached = $this->getLogEnv('cached', $config);
        $this->logLevelException = $this->getLogEnv('exception', $config);

        foreach ($config['endpoints'] ?? [] as $name => $endpoint) {
            $this->endpoints->add(new Endpoint($name, $endpoint));
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getClientConfig(): array
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getLogRequestLevel(): string
    {
        return $this->logLevelRequest;
    }

    /**
     * @return string
     */
    public function getLogResponseLevel(): string
    {
        return $this->logLevelResponse;
    }

    /**
     * @return string
     */
    public function getLogCachedResponseLevel(): string
    {
        return $this->logLevelCached;
    }

    /**
     * @return string
     */
    public function getLogExceptionLevel(): string
    {
        return $this->logLevelException;
    }

    /**
     * @param string $name
     * @return EndpointInterface
     */
    public function getEndpoint(string $name): EndpointInterface
    {
        // @phpstan-ignore-next-line
        return $this->endpoints->find(function (EndpointInterface $item) use ($name) {
            return $item->getName() == $name;
        }) ?? new Endpoint($name);
    }

    /**
     * @param string $name
     * @param array<string, mixed> $config
     * @return string
     */
    private function getLogEnv(string $name, array $config): string
    {
        $field = 'logLevel' . ucfirst($name);
        $envName = 'LOG_LEVEL_' . strtoupper($name);

        return $this->getEnv($envName, $config['log_level_' . $name] ?? $this->$field);
    }

    /**
     * @param string $name
     * @param string $default
     * @return string
     */
    private function getEnv(string $name, string $default): string
    {
        $env = sprintf('DREAMSHAPER_%s_%s', strtoupper($this->domain), strtoupper($name));
        $value = getenv($env);
        return $value ?: $default;
    }
}
