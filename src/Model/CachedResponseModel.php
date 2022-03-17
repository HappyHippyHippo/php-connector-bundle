<?php

namespace Hippy\Connector\Model;

use Hippy\Model\Model;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method Model|null getData()
 */
class CachedResponseModel extends ResponseModel
{
    /**
     * @param int $statusCode
     * @param Model|null $data
     */
    public function __construct(
        protected int $statusCode = Response::HTTP_OK,
        protected ?Model $data = null,
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
}
