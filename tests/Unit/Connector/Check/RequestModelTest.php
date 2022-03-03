<?php

namespace Hippy\Connector\Tests\Unit\Connector\Check;

use Hippy\Connector\Connector\Check\RequestModel;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Connector\Connector\Check\RequestModel */
class RequestModelTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     * @covers ::isDeep
     */
    public function testConstructWithoutHeaders(): void
    {
        $request = new RequestModel(true);

        $this->assertEquals(true, $request->isDeep());
        $this->assertEquals([], $request->getHeaders());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::isDeep
     */
    public function testConstructWithHeaders(): void
    {
        $headers = ['header' => '__dummy_value__'];
        $request = new RequestModel(true, $headers);

        $this->assertEquals(true, $request->isDeep());
        $this->assertEquals($headers, $request->getHeaders());
    }
}
