<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Traits;

/**
 * Trait for DTOs that support tags
 *
 * Expects the DTO to have:
 * - public readonly ?array $tags (or public array $tags with default [])
 */
trait TaggableTrait
{
    /**
     * Get tags array for toArray() method
     *
     * @return array<string, mixed>
     */
    protected function getTagsArray(): array
    {
        $data = [];

        if (isset($this->tags) && !empty($this->tags)) {
            $data['tags'] = $this->tags;
        }

        return $data;
    }
}
