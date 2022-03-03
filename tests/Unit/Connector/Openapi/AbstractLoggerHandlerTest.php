<?php

namespace Hippy\Connector\Tests\Unit\Connector\Openapi;

use Hippy\Connector\Connector\Openapi\AbstractConnector;
use Hippy\Connector\Connector\Openapi\AbstractLoggerHandler;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;

/** @coversDefaultClass \Hippy\Connector\Connector\Openapi\AbstractLoggerHandler */
class AbstractLoggerHandlerTest extends TestCase
{
    /**
     * @return void
     * @throws ReflectionException
     * @covers ::createLogEntrySkeleton
     */
    public function testCreateLogEntrySkeleton(): void
    {
        $sut = $this->getMockForAbstractClass(AbstractLoggerHandler::class);

        $method = new ReflectionMethod(AbstractLoggerHandler::class, 'createLogEntrySkeleton');
        $this->assertEquals([
            'method' => AbstractConnector::METHOD,
            'uri' => AbstractConnector::URI_PATTERN,
        ], $method->invoke($sut));
    }
}
