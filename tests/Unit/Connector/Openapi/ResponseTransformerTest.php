<?php

namespace Hippy\Connector\Tests\Unit\Connector\Openapi;

use Hippy\Connector\Connector\Openapi\ResponseModel;
use Hippy\Connector\Connector\Openapi\ResponseTransformer;
use Hippy\Error\Error;
use Hippy\Error\ErrorCollection;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Connector\Connector\Openapi\ResponseTransformer */
class ResponseTransformerTest extends TestCase
{
    /**
     * @return void
     * @covers ::transform
     */
    public function testTransformValid(): void
    {
        $statusCode = Response::HTTP_OK;
        $data = '__dummy_data__';

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->any())->method('getStatusCode')->willReturn($statusCode);
        $response->expects($this->once())->method('getBody')->willReturn($data);

        $transformer = new ResponseTransformer();
        $result = $transformer->transform($response);
        if (!($result instanceof ResponseModel)) {
            $this->fail('response object is not a response model');
        }

        $this->assertInstanceOf(ResponseModel::class, $result);
        $this->assertEquals($statusCode, $result->getStatusCode());
        $this->assertEquals(new ErrorCollection(), $result->getErrors());
    }

    /**
     * @return void
     * @covers ::transform
     */
    public function testTransformOnFailure(): void
    {
        $statusCode = Response::HTTP_NOT_FOUND;
        $errorCode1 = 123;
        $errorCode2 = 456;
        $errorMessage1 = '__dummy_error_message_1__';
        $errorMessage2 = '__dummy_error_message_2__';
        $errors = new ErrorCollection([
            new Error($errorCode1, $errorMessage1),
            new Error($errorCode2, $errorMessage2),
        ]);

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->any())->method('getStatusCode')->willReturn($statusCode);
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn(json_encode([
                'status' => [
                    'success' => true,
                    'errors' => [
                        ['code' => $errorCode1, 'message' => $errorMessage1],
                        ['code' => $errorCode2, 'message' => $errorMessage2],
                    ],
                ],
                'data' => ['__dummy_data__'],
            ]));

        $transformer = new ResponseTransformer();
        $result = $transformer->transform($response);
        if (!($result instanceof ResponseModel)) {
            $this->fail('response object is not a response model');
        }

        $this->assertEquals($statusCode, $result->getStatusCode());
        $this->assertEquals($errors, $result->getErrors());
        $this->assertEquals('', $result->getData());
    }
}
