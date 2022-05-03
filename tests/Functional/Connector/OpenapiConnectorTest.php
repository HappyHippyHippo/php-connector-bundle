<?php

namespace Hippy\Connector\Tests\Functional\Connector;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Hippy\Connector\Config\Endpoint;
use Hippy\Connector\Connector\Openapi\ResponseHandler;
use Hippy\Connector\Connector\Openapi\ResponseModel;
use Hippy\Connector\Exception\ConnectionException;
use Hippy\Connector\Exception\UnknownClientException;
use Hippy\Connector\Model\RequestModel;
use Hippy\Connector\Tests\Functional\Connector\Mocks\Openapi\Connector;
use Hippy\Connector\Tests\Functional\Connector\Mocks\Openapi\LoggerHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

/** @coversDefaultClass \Hippy\Connector\Tests\Functional\Connector\Mocks\Openapi\Connector */
class OpenapiConnectorTest extends TestCase
{
    /** @var ClientInterface&MockObject */
    private ClientInterface $client;

    /** @var Endpoint&MockObject */
    private Endpoint $config;

    /** @var ResponseHandler&MockObject */
    private ResponseHandler $transformer;

    /** @var LoggerHandler&MockObject */
    private LoggerHandler $logger;

    /** @var Connector */
    private Connector $connector;

    protected function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->config = $this->createMock(Endpoint::class);
        $this->transformer = $this->createMock(ResponseHandler::class);
        $this->logger = $this->createMock(LoggerHandler::class);

        $this->connector = new Connector($this->client, $this->config, $this->transformer, $this->logger);
    }

    /**
     * @return void
     * @covers ::execute
     */
    public function testValidResponse(): void
    {
        $request = new RequestModel([], ['header1' => '__dummy_header_value__']);

        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn(200);
        $this->client
            ->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Request $request) {
                return $request->getUri()->getPath() == '__openapi'
                    && $request->getMethod() == 'GET'
                    && $request->getHeaders() == ['header1' => ['__dummy_header_value__']];
            }))
            ->willReturn($response);

        $responseModel = $this->createMock(ResponseModel::class);
        $this->transformer->expects($this->once())->method('transform')->with($response)->willReturn($responseModel);

        $this->logger->expects($this->once())->method('writeRequest')->with($request);
        $this->logger->expects($this->once())->method('writeDryResponse')->with($request, $responseModel);

        $this->assertSame($responseModel, $this->connector->request($request));
    }

    /**
     * @return void
     * @covers ::execute
     */
    public function testConnectionError(): void
    {
        $statusCode = 500;
        $errorMessage = '__dummy_error_message__';
        $request = new RequestModel([], ['header1' => '__dummy_header_value__']);
        $exception = new ClientException($errorMessage, new Request('GET', '__check'), new Response($statusCode));
        $expected = new ConnectionException($request, $errorMessage, $statusCode);

        $this->client
            ->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Request $request) {
                return $request->getUri()->getPath() == '__openapi'
                    && $request->getMethod() == 'GET'
                    && $request->getHeaders() == ['header1' => ['__dummy_header_value__']];
            }))
            ->willThrowException($exception);

        $this->transformer->expects($this->never())->method('transform');

        $this->logger->expects($this->once())->method('writeRequest')->with($request);
        $this->logger->expects($this->once())->method('writeException')->with($request, $exception);

        $this->expectExceptionObject($expected);

        $this->connector->request($request);
    }

    /**
     * @return void
     * @covers ::execute
     */
    public function testUnhandledFailure(): void
    {
        $statusCode = 500;
        $errorMessage = '__dummy_error_message__';
        $request = new RequestModel([], ['header1' => '__dummy_header_value__']);
        $expected = new UnknownClientException($request, $errorMessage, $statusCode);

        $body = $this->createMock(StreamInterface::class);
        $body->expects($this->once())->method('__toString')->willReturn($errorMessage);
        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn($statusCode);
        $response->expects($this->once())->method('getBody')->willReturn($body);
        $this->client
            ->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Request $request) {
                return $request->getUri()->getPath() == '__openapi'
                    && $request->getMethod() == 'GET'
                    && $request->getHeaders() == ['header1' => ['__dummy_header_value__']];
            }))
            ->willReturn($response);

        $this->transformer->expects($this->never())->method('transform');

        $this->logger->expects($this->once())->method('writeRequest')->with($request);
        $this->logger->expects($this->once())->method('writeException')->with($request, $expected);

        $this->expectExceptionObject($expected);

        $this->connector->request($request);
    }
}
