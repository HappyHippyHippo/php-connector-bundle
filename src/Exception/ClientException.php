<?php

namespace Hippy\Connector\Exception;

use Hippy\Connector\Model\RequestModel;
use Hippy\Error\Error;
use Throwable;

abstract class ClientException extends Exception
{
    /**
     * @param RequestModel $request
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        protected RequestModel $request,
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->parseErrorMessage();
    }

    /**
     * @return RequestModel
     */
    public function getRequest(): RequestModel
    {
        return $this->request;
    }

    /**
     * @return void
     */
    protected function parseErrorMessage(): void
    {
        $response = json_decode($this->getMessage(), true);
        if (
            is_array($response)
            && array_key_exists('status', $response)
            && is_array($response['status'])
            && array_key_exists('errors', $response['status'])
            && is_array($response['status']['errors'])
        ) {
            foreach ($response['status']['errors'] as $error) {
                $this->addError(new Error($error['code'], $error['message']));
            }
        }
    }
}
