<?php

namespace Hippy\Connector\Tests\Functional\Connector\Mocks\Check;

use Hippy\Connector\Connector\Check\AbstractLoggerHandler;
use Hippy\Connector\Log\AbstractLoggerAdapter;

class LoggerHandler extends AbstractLoggerHandler
{
    /**
     * @param AbstractLoggerAdapter|null $adapter
     * @param string $requestMsg
     * @param string $responseMsg
     * @param string $cachedResponseMsg
     * @param string $exceptionMsg
     */
    public function __construct(
        ?AbstractLoggerAdapter $adapter = null,
        string $requestMsg = '__dummy_check_request_message__',
        string $responseMsg = '__dummy_check_response_message__',
        string $cachedResponseMsg = '__dummy_check_cached_request_message__',
        string $exceptionMsg = '__dummy_check_exception_message__'
    ) {
        $this->adapter = $adapter;
        $this->requestMsg = $requestMsg;
        $this->responseMsg = $responseMsg;
        $this->cachedResponseMsg = $cachedResponseMsg;
        $this->exceptionMsg = $exceptionMsg;
    }
}
