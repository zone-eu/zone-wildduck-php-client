<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * Filter action DTO defining what to do with matched messages
 */
final class FilterActionResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public readonly ?bool $seen = null,
        public readonly ?bool $flag = null,
        public readonly ?bool $delete = null,
        public readonly ?bool $spam = null,
        public readonly ?string $mailbox = null,
        /** @var string[]|null */
        public readonly ?array $targets = null,
    ) {}

    /**
     * @param array<int, array{ 0: string, 1: string|false }> $data
     */
    public static function fromArray(array $data): self
    {
        $seen = null;
        $flag = null;
        $delete = null;
        $spam = null;
        $mailbox = null;
        $targets = null;

        foreach ($data as [$key, $value]) {
            switch ($key) {
                case 'forward to':
                    $targets = is_string($value) ? array_map('trim', explode(',', $value)) : null;
                    break;

                default:
                    # code...
                    break;
            }
        }


        return new self(
            seen: $seen,
            flag: $flag,
            delete: $delete,
            spam: $spam,
            mailbox: $mailbox,
            targets: $targets,
        );
    }
}
