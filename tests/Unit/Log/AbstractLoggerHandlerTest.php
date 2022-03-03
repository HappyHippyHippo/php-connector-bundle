<?php

namespace Hippy\Connector\Tests\Unit\Log;

use Hippy\Connector\Log\AbstractLoggerHandler;
use Hippy\Connector\Log\LoggerAdapterInterface;
use Hippy\Connector\Model\RequestModelInterface;
use Hippy\Connector\Model\ResponseModelInterface;
use Exception;
use GuzzleHttp\Exception\RequestException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/** @coversDefaultClass \Hippy\Connector\Log\AbstractLoggerHandler */
class AbstractLoggerHandlerTest extends TestCase
{
    /** @var LoggerAdapterInterface&MockObject */
    private LoggerAdapterInterface $adapter;

    /** @var string */
    private string $requestMsg;

    /** @var string */
    private string $responseMsg;

    /** @var string */
    private string $cachedResponseMsg;

    /** @var string */
    private string $exceptionMsg;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->requestMsg = '__dummy_request_message__';
        $this->responseMsg = '__dummy_response_message__';
        $this->cachedResponseMsg = '__dummy_cached_response_message__';
        $this->exceptionMsg = '__dummy_exception_message__';

        $this->adapter = $this->createMock(LoggerAdapterInterface::class);
    }

    /**
     * @return void
     * @covers ::__construct()
     * @covers ::getAdapter()
     */
    public function testGetAdapter(): void
    {
        $sut = $this->getMockForAbstractClass(AbstractLoggerHandler::class, [$this->adapter]);
        $this->assertEquals($this->adapter, $sut->getAdapter());
    }

    /**
     * @return void
     * @covers ::writeRequest
     */
    public function testWriteRequestWithNoAdapter(): void
    {
        $request = $this->createMock(RequestModelInterface::class);

        $this->adapter->expects($this->never())->method('logRequest');

        $sut = $this->createSUT();
        $sut->writeRequest($request);
    }

    /**
     * @return void
     * @covers ::writeRequest
     */
    public function testWriteRequest(): void
    {
        $mockedSkeletonEntry = ['mock' => '__dummy_skeleton_entry__'];
        $expected = $mockedSkeletonEntry;

        $request = $this->createMock(RequestModelInterface::class);

        $this->adapter->expects($this->once())->method('logRequest')->with($this->requestMsg, $expected);

        $sut = $this->createSUT($this->adapter, $mockedSkeletonEntry);
        $sut->writeRequest($request);
    }

    /**
     * @return void
     * @covers ::writeResponse
     * @covers ::flatHeaders
     */
    public function testWriteResponseWithNoAdapter(): void
    {
        $request = $this->createMock(RequestModelInterface::class);
        $response = $this->createMock(ResponseModelInterface::class);

        $this->adapter->expects($this->never())->method('logResponse');

        $sut = $this->createSUT();
        $sut->writeResponse($request, $response);
    }

    /**
     * @return void
     * @covers ::writeResponse
     * @covers ::flatHeaders
     */
    public function testWriteResponse(): void
    {
        $statusCode = 123;
        $mockedSkeletonEntry = ['mock' => '__dummy_skeleton_entry__'];
        $requestHeaders = ['header' => ['__dummy_request_header__']];
        $serializedRequest = ['field' => '__dummy_value__'];
        $responseHeaders = ['header' => ['__dummy_response_header__']];
        $serializedResponse = ['__dummy_response__'];
        $expected = array_merge($mockedSkeletonEntry, [
            'statusCode' => $statusCode,
            'request' => [
                'headers' => ['header' => '__dummy_request_header__'],
                'params' => $serializedRequest,
            ],
            'response' => [
                'headers' => ['header' => '__dummy_response_header__'],
                'params' => $serializedResponse,
            ],
        ]);

        $request = $this->createMock(RequestModelInterface::class);
        $request->expects($this->once())->method('getHeaders')->willReturn($requestHeaders);
        $request->expects($this->once())->method('jsonSerialize')->willReturn($serializedRequest);

        $response = $this->createMock(ResponseModelInterface::class);
        $response->expects($this->once())->method('getHeaders')->willReturn($responseHeaders);
        $response->expects($this->once())->method('jsonSerialize')->willReturn($serializedResponse);
        $response->expects($this->once())->method('getStatusCode')->willReturn($statusCode);

        $this->adapter->expects($this->once())->method('logResponse')->with($this->responseMsg, $expected);

        $sut = $this->createSUT($this->adapter, $mockedSkeletonEntry);
        $sut->writeResponse($request, $response);
    }

    /**
     * @return void
     * @covers ::writeDryResponse
     * @covers ::flatHeaders
     */
    public function testWriteDryResponseWithNoAdapter(): void
    {
        $request = $this->createMock(RequestModelInterface::class);
        $response = $this->createMock(ResponseModelInterface::class);

        $this->adapter->expects($this->never())->method('logResponse');

        $sut = $this->createSUT();
        $sut->writeDryResponse($request, $response);
    }

    /**
     * @return void
     * @covers ::writeDryResponse
     * @covers ::flatHeaders
     */
    public function testWriteDryResponse(): void
    {
        $statusCode = 123;
        $mockedSkeletonEntry = ['mock' => '__dummy_skeleton_entry__'];
        $requestHeaders = ['header' => ['__dummy_request_header__']];
        $serializedRequest = ['field' => '__dummy_value__'];
        $expected = array_merge($mockedSkeletonEntry, [
            'statusCode' => $statusCode,
            'request' => [
                'headers' => ['header' => '__dummy_request_header__'],
                'params' => $serializedRequest,
            ],
        ]);

        $request = $this->createMock(RequestModelInterface::class);
        $request->expects($this->once())->method('getHeaders')->willReturn($requestHeaders);
        $request->expects($this->once())->method('jsonSerialize')->willReturn($serializedRequest);

        $response = $this->createMock(ResponseModelInterface::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn($statusCode);

        $this->adapter->expects($this->once())->method('logResponse')->with($this->responseMsg, $expected);

        $sut = $this->createSUT($this->adapter, $mockedSkeletonEntry);
        $sut->writeDryResponse($request, $response);
    }

    /**
     * @return void
     * @covers ::writeCachedResponse
     * @covers ::flatHeaders
     */
    public function testWriteCachedResponseWithNoAdapter(): void
    {
        $request = $this->createMock(RequestModelInterface::class);
        $response = $this->createMock(ResponseModelInterface::class);

        $this->adapter->expects($this->never())->method('logCachedResponse');

        $sut = $this->createSUT();
        $sut->writeCachedResponse($request, $response);
    }

    /**
     * @return void
     * @covers ::writeCachedResponse
     * @covers ::flatHeaders
     */
    public function testWriteCachedResponse(): void
    {
        $statusCode = 123;
        $mockedSkeletonEntry = ['mock' => '__dummy_skeleton_entry__'];
        $requestHeaders = ['header' => ['__dummy_request_header__']];
        $serializedRequest = ['field' => '__dummy_value__'];
        $expected = array_merge($mockedSkeletonEntry, [
            'statusCode' => $statusCode,
            'request' => [
                'headers' => ['header' => '__dummy_request_header__'],
                'params' => $serializedRequest,
            ],
        ]);

        $request = $this->createMock(RequestModelInterface::class);
        $request->expects($this->once())->method('getHeaders')->willReturn($requestHeaders);
        $request->expects($this->once())->method('jsonSerialize')->willReturn($serializedRequest);

        $response = $this->createMock(ResponseModelInterface::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn($statusCode);

        $this->adapter->expects($this->once())->method('logCachedResponse')->with($this->cachedResponseMsg, $expected);

        $sut = $this->createSUT($this->adapter, $mockedSkeletonEntry);
        $sut->writeCachedResponse($request, $response);
    }

    /**
     * @return void
     * @covers ::writeException
     * @covers ::flatHeaders
     */
    public function testWriteExceptionWithNoAdapter(): void
    {
        $request = $this->createMock(RequestModelInterface::class);
        $exception = $this->createMock(RequestException::class);

        $this->adapter->expects($this->never())->method('logException');

        $sut = $this->createSUT();
        $sut->writeException($request, $exception);
    }

    /**
     * @return void
     * @covers ::writeException
     * @covers ::flatHeaders
     */
    public function testWriteException(): void
    {
        $mockedSkeletonEntry = ['mock' => '__dummy_skeleton_entry__'];
        $requestHeaders = ['header' => ['__dummy_request_header__']];
        $serializedRequest = ['field' => '__dummy_value__'];
        $errorCode = 123;
        $errorMessage = '__dummy_error_message__';
        $expected = array_merge($mockedSkeletonEntry, [
            'request' => [
                'headers' => ['header' => '__dummy_request_header__'],
                'params' => $serializedRequest,
            ],
            'error' => $errorCode,
            'message' => $errorMessage,
        ]);

        $request = $this->createMock(RequestModelInterface::class);
        $request->expects($this->once())->method('getHeaders')->willReturn($requestHeaders);
        $request->expects($this->once())->method('jsonSerialize')->willReturn($serializedRequest);

        $exception = $this->createMock(RequestException::class);
        $property = new ReflectionProperty(Exception::class, 'code');
        $property->setValue($exception, $errorCode);
        $property = new ReflectionProperty(Exception::class, 'message');
        $property->setValue($exception, $errorMessage);

        $this->adapter->expects($this->once())->method('logException')->with($this->exceptionMsg, $expected);

        $sut = $this->createSUT($this->adapter, $mockedSkeletonEntry);
        $sut->writeException($request, $exception);
    }

    /**
     * @param LoggerAdapterInterface|null $adapter
     * @param array<string, mixed> $mockedSkeletonEntry
     * @return AbstractLoggerHandler
     */
    private function createSUT(
        ?LoggerAdapterInterface $adapter = null,
        array $mockedSkeletonEntry = []
    ): AbstractLoggerHandler {
        $sut = $this->getMockForAbstractClass(
            AbstractLoggerHandler::class,
            [
                $adapter,
                $this->requestMsg,
                $this->responseMsg,
                $this->cachedResponseMsg,
                $this->exceptionMsg
            ],
            '',
            true,
            true,
            true,
            ['createLogEntrySkeleton']
        );

        $sut
            ->expects($this->exactly(!is_null($adapter) ? 1 : 0))
            ->method('createLogEntrySkeleton')
            ->willReturn($mockedSkeletonEntry);

        return $sut;
    }
}
