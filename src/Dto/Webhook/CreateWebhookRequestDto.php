<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Webhook;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Request DTO for creating a new webhook
 */
readonly class CreateWebhookRequestDto implements RequestDtoInterface
{
    /**
     * @param string[] $type
     */
    public function __construct(
        /** @var string[] */ public array $type,
        public string $url,
        public ?string $user = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'type' => $this->type,
            'url' => $this->url,
            'user' => $this->user,
        ], fn($value) => $value !== null);
    }
}
