<?php

namespace Hippy\Connector\Connector\Openapi;

use Hippy\Connector\Connector\AbstractResponseHandler;
use Hippy\Error\ErrorCollection;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class ResponseHandler extends AbstractResponseHandler
{
    /**
     * @param ResponseInterface $response
     * @return ResponseModel
     */
    public function transform(ResponseInterface $response): ResponseModel
    {
        if ($response->getStatusCode() != Response::HTTP_OK) {
            return new ResponseModel($response, $this->getErrors($this->getContent($response)));
        }

        return new ResponseModel($response, new ErrorCollection(), $response->getBody());
    }
}
