<?php

namespace Hippy\Connector\Tests\Unit\Connector\Config;

use Hippy\Connector\Connector\Config\ResponseModel;
use Hippy\Connector\Connector\Config\ResponseHandler;
use Hippy\Error\Error;
use Hippy\Error\ErrorCollection;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Connector\Connector\Config\ResponseHandler */
class ResponseTransformerTest extends TestCase
{
    /**
     * @return void
     * @covers ::transform
     */
    public function testTransform(): void
    {
        $statusCode = Response::HTTP_CREATED;
        $errorCode1 = 123;
        $errorCode2 = 456;
        $errorMessage1 = '__dummy_error_message_1__';
        $errorMessage2 = '__dummy_error_message_2__';
        $errors = new ErrorCollection([
            new Error($errorCode1, $errorMessage1),
            new Error($errorCode2, $errorMessage2),
        ]);
        $data = ['__dummy_data__'];

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn($statusCode);
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
                'data' => $data,
            ]));

        $transformer = new ResponseHandler();
        $result = $transformer->transform($response);
        if (!($result instanceof ResponseModel)) {
            $this->fail('response object is not a response model');
        }


        $this->assertEquals($statusCode, $result->getStatusCode());
        $this->assertEquals($errors, $result->getErrors());
        $this->assertEquals($data, $result->getData());
    }
}
