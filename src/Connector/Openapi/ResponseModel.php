<?php

namespace Hippy\Connector\Connector\Openapi;

use Hippy\Connector\Model\ResponseModel as BaseResponseModel;
use Hippy\Error\ErrorCollection;
use Psr\Http\Message\ResponseInterface;

/**
 * @method string getData()
 */
class ResponseModel extends BaseResponseModel
{
    /**
     * @param ResponseInterface $response
     * @param ErrorCollection|null $errors
     * @param string $data
     */
    public function __construct(
        ResponseInterface $response,
        ?ErrorCollection $errors = null,
        protected string $data = '',
    ) {
        parent::__construct($response, $errors ?? new ErrorCollection());
    }
}
