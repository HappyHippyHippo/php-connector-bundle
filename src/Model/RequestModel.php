<?php

namespace Hippy\Connector\Model;

use Hippy\Model\Model;

class RequestModel extends Model implements RequestModelInterface
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
     * @return array<string, string|string[]>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string $name
     * @param string $value
     * @return RequestModelInterface
     */
    public function setHeader(string $name, string $value): RequestModelInterface
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @param string $token
     * @return RequestModelInterface
     */
    public function setBearerAuthHeader(string $token): RequestModelInterface
    {
        return $this->setHeader('Authorization', 'Bearer ' . $token);
    }

    /**
     * @return RequestModelInterface
     */
    public function setJsonContentTypeHeader(): RequestModelInterface
    {
        return $this->setHeader('Content-Type', 'application/json');
    }

    /**
     * @param string $requestId
     * @return RequestModelInterface
     */
    public function setRequestIdHeader(string $requestId): RequestModelInterface
    {
        return $this->setHeader('X-Request-Id', $requestId);
    }

    /**
     * @param int|string $orgId
     * @return RequestModelInterface
     */
    public function setOrganizationIdHeader(int|string $orgId): RequestModelInterface
    {
        return $this->setHeader('X-Organization-Id', (string) $orgId);
    }

    /**
     * @param string $tokenId
     * @param string $userId
     * @param string $userUUID
     * @param string $userEmail
     * @return RequestModelInterface
     */
    public function setAuthHeaders(
        string $tokenId,
        string $userId,
        string $userUUID,
        string $userEmail
    ): RequestModelInterface {
        $this->setHeader('X-Auth-Token-Id', $tokenId);
        $this->setHeader('X-Auth-User-Id', $userId);
        $this->setHeader('X-Auth-User-UUID', $userUUID);
        $this->setHeader('X-Auth-User-Email', $userEmail);

        return $this;
    }
}
