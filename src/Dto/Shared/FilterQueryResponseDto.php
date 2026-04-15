<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * Filter query DTO defining what messages to match
 * @phpstan-type FilterQuerySize array{size: int, type: 'larger'|'smaller'}|null
 */
final class FilterQueryResponseDto implements ResponseDtoInterface
{
    /**
     * @param FilterQuerySize $size Message size
     */
    public function __construct(
        public readonly ?string $from = null,
        public readonly ?string $to = null,
        public readonly ?string $subject = null,
        public readonly ?string $listId = null,
        public readonly ?string $text = null,
        public readonly ?bool $ha = null,
        public readonly ?array $size = null,
    ) {}

    /**
     * @param array<int, array{ 0: string, 1: string|int|null }> $data
     */
    public static function fromArray(array $data): self
    {
        $from = null;
        $to = null;
        $subject = null;
        $listId = null;
        $ha = false;
        $text = null;
        /** @var FilterQuerySize $size*/
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
                case 'text':
                    $text = $value;
                    break;
                case 'larger':
                    $size = ['size' => (int)$value, 'type' => 'larger'];
                    break;
                case 'smaller':
                    $size = ['size' => (int)$value, 'type' => 'smaller'];
                    break;

                default:
                    throw new \InvalidArgumentException("Unknown filter query key: $key");
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

    /**
     * @param array{ from: string|null, to: string|null, subject: string|null, listId: string|null, text: string|null, ha: bool|null, size: int|null } $data
     */
    public static function fromObject(array $data): self
    {
        return new self(
            from: isset($data['from']) ? trim($data['from'], '()') : null,
            to: isset($data['to']) ? trim($data['to'], '()') : null,
            subject: isset($data['subject']) ? trim($data['subject'], '()') : null,
            listId: isset($data['listId']) ? trim($data['listId'], '()') : null,
            text: $data['text'] ?? null,
            ha: $data['ha'] ?? false,
            size: isset($data['size']) ? ['size' => (int)$data['size'], 'type' => (int)$data['size'] > 0 ? 'larger' : 'smaller'] : null,
        );
    }
}
