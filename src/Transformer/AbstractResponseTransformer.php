<?php

namespace Hippy\Connector\Transformer;

use Hippy\Connector\Exception\InvalidResponseContentException;
use Hippy\Error\Error;
use Hippy\Error\ErrorCollection;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;

abstract class AbstractResponseTransformer implements ResponseTransformerInterface
{
    /**
     * @param ResponseInterface $response
     * @return array<string, mixed>
     * @throws InvalidResponseContentException
     */
    protected function getContent(ResponseInterface $response): array
    {
        $body = $response->getBody();

        $decoder = new JsonDecode();
        $content = $decoder->decode($body, 'json', ['json_decode_associative' => true]);
        if (!is_array($content)) {
            throw new InvalidResponseContentException($body);
        }
        return $content;
    }

    /**
     * @param array<string, mixed> $content
     * @return bool
     */
    protected function getStatus(array $content): bool
    {
        return array_key_exists('status', $content)
            && is_array($content['status'])
            && array_key_exists('success', $content['status'])
            && $content['status']['success'];
    }

    /**
     * @param array<string, mixed> $content
     * @return ErrorCollection
     */
    protected function getErrors(array $content): ErrorCollection
    {
        $errors = new ErrorCollection();
        if (
            array_key_exists('status', $content)
            && is_array($content['status'])
            && array_key_exists('errors', $content['status'])
            && is_array($content['status']['errors'])
        ) {
            foreach ($content['status']['errors'] as $error) {
                $errors->add(new Error($error['code'], $error['message']));
            }
        }
        return $errors;
    }
}
