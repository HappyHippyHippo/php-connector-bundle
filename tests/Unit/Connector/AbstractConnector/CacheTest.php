<?php

namespace Hippy\Connector\Tests\Unit\Connector\AbstractConnector;

use DateTime;
use Hippy\Connector\Model\ResponseModel;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Connector\Connector\AbstractConnector */
class CacheTest extends TestBuilder
{
    /** @var int */
    private const SERVICE_CODE = 1;

    /** @var int */
    private const ENDPOINT_CODE = 2;

    /**
     * @return void
     * @covers ::request()
     * @covers ::getCachedResponse()
     * @covers ::cacheResponse()
     */
    public function testRequestDontCheckCacheIfNotEnabled(): void
    {
        $this->config->expects($this->exactly(2))->method('isCacheEnabled')->willReturn(false);

        $this->cache->expects($this->never())->method('loadResponse');
        $this->cache->expects($this->never())->method('storeResponse');

        $this->response->expects($this->once())->method('getStatusCode')->willReturn(Response::HTTP_OK);

        $connector = $this->getConnector(
            [
                self::SERVICE_CODE,
                self::ENDPOINT_CODE,
                $this->client,
                $this->config,
                null,
                null,
                $this->cache
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
     * @covers ::getCachedResponse()
     * @covers ::cacheResponse()
     */
    public function testRequestStoreNewResponseWithInfiniteTTL(): void
    {
        $responseModel = new ResponseModel($this->response);

        $this->config->expects($this->exactly(2))->method('isCacheEnabled')->willReturn(true);
        $this->config->expects($this->once())->method('getCacheTTL')->willReturn(0);

        $this->cache
            ->expects($this->once())
            ->method('loadResponse')
            ->with($this->requestModel)
            ->willReturn(null);
        $this->cache
            ->expects($this->once())
            ->method('storeResponse')
            ->with($this->requestModel, $responseModel, new DateTime('9999-12-31 23:59:59'));

        $this->response->expects($this->once())->method('getStatusCode')->willReturn(Response::HTTP_OK);

        $connector = $this->getConnector(
            [
                self::SERVICE_CODE,
                self::ENDPOINT_CODE,
                $this->client,
                $this->config,
                null,
                null,
                $this->cache
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
     * @covers ::getCachedResponse()
     * @covers ::cacheResponse()
     */
    public function testRequestStoreNewResponseWithConfiguredTTL(): void
    {
        $responseModel = new ResponseModel($this->response);

        $this->config->expects($this->exactly(2))->method('isCacheEnabled')->willReturn(true);
        $this->config->expects($this->once())->method('getCacheTTL')->willReturn(100);

        $this->cache
            ->expects($this->once())
            ->method('loadResponse')
            ->with($this->requestModel)
            ->willReturn(null);
        $this->cache
            ->expects($this->once())
            ->method('storeResponse')
            ->with($this->requestModel, $responseModel, (new DateTime())->setTimestamp(time() + 100));

        $this->response->expects($this->once())->method('getStatusCode')->willReturn(Response::HTTP_OK);

        $connector = $this->getConnector(
            [
                self::SERVICE_CODE,
                self::ENDPOINT_CODE,
                $this->client,
                $this->config,
                null,
                null,
                $this->cache
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
     * @covers ::getCachedResponse()
     * @covers ::cacheResponse()
     */
    public function testRequestDontStoreIfUnexpectedStatusCode(): void
    {
        $statusCode = Response::HTTP_NO_CONTENT;

        $this->config->expects($this->once())->method('isCacheEnabled')->willReturn(true);

        $this->cache->expects($this->once())->method('loadResponse')->with($this->requestModel)->willReturn(null);
        $this->cache->expects($this->never())->method('storeResponse');

        $stream = $this->createMock(StreamInterface::class);
        $this->response->expects($this->once())->method('getStatusCode')->willReturn($statusCode);
        $this->response->expects($this->once())->method('getBody')->willReturn($stream);

        $connector = $this->getConnector(
            [
                self::SERVICE_CODE,
                self::ENDPOINT_CODE,
                $this->client,
                $this->config,
                null,
                null,
                $this->cache
            ],
            function ($connector) {
                $connector
                    ->expects($this->once())
                    ->method('execute')
                    ->with($this->requestModel)
                    ->willReturn($this->response);
                $connector->method('handleFailure');
            },
            ['handleFailure']
        );

        $connector->expects($this->once())->method('handleFailure')->with($this->requestModel, $stream, $statusCode);

        $connector->request($this->requestModel);
    }

    /**
     * @return void
     * @covers ::request()
     * @covers ::getCachedResponse()
     */
    public function testRequestReturnStoredResponseWithoutServiceCall(): void
    {
        $responseModel = new ResponseModel($this->response);

        $this->config->expects($this->exactly(1))->method('isCacheEnabled')->willReturn(true);

        $this->cache
            ->expects($this->once())
            ->method('loadResponse')
            ->with($this->requestModel)
            ->WillReturn($responseModel);
        $this->cache->expects($this->never())->method('storeResponse');

        $connector = $this->getConnector(
            [
                self::SERVICE_CODE,
                self::ENDPOINT_CODE,
                $this->client,
                $this->config,
                null,
                null,
                $this->cache
            ],
            function ($connector) {
                $connector->expects($this->never())->method('execute');
            }
        );

        $this->assertEquals($responseModel, $connector->request($this->requestModel));
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
