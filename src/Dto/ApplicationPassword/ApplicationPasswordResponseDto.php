<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\ApplicationPassword;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Dto\Shared\ApplicationPasswordLastUseResponseDto;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Application Password DTO
 */
readonly class ApplicationPasswordResponseDto implements ResponseDtoInterface
{
    /**
     * @param string[] $scopes
     */
    public function __construct(
        public string $id,
        public string $description,
        public array $scopes,
        public ?ApplicationPasswordLastUseResponseDto $lastUse = null,
        public ?string $created = null,
        public ?string $expires = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }
        if (!isset($data['description'])) {
            throw DtoValidationException::missingRequiredField('description', 'string');
        }
        if (!isset($data['scopes'])) {
            throw DtoValidationException::missingRequiredField('scopes', 'array');
        }

        if (!is_string($data['id'])) {
            throw DtoValidationException::invalidType('id', 'string', $data['id']);
        }
        if (!is_string($data['description'])) {
            throw DtoValidationException::invalidType('description', 'string', $data['description']);
        }
        if (!is_array($data['scopes'])) {
            throw DtoValidationException::invalidType('scopes', 'array', $data['scopes']);
        }

        $lastUse = null;
        if (isset($data['lastUse']) && is_array($data['lastUse'])) {
            $lastUse = ApplicationPasswordLastUseResponseDto::fromArray($data['lastUse']);
        }

        return new self(
            id: $data['id'],
            description: $data['description'],
            scopes: $data['scopes'],
            lastUse: $lastUse,
            created: isset($data['created']) && is_string($data['created']) ? $data['created'] : null,
            expires: isset($data['expires']) && is_string($data['expires']) ? $data['expires'] : null,
        );
    }
}
