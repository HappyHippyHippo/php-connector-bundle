<?php

namespace Hippy\Connector\Model;

use Hippy\Error\ErrorCollection;
use Hippy\Model\Model;
use Psr\Http\Message\ResponseInterface;

/**
 * @method ResponseInterface|null getResponse()
 * @method ErrorCollection getErrors()
 */
class ResponseModel extends Model
{
    /**
     * @param ResponseInterface|null $response
     * @param ErrorCollection $errors
     */
    public function __construct(
        protected ?ResponseInterface $response = null,
        protected ErrorCollection $errors = new ErrorCollection(),
    ) {
        parent::__construct();

        $this->addHideParser('response');
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->response?->getStatusCode() ?? 0;
    }

    /**
     * @return array<string, mixed>
     */
    public function getHeaders(): array
    {
        return $this->response?->getHeaders() ?? [];
    }
}
