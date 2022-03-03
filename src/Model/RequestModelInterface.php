<?php

namespace Hippy\Connector\Model;

use Hippy\Model\ModelInterface;

interface RequestModelInterface extends ModelInterface
{
    /**
     * @return array<string, string>
     */
    public function getHeaders(): array;

    /**
     * @param string $name
     * @param string $value
     * @return RequestModelInterface
     */
    public function setHeader(string $name, string $value): RequestModelInterface;

    /**
     * @param string $token
     * @return RequestModelInterface
     */
    public function setBearerAuthHeader(string $token): RequestModelInterface;

    /**
     * @return RequestModelInterface
     */
    public function setJsonContentTypeHeader(): RequestModelInterface;

    /**
     * @param string $requestId
     * @return RequestModelInterface
     */
    public function setRequestIdHeader(string $requestId): RequestModelInterface;

    /**
     * @param int|string $orgId
     * @return RequestModelInterface
     */
    public function setOrganizationIdHeader(int|string $orgId): RequestModelInterface;

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
    ): RequestModelInterface;
}
