<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\User;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Request DTO for canceling user deletion task
 * POST /users/:user/restore
 */
final class CancelUserDeletionRequestDto implements RequestDtoInterface
{
    public function __construct()
    {
    }

    public function toArray(): array
    {
        return [];
    }
}
