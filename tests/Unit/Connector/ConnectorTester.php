<?php

namespace Hippy\Connector\Tests\Unit\Connector;

use Hippy\Connector\Connector\AbstractConnector;
use Hippy\Connector\Exception\UnknownClientException;
use Hippy\Connector\Model\RequestModel;
use GuzzleHttp\ClientInterface;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\Response;

class ConnectorTester extends TestCase
{
    /**
     * @param AbstractConnector $connector
     * @param int $statusCode
     * @return void
     * @throws ReflectionException
     */
    protected function expectedStatusCodeTester(AbstractConnector $connector, int $statusCode)
    {
        $method = new ReflectionMethod($connector, 'getExpectedStatusCode');
        $this->assertEquals($statusCode, $method->invoke($connector));
    }

    /**
     * @param AbstractConnector $connector
     * @param RequestModel $requestModel
     * @param int $statusCode
     * @return void
     * @throws ReflectionException
     */
    public function handleFailureOnUnexpectedStatusCodeTester(
        AbstractConnector $connector,
        RequestModel $requestModel,
        int $statusCode
    ): void {
        $message = '__test_message__';

        $method = new ReflectionMethod($connector, 'handleFailure');

        $this->expectException(UnknownClientException::class);
        $this->expectExceptionCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->expectExceptionMessage($message);

        $method->invoke($connector, $requestModel, $message, $statusCode);
    }

    /**
     * @param AbstractConnector $connector
     * @param RequestModel $requestModel
     * @param int $statusCode
     * @return void
     * @throws ReflectionException
     */
    public function handleFailureOnPossibleStatusCodeTester(
        AbstractConnector $connector,
        RequestModel $requestModel,
        int $statusCode
    ): void {
        $message = '__test_message__';

        $method = new ReflectionMethod($connector, 'handleFailure');

        $this->assertNull($method->invoke($connector, $requestModel, $message, $statusCode));
    }

    /**
     * @param AbstractConnector $connector
     * @return void
     * @throws ReflectionException
     */
    public function executeOnInvalidModel(AbstractConnector $connector): void
    {
        $this->expectException(InvalidArgumentException::class);

        $requestModel = $this->createMock(RequestModel::class);

        $method = new ReflectionMethod($connector, 'execute');
        $method->invoke($connector, $requestModel);
    }

    /**
     * @param ClientInterface&MockObject $client
     * @param AbstractConnector $connector
     * @param RequestModel $request
     * @param ResponseInterface $response
     * @param callable $checked
     * @return void
     * @throws ReflectionException
     */
    public function executeReturnClientResponse(
        ClientInterface&MockObject $client,
        AbstractConnector $connector,
        RequestModel $request,
        ResponseInterface $response,
        callable $checked
    ): void {
        $client->method('send')->with($this->callback($checked))->willReturn($response);

        $method = new ReflectionMethod($connector, 'execute');
        $this->assertEquals($response, $method->invoke($connector, $request));
    }
}
