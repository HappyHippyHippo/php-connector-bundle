<?php

namespace Hippy\Connector\Model\Config;

use Hippy\Model\Model;

class Endpoint extends Model implements EndpointInterface
{
    /** @var bool */
    protected bool $cacheEnabled;

    /** @var int */
    protected int $cacheTTL;

    /**
     * @param string $name
     * @param array<string, mixed>|null $config
     */
    public function __construct(
        protected string $name,
        ?array $config = []
    ) {
        parent::__construct([
            'cacheEnabled' => (($config['cache'] ?? [])['enabled'] ?? 'false') === true,
            'cacheTTL' => ($config['cache'] ?? [])['ttl'] ?? 0
        ]);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isCacheEnabled(): bool
    {
        return $this->cacheEnabled;
    }

    /**
     * @return int
     */
    public function getCacheTTL(): int
    {
        return $this->cacheTTL;
    }
}
