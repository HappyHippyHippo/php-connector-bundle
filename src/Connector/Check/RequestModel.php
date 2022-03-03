<?php

namespace Hippy\Connector\Connector\Check;

use Hippy\Connector\Model\RequestModel as BaseRequestModel;

class RequestModel extends BaseRequestModel
{
    /**
     * @param bool $deep
     * @param array<string, string|string[]> $headers
     */
    public function __construct(protected bool $deep, array $headers = [])
    {
        parent::__construct([], $headers);
    }

    /**
     * @return bool|null
     */
    public function isDeep(): ?bool
    {
        return $this->deep ?? false;
    }
}
