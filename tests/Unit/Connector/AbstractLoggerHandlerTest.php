<?php

namespace Hippy\Connector\Tests\Unit\Connector;

use Hippy\Connector\Connector\AbstractLoggerHandler;
use Hippy\Connector\Log\AbstractLoggerAdapter;
use Hippy\Connector\Model\RequestModel;
use Hippy\Connector\Model\ResponseModel;
use Exception;
use GuzzleHttp\Exception\RequestException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/** @coversDefaultClass \Hippy\Connector\Connector\AbstractLoggerHandler */
class AbstractLoggerHandlerTest extends TestCase
{
    /** @var AbstractLoggerAdapter&MockObject */
    private AbstractLoggerAdapter $adapter;

    /** @var string */
    private const REQUEST_MSG = '__dummy_request_message__';

    /** @var string */
    private const RESPONSE_MSG = '__dummy_response_message__';

    /** @var string */
    private const CACHED_MSG = '__dummy_cached_response_message__';

    /** @var string */
    private const EXCEPTION_MSG = '__dummy_exception_message__';

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->adapter = $this->createMock(AbstractLoggerAdapter::class);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::writeRequest
     */
    public function testWriteRequestWithNoAdapter(): void
    {
        $request = $this->createMock(RequestModel::class);

        $this->adapter->expects($this->never())->method('logRequest');

        $sut = $this->createSUT();
        $sut->writeRequest($request);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::writeRequest
     */
    public function testWriteRequest(): void
    {
        $requestHeaders = ['header' => ['__dummy_request_header__']];
        $serializedRequest = ['field' => '__dummy_value__'];
        $mockedSkeletonEntry = ['mock' => '__dummy_skeleton_entry__'];
        $expected = array_merge($mockedSkeletonEntry, [
            'request' => [
                'headers' => ['header' => '__dummy_request_header__'],
                'params' => $serializedRequest,
            ],
        ]);

        $request = $this->getMockBuilder(RequestModel::class)
            ->onlyMethods(['jsonSerialize'])
            ->addMethods(['getHeaders'])
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())->method('getHeaders')->willReturn($requestHeaders);
        $request->expects($this->once())->method('jsonSerialize')->willReturn($serializedRequest);

        $this->adapter->expects($this->once())->method('logRequest')->with(self::REQUEST_MSG, $expected);

        $sut = $this->createSUT($this->adapter, $mockedSkeletonEntry);
        $sut->writeRequest($request);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::writeResponse
     * @covers ::flatHeaders
     */
    public function testWriteResponseWithNoAdapter(): void
    {
        $request = $this->createMock(RequestModel::class);
        $response = $this->createMock(ResponseModel::class);

        $this->adapter->expects($this->never())->method('logResponse');

        $sut = $this->createSUT();
        $sut->writeResponse($request, $response);
    }

    /**
     * @return void
     * @covers ::__construct
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

        $request = $this->getMockBuilder(RequestModel::class)
            ->onlyMethods(['jsonSerialize'])
            ->addMethods(['getHeaders'])
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())->method('getHeaders')->willReturn($requestHeaders);
        $request->expects($this->once())->method('jsonSerialize')->willReturn($serializedRequest);

        $response = $this->createMock(ResponseModel::class);
        $response->expects($this->once())->method('getHeaders')->willReturn($responseHeaders);
        $response->expects($this->once())->method('jsonSerialize')->willReturn($serializedResponse);
        $response->expects($this->once())->method('getStatusCode')->willReturn($statusCode);

        $this->adapter->expects($this->once())->method('logResponse')->with(self::RESPONSE_MSG, $expected);

        $sut = $this->createSUT($this->adapter, $mockedSkeletonEntry);
        $sut->writeResponse($request, $response);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::writeDryResponse
     * @covers ::flatHeaders
     */
    public function testWriteDryResponseWithNoAdapter(): void
    {
        $request = $this->createMock(RequestModel::class);
        $response = $this->createMock(ResponseModel::class);

        $this->adapter->expects($this->never())->method('logResponse');

        $sut = $this->createSUT();
        $sut->writeDryResponse($request, $response);
    }

    /**
     * @return void
     * @covers ::__construct
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

        $request = $this->getMockBuilder(RequestModel::class)
            ->onlyMethods(['jsonSerialize'])
            ->addMethods(['getHeaders'])
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())->method('getHeaders')->willReturn($requestHeaders);
        $request->expects($this->once())->method('jsonSerialize')->willReturn($serializedRequest);

        $response = $this->createMock(ResponseModel::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn($statusCode);

        $this->adapter->expects($this->once())->method('logResponse')->with(self::RESPONSE_MSG, $expected);

        $sut = $this->createSUT($this->adapter, $mockedSkeletonEntry);
        $sut->writeDryResponse($request, $response);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::writeCachedResponse
     * @covers ::flatHeaders
     */
    public function testWriteCachedResponseWithNoAdapter(): void
    {
        $request = $this->createMock(RequestModel::class);
        $response = $this->createMock(ResponseModel::class);

        $this->adapter->expects($this->never())->method('logCachedResponse');

        $sut = $this->createSUT();
        $sut->writeCachedResponse($request, $response);
    }

    /**
     * @return void
     * @covers ::__construct
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

        $request = $this->getMockBuilder(RequestModel::class)
            ->onlyMethods(['jsonSerialize'])
            ->addMethods(['getHeaders'])
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())->method('getHeaders')->willReturn($requestHeaders);
        $request->expects($this->once())->method('jsonSerialize')->willReturn($serializedRequest);

        $response = $this->createMock(ResponseModel::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn($statusCode);

        $this->adapter->expects($this->once())->method('logCachedResponse')->with(self::CACHED_MSG, $expected);

        $sut = $this->createSUT($this->adapter, $mockedSkeletonEntry);
        $sut->writeCachedResponse($request, $response);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::writeException
     * @covers ::flatHeaders
     */
    public function testWriteExceptionWithNoAdapter(): void
    {
        $request = $this->createMock(RequestModel::class);
        $exception = $this->createMock(RequestException::class);

        $this->adapter->expects($this->never())->method('logException');

        $sut = $this->createSUT();
        $sut->writeException($request, $exception);
    }

    /**
     * @return void
     * @covers ::__construct
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

        $request = $this->getMockBuilder(RequestModel::class)
            ->onlyMethods(['jsonSerialize'])
            ->addMethods(['getHeaders'])
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())->method('getHeaders')->willReturn($requestHeaders);
        $request->expects($this->once())->method('jsonSerialize')->willReturn($serializedRequest);

        $exception = $this->createMock(RequestException::class);
        $property = new ReflectionProperty(Exception::class, 'code');
        $property->setValue($exception, $errorCode);
        $property = new ReflectionProperty(Exception::class, 'message');
        $property->setValue($exception, $errorMessage);

        $this->adapter->expects($this->once())->method('logException')->with(self::EXCEPTION_MSG, $expected);

        $sut = $this->createSUT($this->adapter, $mockedSkeletonEntry);
        $sut->writeException($request, $exception);
    }

    /**
     * @param AbstractLoggerAdapter|null $adapter
     * @param array<string, mixed> $mockedSkeletonEntry
     * @return AbstractLoggerHandler
     */
    private function createSUT(
        ?AbstractLoggerAdapter $adapter = null,
        array $mockedSkeletonEntry = []
    ): AbstractLoggerHandler {
        $sut = $this->getMockForAbstractClass(
            AbstractLoggerHandler::class,
            [$adapter, self::REQUEST_MSG, self::RESPONSE_MSG, self::CACHED_MSG, self::EXCEPTION_MSG],
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
