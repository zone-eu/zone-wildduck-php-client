<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Authentication;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for user authentication
 */
readonly class AuthenticationResponseDto implements ResponseDtoInterface
{
    /**
     * @param string[]|bool $require2fa
     */
    public function __construct(
        public bool $success,
        public string $id,
        public string $username,
        public string $address,
        public string $scope,
        public array|bool $require2fa,
        public bool $requirePasswordChange,
        public ?string $token = null,
        public ?bool $passwordPwned = null,
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
        if (!isset($data['username'])) {
            throw DtoValidationException::missingRequiredField('username', 'string');
        }
        if (!isset($data['address'])) {
            throw DtoValidationException::missingRequiredField('address', 'string');
        }
        if (!isset($data['scope'])) {
            throw DtoValidationException::missingRequiredField('scope', 'string');
        }
        if (!isset($data['require2fa'])) {
            throw DtoValidationException::missingRequiredField('require2fa', 'array|bool');
        }
        if (!isset($data['requirePasswordChange'])) {
            throw DtoValidationException::missingRequiredField('requirePasswordChange', 'bool');
        }

        return new self(
            success: $data['success'],
            id: $data['id'],
            username: $data['username'],
            address: $data['address'],
            scope: $data['scope'],
            require2fa: is_array($data['require2fa']) ? $data['require2fa'] : $data['require2fa'],
            requirePasswordChange: $data['requirePasswordChange'],
            token: $data['token'] ?? null,
            passwordPwned: $data['passwordPwned'] ?? null,
        );
    }
}
