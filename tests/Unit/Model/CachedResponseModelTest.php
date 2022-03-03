<?php

namespace Hippy\Connector\Tests\Unit\Model;

use Hippy\Connector\Model\CachedResponseModel;
use Hippy\Error\ErrorCollection;
use Hippy\Model\ModelInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Connector\Model\CachedResponseModel */
class CachedResponseModelTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     * @covers ::getResponse
     * @covers ::getStatusCode
     * @covers ::getHeaders
     * @covers ::getErrors
     * @covers ::getData
     */
    public function testConstructorWithoutArguments(): void
    {
        $model = new CachedResponseModel();

        $this->assertNull($model->getResponse());
        $this->assertEquals(Response::HTTP_OK, $model->getStatusCode());
        $this->assertEquals([], $model->getHeaders());
        $this->assertEquals(new ErrorCollection(), $model->getErrors());
        $this->assertNull($model->getData());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::getResponse
     * @covers ::getStatusCode
     * @covers ::getHeaders
     * @covers ::getErrors
     * @covers ::getData
     */
    public function testConstructorWithArguments(): void
    {
        $statusCode = Response::HTTP_NOT_FOUND;
        $data = $this->createMock(ModelInterface::class);

        $model = new CachedResponseModel($statusCode, $data);

        $this->assertNull($model->getResponse());
        $this->assertEquals($statusCode, $model->getStatusCode());
        $this->assertEquals([], $model->getHeaders());
        $this->assertEquals(new ErrorCollection(), $model->getErrors());
        $this->assertSame($data, $model->getData());
    }
}
