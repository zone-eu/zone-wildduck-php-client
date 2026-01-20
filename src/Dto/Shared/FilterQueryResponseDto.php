<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * Filter query DTO defining what messages to match
 */
final class FilterQueryResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public readonly ?string $from = null,
        public readonly ?string $to = null,
        public readonly ?string $subject = null,
        public readonly ?string $listId = null,
        public readonly ?string $text = null,
        public readonly ?bool $ha = null,
        public readonly ?int $size = null,
    ) {}

    /**
     * @param array<int, array{ 0: string|false, 1: string|int|unset }> $data
     */
    public static function fromArray(array $data): self
    {
        $from = null;
        $to = null;
        $subject = null;
        $listId = null;
        $ha = false;
        $text = null;
        $size = null;

        foreach ($data as $query) {
            $key = $query[0];
            $value = $query[1] ?? null;
            switch ($key) {
                case 'from':
                    $from = trim($value, '()');
                    break;
                case 'to':
                    $to = trim($value, '()');
                    break;
                case 'subject':
                    $subject = trim($value, '()');
                    break;
                case 'listId':
                    $listId = trim($value, '()');
                    break;
                case 'has attachment':
                    $ha = true;
                    break;
                case false:
                    $text = trim($value, '"');
                    break;
                case 'larger':
                    $size = $value;
                    break;
                case 'smaller':
                    $size = $value;
                    break;

                default:
                    throw new \InvalidArgumentException("Unknown filter action key: $key");
                    break;
            }
        }

        return new self(
            from: $from,
            to: $to,
            subject: $subject,
            listId: $listId,
            text: $text,
            ha: $ha,
            size: $size,
        );
    }
}
