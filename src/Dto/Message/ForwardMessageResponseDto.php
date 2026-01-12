<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for forward message operation
 */
readonly class ForwardMessageResponseDto implements ResponseDtoInterface
{
    /**
     * @param array<mixed> $forwarded
     */
    public function __construct(
        public bool $success,
        public ?string $queueId = null,
        /** @var string[]|null */ public ?array $forwarded = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }

        return new self(
            success: $data['success'],
            queueId: $data['queueId'] ?? null,
            forwarded: $data['forwarded'] ?? null,
        );
    }
}
