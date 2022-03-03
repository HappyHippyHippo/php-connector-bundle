<?php

namespace Hippy\Connector\Tests\Unit\Connector\Config;

use Hippy\Connector\Connector\Config\AbstractConnector;
use Hippy\Connector\Connector\Config\AbstractLoggerHandler;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;

/** @coversDefaultClass \Hippy\Connector\Connector\Config\AbstractLoggerHandler */
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
