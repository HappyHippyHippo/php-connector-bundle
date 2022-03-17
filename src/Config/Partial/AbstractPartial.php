<?php

namespace Hippy\Connector\Config\Partial;

use Hippy\Config\Partial\AbstractPartial as BaseAbstractPartial;
use Hippy\Connector\Config\Config;

abstract class AbstractPartial extends BaseAbstractPartial
{
    /**
     * @param string $domain
     */
    public function __construct(string $domain)
    {
        parent::__construct($domain);

        $this->config = [$domain . '.config' => new Config($domain)];
    }

    /**
     * @param array<string, mixed> $config
     * @return $this
     */
    public function load(array $config = []): self
    {
        $key = $this->domain . '.config';
        if (array_key_exists($key, $config)) {
            $this->config[$key]->load($config[$key]);
        }
        return $this;
    }
}
