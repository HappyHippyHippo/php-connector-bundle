<?php

namespace Hippy\Connector\Tests\Unit\Model;

use Hippy\Connector\Model\ResponseModel;
use Hippy\Error\Error;
use Hippy\Error\ErrorCollection;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/** @coversDefaultClass \Hippy\Connector\Model\ResponseModel */
class ResponseModelTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructorWithoutErrorsArguments(): void
    {
        $response = $this->createMock(ResponseInterface::class);

        $model = new ResponseModel($response);

        $this->assertEquals($response, $model->getResponse());
        $this->assertEquals(new ErrorCollection(), $model->getErrors());
    }

    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructorWithErrorsArguments(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $errors = (new ErrorCollection())->add(new Error(123, '__dummy_error_message__'));

        $model = new ResponseModel($response, $errors);

        $this->assertEquals($response, $model->getResponse());
        $this->assertEquals($errors, $model->getErrors());
    }

    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructDontShowResponseInJsonSerialize(): void
    {
        $errorCode = 1;
        $errorMessage = '__dummy_error_message__';

        $response = $this->createMock(ResponseInterface::class);
        $model = new ResponseModel($response, new ErrorCollection([new Error($errorCode, $errorMessage)]));

        $this->assertEquals([
            'errors' => [['code' => 'c' . $errorCode, 'message' => $errorMessage]]
        ], $model->jsonSerialize());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::getStatusCode
     */
    public function testGetStatusCode(): void
    {
        $statusCode = 123;
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn($statusCode);

        $model = new ResponseModel($response);

        $this->assertEquals($statusCode, $model->getStatusCode());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::getHeaders
     */
    public function testGetHeaders(): void
    {
        $headers = ['__dummy_header__'];
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('getHeaders')->willReturn($headers);

        $model = new ResponseModel($response);

        $this->assertEquals($headers, $model->getHeaders());
    }
}
