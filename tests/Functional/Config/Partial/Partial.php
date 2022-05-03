<?php

namespace Hippy\Connector\Tests\Functional\Config\Partial;

use Hippy\Connector\Config\Partial\AbstractPartial;

class Partial extends AbstractPartial
{
    /** @var string */
    public const DOMAIN = 'test_connector';

    public function __construct()
    {
        parent::__construct(self::DOMAIN);
    }
}
