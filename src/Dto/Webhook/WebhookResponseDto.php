<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Webhook;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for webhook information
 */
readonly class WebhookResponseDto implements ResponseDtoInterface
{
    /**
     * @param string[] $type
     */
    public function __construct(
        public string $id,
        /** @var string[] */ public array $type,
        public string $url,
        public ?string $user = null,
        public ?string $created = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }
        if (!isset($data['type'])) {
            throw DtoValidationException::missingRequiredField('type', 'array');
        }
        if (!isset($data['url'])) {
            throw DtoValidationException::missingRequiredField('url', 'string');
        }

        return new self(
            id: $data['id'],
            type: $data['type'],
            url: $data['url'],
            user: $data['user'] ?? null,
            created: $data['created'] ?? null,
        );
    }
}
