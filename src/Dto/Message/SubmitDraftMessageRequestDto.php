<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for submitting a message
 */
class SubmitDraftMessageRequestDto implements RequestDtoInterface
{
    public function __construct(

    public bool $deleteFiles = false,
    public ?string $sendTime = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'deleteFiles' => $this->deleteFiles,
            'sendTime' => $this->sendTime,
        ]);
    }
}
