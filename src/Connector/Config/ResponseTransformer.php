<?php

namespace Hippy\Connector\Connector\Config;

use Hippy\Connector\Model\ResponseModelInterface;
use Hippy\Connector\Transformer\AbstractResponseTransformer;
use Psr\Http\Message\ResponseInterface;

class ResponseTransformer extends AbstractResponseTransformer
{
    /**
     * @param ResponseInterface $response
     * @return ResponseModelInterface
     */
    public function transform(ResponseInterface $response): ResponseModelInterface
    {
        $content = $this->getContent($response);
        $status = $this->getStatus($content);
        $errors = $this->getErrors($content);

        return new ResponseModel($response, $errors, $status ? $content['data'] : []);
    }
}
