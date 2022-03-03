<?php

namespace Hippy\Connector\Tests\Unit\Connector\AbstractConnector;

use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Connector\Connector\AbstractConnector */
class GettersTest extends TestBuilder
{
    /**
     * @return void
     * @covers ::getServiceCode
     */
    public function testGetServiceCode(): void
    {
        $service = 1;
        $endpoint = 2;

        $this->assertEquals($service, $this->getConnector(
            [$service, $endpoint, $this->client, $this->config, $this->transformer, $this->logger, $this->cache]
        )->getServiceCode());
    }

    /**
     * @return void
     * @covers ::getEndpointCode
     */
    public function testGetEndpointCode(): void
    {
        $service = 1;
        $endpoint = 2;

        $this->assertEquals($endpoint, $this->getConnector(
            [$service, $endpoint, $this->client, $this->config, $this->transformer, $this->logger, $this->cache]
        )->getEndpointCode());
    }

    /**
     * @return void
     * @covers ::getExpectedStatusCode
     */
    public function testGetExpectedStatusCode(): void
    {
        $service = 1;
        $endpoint = 2;

        $this->assertEquals(Response::HTTP_OK, $this->getConnector(
            [$service, $endpoint, $this->client, $this->config, $this->transformer, $this->logger, $this->cache]
        )->getExpectedStatusCode());
    }
}
