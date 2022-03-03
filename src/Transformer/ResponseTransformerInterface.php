<?php

namespace Hippy\Connector\Transformer;

use Hippy\Connector\Exception\InvalidResponseContentException;
use Hippy\Connector\Model\ResponseModelInterface;
use Psr\Http\Message\ResponseInterface;

interface ResponseTransformerInterface
{
    /**
     * @param ResponseInterface $response
     * @return ResponseModelInterface
     * @throws InvalidResponseContentException
     */
    public function transform(ResponseInterface $response): ResponseModelInterface;
}
