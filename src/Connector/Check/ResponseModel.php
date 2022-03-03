<?php

namespace Hippy\Connector\Connector\Check;

use Hippy\Connector\Model\ResponseModel as BaseResponseModel;
use Hippy\Error\ErrorCollection;
use Psr\Http\Message\ResponseInterface;

class ResponseModel extends BaseResponseModel
{
    /**
     * @param ResponseInterface $response
     * @param ErrorCollection|null $errors
     * @param array<int|string, mixed> $data
     */
    public function __construct(
        ResponseInterface $response,
        ?ErrorCollection $errors = null,
        protected array $data = [],
    ) {
        parent::__construct($response, $errors);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }
}
