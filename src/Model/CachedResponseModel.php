<?php

namespace Hippy\Connector\Model;

use Hippy\Error\ErrorCollection;
use Hippy\Model\Model;
use Hippy\Model\ModelInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class CachedResponseModel extends ResponseModel implements ResponseModelInterface
{
    public function __construct(
        protected int $statusCode = Response::HTTP_OK,
        protected ?ModelInterface $data = null,
    ) {
        parent::__construct();

        $this->addHideParser('response');
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return ModelInterface|null
     */
    public function getData(): ?ModelInterface
    {
        return $this->data;
    }
}
