<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Filter action DTO defining what to do with matched messages
 */
final class FilterActionRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly ?bool $seen = null,
        public readonly ?bool $flag = null,
        public readonly ?bool $delete = null,
        public readonly ?bool $spam = null,
        public readonly ?string $mailbox = null,
        /** @var string[]|null */
        public readonly ?array $targets = null,
    ) {
    }

    public static function fromArray(array $data): static
    {
        $targets = null;
        if (isset($data['targets'])) {
            if (!is_array($data['targets'])) {
                throw DtoValidationException::invalidType('targets', 'array', $data['targets']);
            }
            $targets = array_map(function ($target) {
                if (!is_string($target)) {
                    throw DtoValidationException::invalidType('targets[]', 'string', $target);
                }
                return $target;
            }, $data['targets']);
        }

        return new self(
            seen: isset($data['seen']) && is_bool($data['seen']) ? $data['seen'] : null,
            flag: isset($data['flag']) && is_bool($data['flag']) ? $data['flag'] : null,
            delete: isset($data['delete']) && is_bool($data['delete']) ? $data['delete'] : null,
            spam: isset($data['spam']) && is_bool($data['spam']) ? $data['spam'] : null,
            mailbox: isset($data['mailbox']) && is_string($data['mailbox']) ? $data['mailbox'] : null,
            targets: $targets,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'seen' => $this->seen,
            'flag' => $this->flag,
            'delete' => $this->delete,
            'spam' => $this->spam,
            'mailbox' => $this->mailbox,
            'targets' => $this->targets,
        ], fn($value) => $value !== null);
    }
}
