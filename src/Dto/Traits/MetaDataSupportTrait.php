<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Traits;

/**
 * Trait for DTOs that support metadata fields
 *
 * Expects the DTO to have:
 * - public readonly ?array $metaData
 * - public readonly ?array $internalData
 */
trait MetaDataSupportTrait
{
    /**
     * Get metadata array for toArray() method
     *
     * @return array<string, mixed>
     */
    protected function getMetaDataArray(): array
    {
        $data = [];

        if (isset($this->metaData)) {
            $data['metaData'] = $this->metaData;
        }

        if (isset($this->internalData)) {
            $data['internalData'] = $this->internalData;
        }

        return $data;
    }
}
