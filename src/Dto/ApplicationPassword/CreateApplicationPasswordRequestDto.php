<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\ApplicationPassword;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * DTO for creating an application password
 */
readonly class CreateApplicationPasswordRequestDto implements RequestDtoInterface
{
    /**
     * @param ('imap'|'pop3'|'smtp'|'*')[] $scopes
     */
    public function __construct(
        public string $description,
        public array $scopes = [],
        public ?string $address = null,
        public ?string $password = null,
        public ?bool $generateMobileconfig = null,
        public ?int $ttl = null,
        public ?string $protocol = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'description' => $this->description,
        ];

        if (!empty($this->scopes)) {
            $data['scopes'] = $this->scopes;
        }
        if ($this->address !== null) {
            $data['address'] = $this->address;
        }
        if ($this->password !== null) {
            $data['password'] = $this->password;
        }
        if ($this->generateMobileconfig !== null) {
            $data['generateMobileconfig'] = $this->generateMobileconfig;
        }
        if ($this->ttl !== null) {
            $data['ttl'] = $this->ttl;
        }
        if ($this->protocol !== null) {
            $data['protocol'] = $this->protocol;
        }

        return $data;
    }
}
