<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Certs;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Request DTO for creating or updating a TLS certificate
 */
readonly class CreateOrUpdateCertificateRequestDto implements RequestDtoInterface
{
    /**
     * @param string[] $ca
     */
    public function __construct(
        public string $servername,
        public ?string $privateKey = null,
        public ?string $cert = null,
        public ?array $ca = null,
        public ?string $description = null,
        public ?bool $acme = null,
    ) {
        if (!$this->acme) {
            if (!$this->privateKey) {
                throw new DtoValidationException('privateKey is required when acme is not set or false', 'string', $this->privateKey);
            }
            if (!$this->cert) {
                throw new DtoValidationException('cert is required when acme is not set or false', 'string', $this->cert);
            }
        }
    }

    public function toArray(): array
    {
        $data = [
            'servername' => $this->servername,
            'privateKey' => $this->privateKey,
            'cert' => $this->cert,
        ];

        if ($this->ca !== null) {
            $data['ca'] = $this->ca;
        }

        return $data;
    }
}
