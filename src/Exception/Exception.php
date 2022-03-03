<?php

namespace Hippy\Connector\Exception;

use Hippy\Exception\Exception as BaseException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

abstract class Exception extends BaseException
{
    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct(Response::HTTP_SERVICE_UNAVAILABLE, $message, $previous, [], $code);
    }
}
