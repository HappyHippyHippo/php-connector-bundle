<?php

namespace Hippy\Connector\Tests\Unit\Exception;

use Hippy\Connector\Exception\ClientException;
use Hippy\Connector\Model\RequestModelInterface;
use Hippy\Error\Error;
use Hippy\Error\ErrorCollection;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Connector\Exception\ClientException */
class ClientExceptionTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     * @covers ::getRequest
     */
    public function testConstruct(): void
    {
        $request = $this->createMock(RequestModelInterface::class);
        $message = '__dummy_message__';
        $code = 123;
        $previous = new Exception();

        $sut = $this->getMockForAbstractClass(ClientException::class, [$request, $message, $code, $previous]);

        $this->assertSame($request, $sut->getRequest());
        $this->assertEquals(Response::HTTP_SERVICE_UNAVAILABLE, $sut->getStatusCode());
        $this->assertEquals($message, $sut->getMessage());
        $this->assertEquals($code, $sut->getCode());
        $this->assertEquals([], $sut->getHeaders());
        $this->assertEquals($previous, $sut->getPrevious());
    }

    /**
     * @param string $message
     * @return void
     * @covers ::parseErrorMessage
     * @dataProvider providerForFailedParseErrorMessageTests
     */
    public function testFailedParseErrorMessage(string $message): void
    {
        $request = $this->createMock(RequestModelInterface::class);
        $sut = $this->getMockForAbstractClass(ClientException::class, [$request, $message]);
        $this->assertEquals(new ErrorCollection(), $sut->getErrors());
    }

    /**
     * @return array<string, array<int, string|false>>
     */
    public function providerForFailedParseErrorMessageTests(): array
    {
        return [
            'empty message' => [''],
            'is an empty object' => [json_encode([])],
            'status field is not an array' => [json_encode(['status' => 123])],
            'status field is an empty array' => [json_encode(['status' => []])],
            'inner errors field is not an array' => [json_encode(['status' => ['errors' => 123]])],
            'inner errors field is an empty array' => [json_encode(['status' => ['errors' => []]])],
        ];
    }

    /**
     * @return void
     * @covers ::parseErrorMessage
     */
    public function testParseErrorMessage(): void
    {
        $message = json_encode([
            'status' => [
                'errors' => [
                    ['code' => '__dummy_code_1__', 'message' => '__dummy_message_1__'],
                    ['code' => '__dummy_code_2__', 'message' => '__dummy_message_2__'],
                ],
            ],
        ]);
        $expected = new ErrorCollection([
            new Error('__dummy_code_1__', '__dummy_message_1__'),
            new Error('__dummy_code_2__', '__dummy_message_2__'),
        ]);

        $request = $this->createMock(RequestModelInterface::class);
        $sut = $this->getMockForAbstractClass(ClientException::class, [$request, $message]);
        $this->assertEquals($expected, $sut->getErrors());
    }
}
