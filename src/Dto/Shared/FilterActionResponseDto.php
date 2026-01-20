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
        public readonly bool $seen,
        public readonly bool $flag,
        public readonly bool $delete,
        public readonly ?bool $spam = null,
        public readonly ?string $mailbox = null,
        /** @var string[]|null */
        public readonly ?array $targets = null,
    ) {}

    /**
     * @param array<int, array{ 0: string, 1: string|bool|unset }> $data
     */
    public static function fromArray(array $data): self
    {
        $seen = false;
        $flag = false;
        $delete = false;
        $spam = null;
        $mailbox = null;
        $targets = null;

        foreach ($data as $action) {
            $key = $action[0];
            $value = $action[1] ?? null;
            switch ($key) {
                case 'mark as read':
                    $seen = true;
                    break;
                case 'flag it':
                    $flag = true;
                    break;
                case 'delete it':
                    $delete = true;
                    break;
                case 'forward to':
                    $targets = is_string($value) ? array_map('trim', explode(',', $value)) : null;
                    break;
                case 'move to folder':
                    $mailbox = trim($value, '"');
                    break;
                case 'mark it as spam':
                    $spam = true;
                    break;
                case 'do not mark it as spam':
                    $spam = false;
                    break;

                default:
                    throw new \InvalidArgumentException("Unknown filter action key: $key");
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
