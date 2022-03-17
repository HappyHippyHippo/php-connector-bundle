<?php

namespace Hippy\Connector\Connector\Check;

use Hippy\Connector\Model\RequestModel as BaseRequestModel;

/**
 * @method bool isDeep()
 */
class RequestModel extends BaseRequestModel
{
    /**
     * @param bool $deep
     * @param array<string, string|string[]> $headers
     */
    public function __construct(protected bool $deep = false, array $headers = [])
    {
        parent::__construct([], $headers);
    }
}
