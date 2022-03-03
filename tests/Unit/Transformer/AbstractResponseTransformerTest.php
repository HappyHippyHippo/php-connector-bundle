<?php

namespace Hippy\Connector\Tests\Unit\Transformer;

use Hippy\Connector\Exception\InvalidResponseContentException;
use Hippy\Connector\Transformer\AbstractResponseTransformer;
use Hippy\Error\Error;
use Hippy\Error\ErrorCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use ReflectionException;
use ReflectionMethod;

/** @coversDefaultClass \Hippy\Connector\Transformer\AbstractResponseTransformer */
class AbstractResponseTransformerTest extends TestCase
{
    /** @var AbstractResponseTransformer&MockObject */
    private AbstractResponseTransformer $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->sut = $this->getMockForAbstractClass(AbstractResponseTransformer::class);
    }

    /**
     * @return void
     * @throws ReflectionException
     * @covers ::getContent
     */
    public function testGetContent(): void
    {
        $expected = ['__dummy_content__'];
        $body = json_encode($expected);

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('getBody')->willReturn($body);

        $method = new ReflectionMethod(AbstractResponseTransformer::class, 'getContent');
        $result = $method->invoke($this->sut, $response);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return void
     * @throws ReflectionException
     * @covers ::getContent
     */
    public function testGetContentThrowsOnNonObjectResponse(): void
    {
        $body = json_encode('string');

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('getBody')->willReturn($body);

        $this->expectException(InvalidResponseContentException::class);
        $this->expectExceptionMessage('string');

        $method = new ReflectionMethod(AbstractResponseTransformer::class, 'getContent');
        $method->invoke($this->sut, $response);
    }

    /**
     * @param array<string, mixed> $response
     * @param bool $expected
     * @return void
     * @throws ReflectionException
     * @covers ::getStatus
     * @dataProvider providerForGetStatusTests
     */
    public function testGetStatus(array $response, bool $expected): void
    {
        $method = new ReflectionMethod(AbstractResponseTransformer::class, 'getStatus');
        $this->assertEquals($expected, $method->invoke($this->sut, $response));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function providerForGetStatusTests(): array
    {
        return [
            'no status' => [
                'response' => [],
                'expected' => false,
            ],
            'status is a string' => [
                'response' => ['status' => '__dummy_string__'],
                'expected' => false,
            ],
            'status is an array without success' => [
                'response' => ['status' => []],
                'expected' => false,
            ],
            'status is an array with false success' => [
                'response' => ['status' => ['success' => false]],
                'expected' => false,
            ],
            'status is an array with true success' => [
                'response' => ['status' => ['success' => true]],
                'expected' => true,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $response
     * @param ErrorCollection $expected
     * @return void
     * @throws ReflectionException
     * @covers ::getErrors
     * @dataProvider providerForGetErrorsTests
     */
    public function testGetErrors(array $response, ErrorCollection $expected): void
    {
        $method = new ReflectionMethod(AbstractResponseTransformer::class, 'getErrors');
        $this->assertEquals($expected, $method->invoke($this->sut, $response));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function providerForGetErrorsTests(): array
    {
        return [
            'no status' => [
                'response' => [],
                'expected' => new ErrorCollection(),
            ],
            'status is a string' => [
                'response' => ['status' => '__dummy_string__'],
                'expected' => new ErrorCollection(),
            ],
            'errors is a string' => [
                'response' => ['status' => ['errors' => '__dummy_string__']],
                'expected' => new ErrorCollection(),
            ],
            'errors is an array without data' => [
                'response' => ['status' => ['errors' => []]],
                'expected' => new ErrorCollection(),
            ],
            'errors is an array with single error' => [
                'response' => ['status' => ['errors' => [['code' => 123, 'message' => 'message']]]],
                'expected' => new ErrorCollection([new Error(123, 'message')]),
            ],
            'errors is an array with multiple error' => [
                'response' => ['status' => ['errors' => [
                    ['code' => 1, 'message' => 'message.1'],
                    ['code' => 2, 'message' => 'message.2'],
                    ['code' => 3, 'message' => 'message.3'],
                ]]],
                'expected' => new ErrorCollection([
                    new Error(1, 'message.1'),
                    new Error(2, 'message.2'),
                    new Error(3, 'message.3'),
                ]),
            ],
        ];
    }
}
