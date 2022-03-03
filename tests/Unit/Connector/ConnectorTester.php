<?php

namespace Hippy\Connector\Tests\Unit\Connector;

use Hippy\Connector\Connector\ConnectorInterface;
use Hippy\Connector\Exception\UnknownClientException;
use Hippy\Connector\Model\RequestModelInterface;
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
     * @param ConnectorInterface $connector
     * @param int $statusCode
     * @return void
     * @throws ReflectionException
     */
    protected function expectedStatusCodeTester(ConnectorInterface $connector, int $statusCode)
    {
        $method = new ReflectionMethod($connector, 'getExpectedStatusCode');
        $this->assertEquals($statusCode, $method->invoke($connector));
    }

    /**
     * @param ConnectorInterface $connector
     * @param RequestModelInterface $requestModel
     * @param int $statusCode
     * @return void
     * @throws ReflectionException
     */
    public function handleFailureOnUnexpectedStatusCodeTester(
        ConnectorInterface $connector,
        RequestModelInterface $requestModel,
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
     * @param ConnectorInterface $connector
     * @param RequestModelInterface $requestModel
     * @param int $statusCode
     * @return void
     * @throws ReflectionException
     */
    public function handleFailureOnPossibleStatusCodeTester(
        ConnectorInterface $connector,
        RequestModelInterface $requestModel,
        int $statusCode
    ): void {
        $message = '__test_message__';

        $method = new ReflectionMethod($connector, 'handleFailure');

        $this->assertNull($method->invoke($connector, $requestModel, $message, $statusCode));
    }

    /**
     * @param ConnectorInterface $connector
     * @return void
     * @throws ReflectionException
     */
    public function executeOnInvalidModel(ConnectorInterface $connector): void
    {
        $this->expectException(InvalidArgumentException::class);

        $requestModel = $this->createMock(RequestModelInterface::class);

        $method = new ReflectionMethod($connector, 'execute');
        $method->invoke($connector, $requestModel);
    }

    /**
     * @param ClientInterface&MockObject $client
     * @param ConnectorInterface $connector
     * @param RequestModelInterface $request
     * @param ResponseInterface $response
     * @param callable $checked
     * @return void
     * @throws ReflectionException
     */
    public function executeReturnClientResponse(
        ClientInterface&MockObject $client,
        ConnectorInterface $connector,
        RequestModelInterface $request,
        ResponseInterface $response,
        callable $checked
    ): void {
        $client->method('send')->with($this->callback($checked))->willReturn($response);

        $method = new ReflectionMethod($connector, 'execute');
        $this->assertEquals($response, $method->invoke($connector, $request));
    }
}
