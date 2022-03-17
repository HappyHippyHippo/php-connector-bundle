<?php

namespace Hippy\Connector\Config;

use Hippy\Model\Model;

/**
 * @method string getName()
 * @method bool isCacheEnabled()
 * @method int getCacheTTL()
 */
class Endpoint extends Model
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
}
