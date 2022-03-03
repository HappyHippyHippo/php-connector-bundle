<?php

namespace Hippy\Connector\Tests\Unit\Exception;

use Hippy\Connector\Exception\Exception;
use Hippy\Error\Error;
use Hippy\Error\ErrorCollection;
use Exception as DefaultException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Connector\Exception\Exception */
class ExceptionTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $message = '__dummy_message__';
        $code = 123;
        $previous = new DefaultException();

        $sut = $this->getMockForAbstractClass(Exception::class, [$message, $code, $previous]);

        $this->assertEquals(Response::HTTP_SERVICE_UNAVAILABLE, $sut->getStatusCode());
        $this->assertEquals($message, $sut->getMessage());
        $this->assertEquals($code, $sut->getCode());
        $this->assertEquals([], $sut->getHeaders());
        $this->assertEquals(new ErrorCollection(), $sut->getErrors());
        $this->assertEquals($previous, $sut->getPrevious());
    }
}
