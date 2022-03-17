<?php

namespace Hippy\Connector\Tests\Unit\Connector\Config;

use Hippy\Connector\Connector\Config\ResponseModel;
use Hippy\Error\Error;
use Hippy\Error\ErrorCollection;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/** @coversDefaultClass \Hippy\Connector\Connector\Config\ResponseModel */
class ResponseModelTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructorWithoutErrorsArguments(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $data = ['__dummy_data__'];

        $model = new ResponseModel($response, null, $data);

        $this->assertEquals($response, $model->getResponse());
        $this->assertEquals(new ErrorCollection(), $model->getErrors());
        $this->assertEquals($data, $model->getData());
    }

    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructorWithErrorsArguments(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $errors = (new ErrorCollection())->add(new Error(123, '__dummy_error_message__'));
        $data = ['__dummy_data__'];

        $model = new ResponseModel($response, $errors, $data);

        $this->assertEquals($response, $model->getResponse());
        $this->assertEquals($errors, $model->getErrors());
        $this->assertEquals($data, $model->getData());
    }
}
