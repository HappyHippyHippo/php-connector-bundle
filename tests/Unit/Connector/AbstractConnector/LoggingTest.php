<?php

namespace Hippy\Connector\Tests\Unit\Connector\AbstractConnector;

use Hippy\Connector\Exception\ConnectionException;
use Hippy\Connector\Model\ResponseModel;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Connector\Connector\AbstractConnector */
class LoggingTest extends TestBuilder
{
    /** @var int */
    private const SERVICE_CODE = 1;

    /** @var int */
    private const ENDPOINT_CODE = 2;

    /**
     * @return void
     * @covers ::request()
     * @covers ::logRequest()
     * @covers ::logResponse()
     */
    public function testLogOnSuccessRequest(): void
    {
        $responseModel = new ResponseModel($this->response);

        $this->response->expects($this->once())->method('getStatusCode')->willReturn(Response::HTTP_OK);

        $this->logger->expects($this->once())->method('writeRequest')->with($this->requestModel);
        $this->logger->expects($this->once())->method('writeDryResponse')->with($this->requestModel, $responseModel);

        $connector = $this->getConnector(
            [
                self::SERVICE_CODE,
                self::ENDPOINT_CODE,
                $this->client,
                $this->config,
                null,
                $this->logger
            ],
            function ($connector) {
                $connector
                    ->expects($this->once())
                    ->method('execute')
                    ->with($this->requestModel)
                    ->willReturn($this->response);
            }
        );

        $connector->request($this->requestModel);
    }

    /**
     * @return void
     * @covers ::request()
     * @covers ::logRequest()
     * @covers ::logResponse()
     */
    public function testLogOnFailureRequest(): void
    {
        $statusCode = Response::HTTP_GONE;
        $responseModel = new ResponseModel($this->response);

        $stream = $this->createMock(StreamInterface::class);

        $this->response->expects($this->once())->method('getStatusCode')->willReturn($statusCode);
        $this->response->expects($this->once())->method('getBody')->willReturn($stream);

        $this->logger->expects($this->once())->method('writeRequest')->with($this->requestModel);
        $this->logger->expects($this->once())->method('writeResponse')->with($this->requestModel, $responseModel);

        $connector = $this->getConnector(
            [
                self::SERVICE_CODE,
                self::ENDPOINT_CODE,
                $this->client,
                $this->config,
                null,
                $this->logger
            ],
            function ($connector) {
                $connector
                    ->expects($this->once())
                    ->method('execute')
                    ->with($this->requestModel)
                    ->willReturn($this->response);
            },
            ['handleFailure']
        );

        $connector->expects($this->once())->method('handleFailure')->with($this->requestModel, $stream, $statusCode);

        $connector->request($this->requestModel);
    }

    /**
     * @return void
     * @covers ::request()
     * @covers ::logException()
     */
    public function testRequestLogException(): void
    {
        $statusCode = Response::HTTP_NOT_FOUND;
        $message = '__test_message__';

        $response = $this->createMock(PsrResponseInterface::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn($statusCode);

        $exception = new RequestException($message, $this->createMock(RequestInterface::class), $response);
        $this->logger->expects($this->once())->method('writeException')->with($this->requestModel, $exception);

        $connector = $this->getConnector(
            [
                self::SERVICE_CODE,
                self::ENDPOINT_CODE,
                $this->client,
                $this->config,
                null,
                $this->logger
            ],
            function ($connector) use ($exception) {
                $connector
                    ->expects($this->once())
                    ->method('execute')
                    ->with($this->requestModel)
                    ->willThrowException($exception);
            }
        );

        $this->expectException(ConnectionException::class);
        $this->expectExceptionCode($statusCode);
        $this->expectExceptionMessage($message);

        $connector->request($this->requestModel);
    }

    /**
     * @return void
     * @covers ::request()
     * @covers ::getCachedResponse()
     * @covers ::logCachedResponse()
     */
    public function testRequestLogCachedResponse(): void
    {
        $responseModel = new ResponseModel($this->response);

        $this->config->expects($this->exactly(1))->method('isCacheEnabled')->willReturn(true);

        $this->cache
            ->expects($this->once())
            ->method('loadResponse')
            ->with($this->requestModel)
            ->WillReturn($responseModel);
        $this->cache->expects($this->never())->method('storeResponse');

        $this->logger->expects($this->once())->method('writeCachedResponse')->with($this->requestModel, $responseModel);

        $connector = $this->getConnector(
            [
                self::SERVICE_CODE,
                self::ENDPOINT_CODE,
                $this->client,
                $this->config,
                null,
                $this->logger,
                $this->cache
            ],
            function ($connector) {
                $connector->expects($this->never())->method('execute');
            }
        );

        $this->assertEquals($responseModel, $connector->request($this->requestModel));
    }
}
