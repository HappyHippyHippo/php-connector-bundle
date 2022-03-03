<?php

namespace Hippy\Connector\Model;

use Hippy\Error\ErrorCollection;
use Hippy\Model\Model;
use Psr\Http\Message\ResponseInterface;

class ResponseModel extends Model implements ResponseModelInterface
{
    /**
     * @param ResponseInterface|null $response
     * @param ErrorCollection|null $errors
     */
    public function __construct(
        protected ?ResponseInterface $response = null,
        protected ?ErrorCollection $errors = null,
    ) {
        parent::__construct();

        $this->addHideParser('response');
    }

    /**
     * @return ResponseInterface|null
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
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

    /**
     * @return ErrorCollection
     */
    public function getErrors(): ErrorCollection
    {
        return $this->errors ?? new ErrorCollection();
    }
}
