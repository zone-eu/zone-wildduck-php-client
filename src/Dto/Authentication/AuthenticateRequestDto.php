<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Authentication;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Request DTO for user authentication
 */
class AuthenticateRequestDto implements RequestDtoInterface
{
    /**
     * @param 'master'|'imap'|'smtp'|'pop3' $scope
     */
    public function __construct(
        public string $username,
        public string $password,
        public string $protocol = 'API',
        public ?string $scope = null,
        public ?string $appId = null,
        public bool $token = false,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'username' => $this->username,
            'password' => $this->password,
            'protocol' => $this->protocol,
            'scope' => $this->scope,
            'appId' => $this->appId,
            'token' => $this->token,
        ], fn($value) => $value !== null);
    }
}
