<?php

namespace Hippy\Connector\Model;

use Hippy\Model\Model;

/**
 * @method array<string, string|string[]> getHeaders()
 */
class RequestModel extends Model
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, string|string[]> $headers
     */
    public function __construct(
        array $data = [],
        protected array $headers = []
    ) {
        parent::__construct(array_merge($data));

        $this->addHideParser('headers');
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function setJsonContentTypeHeader(): self
    {
        return $this->setHeader('Content-Type', 'application/json');
    }

    /**
     * @param string $requestId
     * @return $this
     */
    public function setRequestIdHeader(string $requestId): self
    {
        return $this->setHeader('X-Request-Id', $requestId);
    }

    /**
     * @param string $token
     * @return $this
     */
    public function setBearerAuthHeader(string $token): self
    {
        return $this->setHeader('Authorization', 'Bearer ' . $token);
    }

    /**
     * @param string $tokenId
     * @param string $userId
     * @param string $userEmail
     * @return $this
     */
    public function setAuthHeaders(
        string $tokenId,
        string $userId,
        string $userEmail
    ): self {
        $this->setHeader('X-Auth-Token-Id', $tokenId);
        $this->setHeader('X-Auth-User-Id', $userId);
        $this->setHeader('X-Auth-User-Email', $userEmail);
        return $this;
    }
}
