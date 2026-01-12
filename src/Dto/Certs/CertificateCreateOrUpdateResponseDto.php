<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Certs;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for certificate operations
 */
readonly class CertificateCreateOrUpdateResponseDto implements ResponseDtoInterface
{
    /**
     * @param string[] $altNames
     */
    public function __construct(
        public bool $success,
        public string $id,
        public string $servername,
        public string $fingerprint,
        public string $expires,
        public array $altNames,
        public bool $acme,
        public ?string $description = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }
        if (!isset($data['servername'])) {
            throw DtoValidationException::missingRequiredField('servername', 'string');
        }
        if (!isset($data['acme'])) {
            throw DtoValidationException::missingRequiredField('acme', 'bool');
        }
        // if (!isset($data['description'])) {
        //     throw DtoValidationException::missingRequiredField('description', 'string');
        // }
        if (!isset($data['fingerprint'])) {
            throw DtoValidationException::missingRequiredField('fingerprint', 'string');
        }
        if (!isset($data['expires'])) {
            throw DtoValidationException::missingRequiredField('expires', 'string');
        }
        if (!isset($data['altNames'])) {
            throw DtoValidationException::missingRequiredField('altNames', 'string');
        }

        return new self(
            success: $data['success'],
            id: $data['id'],
            servername: $data['servername'],
            acme: $data['acme'],
            description: $data['description'] ?? null,
            fingerprint: $data['fingerprint'],
            expires: $data['expires'],
            altNames: $data['altNames'],
        );
    }
}
