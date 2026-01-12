<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Dkim;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for DKIM key information
 */
readonly class DkimListResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $id,
        public string $domain,
        public string $selector,
        public string $description,
        public string $fingerprint,
        public string $created,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }
        if (!isset($data['domain'])) {
            throw DtoValidationException::missingRequiredField('domain', 'string');
        }
        if (!isset($data['selector'])) {
            throw DtoValidationException::missingRequiredField('selector', 'string');
        }
        if (!isset($data['description'])) {
            throw DtoValidationException::missingRequiredField('description', 'string');
        }
        if (!isset($data['fingerprint'])) {
            throw DtoValidationException::missingRequiredField('fingerprint', 'string');
        }
        if (!isset($data['created'])) {
            throw DtoValidationException::missingRequiredField('created', 'string');
        }

        return new self(
            id: $data['id'],
            domain: $data['domain'],
            selector: $data['selector'],
            description: $data['description'],
            fingerprint: $data['fingerprint'],
            created: $data['created'],
        );
    }
}
