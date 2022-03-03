<?php

namespace Hippy\Connector\Connector\Openapi;

use Hippy\Connector\Model\ResponseModelInterface;
use Hippy\Connector\Transformer\AbstractResponseTransformer;
use Hippy\Error\ErrorCollection;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class ResponseTransformer extends AbstractResponseTransformer
{
    /**
     * @param ResponseInterface $response
     * @return ResponseModelInterface
     */
    public function transform(ResponseInterface $response): ResponseModelInterface
    {
        if ($response->getStatusCode() != Response::HTTP_OK) {
            return new ResponseModel($response, $this->getErrors($this->getContent($response)));
        }

        return new ResponseModel($response, new ErrorCollection(), $response->getBody());
    }
}
