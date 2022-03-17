<?php

namespace Hippy\Connector\Connector\Index;

use Hippy\Connector\Connector\AbstractResponseHandler;
use Psr\Http\Message\ResponseInterface;

class ResponseHandler extends AbstractResponseHandler
{
    /**
     * @param ResponseInterface $response
     * @return ResponseModel
     */
    public function transform(ResponseInterface $response): ResponseModel
    {
        $content = $this->getContent($response);
        $status = $this->getStatus($content);
        $errors = $this->getErrors($content);

        return new ResponseModel($response, $errors, $status ? $content['data'] : []);
    }
}
