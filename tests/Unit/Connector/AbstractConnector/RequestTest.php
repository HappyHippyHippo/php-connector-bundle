<?php

namespace Hippy\Connector\Tests\Unit\Connector\AbstractConnector;

use Hippy\Connector\Connector\AbstractConnector;
use Hippy\Connector\Exception\ConnectionException;
use Hippy\Connector\Exception\UnknownClientException;
use Hippy\Connector\Model\ResponseModel;
use Hippy\Connector\Model\ResponseModelInterface;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use ReflectionException;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Connector\Connector\AbstractConnector */
class RequestTest extends TestBuilder
{
    /** @var int */
    private const SERVICE_CODE = 1;

    /** @var int */
    private const ENDPOINT_CODE = 2;

    /**
     * @return void
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $connector = $this->getConnector(
            [
                self::SERVICE_CODE,
                self::ENDPOINT_CODE,
                $this->client,
                $this->config,
                $this->transformer,
                $this->logger,
                $this->cache
            ]
        );

        $check = function (AbstractConnector $instance, $check, string $property) {
            $prop = new ReflectionProperty(AbstractConnector::class, $property);
            $this->assertEquals($check, $prop->getValue($instance));
        };

        $check($connector, $this->client, 'client');
        $check($connector, $this->config, 'config');
        $check($connector, $this->transformer, 'transformer');
        $check($connector, $this->logger, 'logger');
        $check($connector, $this->cache, 'cache');
    }

    /**
     * @return void
     * @covers ::request
     */
    public function testRequestRunTransformedIfTransformerIdPresent(): void
    {
        $responseModel = $this->createMock(ResponseModelInterface::class);
        $this->transformer
            ->expects($this->once())
            ->method('transform')
            ->with($this->response)
            ->willReturn($responseModel);

        $this->response->expects($this->once())->method('getStatusCode')->willReturn(Response::HTTP_OK);

        $connector = $this->getConnector(
            [
                self::SERVICE_CODE,
                self::ENDPOINT_CODE,
                $this->client,
                $this->config,
                $this->transformer
            ],
            function ($connector) {
                $connector
                    ->expects($this->once())
                    ->method('execute')
                    ->with($this->requestModel)
                    ->willReturn($this->response);
            }
        );

        $this->assertEquals($responseModel, $connector->request($this->requestModel));
    }

    /**
     * @return void
     * @covers ::request
     */
    public function testRequestReturnStandardResponseIfNoTransformerIsPresent(): void
    {
        $responseModel = new ResponseModel($this->response);

        $this->response->expects($this->once())->method('getStatusCode')->willReturn(Response::HTTP_OK);

        $this->transformer->expects($this->never())->method('transform');

        $connector = $this->getConnector(
            [
                self::SERVICE_CODE,
                self::ENDPOINT_CODE,
                $this->client,
                $this->config,
                null
            ],
            function ($connector) {
                $connector
                    ->expects($this->once())
                    ->method('execute')
                    ->with($this->requestModel)
                    ->willReturn($this->response);
            }
        );

        $this->assertEquals($responseModel, $connector->request($this->requestModel));
    }

    /**
     * @return void
     * @covers ::request()
     * @covers ::handleFailure()
     */
    public function testRequestThrowsUnknownClientExceptionForUnexpectedStatusCode(): void
    {
        $resultStatusCode = Response::HTTP_NOT_FOUND;
        $resultMessage = '__test_message__';

        $this->response->expects($this->once())->method('getStatusCode')->willReturn($resultStatusCode);
        $this->response->expects($this->once())->method('getBody')->willReturn($resultMessage);

        $connector = $this->getConnector(
            [
                self::SERVICE_CODE,
                self::ENDPOINT_CODE,
                $this->client,
                $this->config,
                null
            ],
            function ($connector) {
                $connector
                    ->expects($this->once())
                    ->method('execute')
                    ->with($this->requestModel)
                    ->willReturn($this->response);
            }
        );

        $this->expectException(UnknownClientException::class);
        $this->expectExceptionCode($resultStatusCode);
        $this->expectExceptionMessage($resultMessage);

        $connector->request($this->requestModel);
    }

    /**
     * @return void
     * @covers ::request()
     */
    public function testRequestThrowsClientExceptionOnGuzzleException(): void
    {
        $resultStatusCode = Response::HTTP_NOT_FOUND;
        $resultMessage = '__test_message__';

        $response = $this->createMock(PsrResponseInterface::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn($resultStatusCode);

        $exception = new RequestException(
            $resultMessage,
            $this->createMock(RequestInterface::class),
            $response
        );

        $connector = $this->getConnector(
            [
                self::SERVICE_CODE,
                self::ENDPOINT_CODE,
                $this->client,
                $this->config,
                null
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
        $this->expectExceptionCode($resultStatusCode);
        $this->expectExceptionMessage($resultMessage);

        $connector->request($this->requestModel);
    }
}
