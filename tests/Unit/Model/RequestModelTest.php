<?php

namespace Hippy\Connector\Tests\Unit\Model;

use Hippy\Connector\Model\RequestModel;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Connector\Model\RequestModel */
class RequestModelTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $data = ['field' => '__dummy_field__'];
        $headers = ['header' => '__dummy_value__'];
        $sut = new class ($data, $headers) extends RequestModel {
            protected string $field;
            public function getField(): string
            {
                return $this->field;
            }
        };

        $this->assertEquals($data['field'], $sut->getField());
        $this->assertEquals($headers, $sut->getHeaders());
    }

    /**
     * @return void
     * @covers ::__construct
     */
    public function testGetHeaders(): void
    {
        $headers = ['header' => '__dummy_value__'];
        $sut = new RequestModel([], $headers);

        $this->assertEquals($headers, $sut->getHeaders());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::setHeader
     */
    public function testSetHeader(): void
    {
        $sut = new RequestModel();

        $this->assertSame($sut, $sut->setHeader('header', '__dummy_value__'));

        $this->assertEquals(['header' => '__dummy_value__'], $sut->getHeaders());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::setJsonContentTypeHeader
     */
    public function testSetJsonContentTypeHeader(): void
    {
        $sut = new RequestModel();

        $this->assertSame($sut, $sut->setJsonContentTypeHeader());

        $this->assertEquals(['Content-Type' => 'application/json'], $sut->getHeaders());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::setRequestIdHeader
     */
    public function testSetRequestIdHeader(): void
    {
        $requestId = '__dummy_request_id__';

        $sut = new RequestModel();

        $this->assertSame($sut, $sut->setRequestIdHeader($requestId));

        $this->assertEquals(['X-Request-Id' => $requestId], $sut->getHeaders());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::setBearerAuthHeader
     */
    public function testSetBearerAuthHeader(): void
    {
        $token = '__dummy_token__';

        $sut = new RequestModel();

        $this->assertSame($sut, $sut->setBearerAuthHeader($token));

        $this->assertEquals(['Authorization' => 'Bearer ' . $token], $sut->getHeaders());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::setAuthHeaders
     */
    public function testSetAuthHeaders(): void
    {
        $tokenId = '__dummy_token_id__';
        $userId = '__dummy_user_id__';
        $userEmail = '__dummy_user_email__';

        $sut = new RequestModel();

        $this->assertSame($sut, $sut->setAuthHeaders($tokenId, $userId, $userEmail));

        $this->assertEquals([
            'X-Auth-Token-Id' => $tokenId,
            'X-Auth-User-Id' => $userId,
            'X-Auth-User-Email' => $userEmail,
        ], $sut->getHeaders());
    }
}
