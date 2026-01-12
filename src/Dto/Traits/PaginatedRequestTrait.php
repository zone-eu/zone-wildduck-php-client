<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Traits;

/**
 * Trait for request DTOs that support pagination
 *
 * Expects the DTO to have:
 * - public readonly ?int $limit
 * - public readonly ?string $next
 * - public readonly ?string $previous
 * - public readonly ?string $order
 */
trait PaginatedRequestTrait
{
    /**
     * Get pagination array for toArray() method
     *
     * @return array<string, mixed>
     */
    protected function getPaginationArray(): array
    {
        $data = [];

        if (isset($this->limit)) {
            $data['limit'] = $this->limit;
        }

        if (isset($this->next)) {
            $data['next'] = $this->next;
        }

        if (isset($this->previous)) {
            $data['previous'] = $this->previous;
        }

        if (isset($this->order)) {
            $data['order'] = $this->order;
        }

        return $data;
    }
}
