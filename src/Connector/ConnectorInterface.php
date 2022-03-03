<?php

namespace Hippy\Connector\Connector;

use Hippy\Connector\Exception\ConnectionException;
use Hippy\Connector\Model\RequestModelInterface;
use Hippy\Connector\Model\ResponseModelInterface;

interface ConnectorInterface
{
    /**
     * @return int
     */
    public function getServiceCode(): int;

    /**
     * @return int
     */
    public function getEndpointCode(): int;

    /**
     * @return int
     */
    public function getExpectedStatusCode(): int;

    /**
     * @param RequestModelInterface $request
     * @return ResponseModelInterface
     * @throws ConnectionException
     */
    public function request(RequestModelInterface $request): ResponseModelInterface;
}
