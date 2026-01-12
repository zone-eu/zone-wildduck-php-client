<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Traits;

/**
 * Trait for DTOs that support encryption settings
 *
 * Expects the DTO to have:
 * - public readonly ?bool $encryptMessages
 * - public readonly ?bool $encryptForwarded
 */
trait EncryptionSupportTrait
{
    /**
     * Get encryption array for toArray() method
     *
     * @return array<string, mixed>
     */
    protected function getEncryptionArray(): array
    {
        $data = [];

        if (isset($this->encryptMessages)) {
            $data['encryptMessages'] = $this->encryptMessages;
        }

        if (isset($this->encryptForwarded)) {
            $data['encryptForwarded'] = $this->encryptForwarded;
        }

        return $data;
    }
}
