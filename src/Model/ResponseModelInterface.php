<?php

namespace Hippy\Connector\Model;

use Hippy\Error\ErrorCollection;
use Hippy\Model\ModelInterface;
use Psr\Http\Message\ResponseInterface;

interface ResponseModelInterface extends ModelInterface
{
    /**
     * @return ResponseInterface|null
     */
    public function getResponse(): ?ResponseInterface;

    /**
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * @return array<string, mixed>
     */
    public function getHeaders(): array;

    /**
     * @return ErrorCollection
     */
    public function getErrors(): ErrorCollection;
}
