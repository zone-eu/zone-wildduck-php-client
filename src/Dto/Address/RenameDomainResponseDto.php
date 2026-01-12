<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Address;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for domain rename operation
 */
readonly class RenameDomainResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public int $modifiedAddresses,
        public int $modifiedUsers,
        public int $modifiedDkim,
        public int $modifiedAliases,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['modifiedAddresses'])) {
            throw DtoValidationException::missingRequiredField('modifiedAddresses', 'int');
        }
        if (!isset($data['modifiedUsers'])) {
            throw DtoValidationException::missingRequiredField('modifiedUsers', 'int');
        }
        if (!isset($data['modifiedDkim'])) {
            throw DtoValidationException::missingRequiredField('modifiedDkim', 'int');
        }
        if (!isset($data['modifiedAliases'])) {
            throw DtoValidationException::missingRequiredField('modifiedAliases', 'int');
        }

        if (!is_int($data['modifiedAddresses'])) {
            throw DtoValidationException::invalidType('modifiedAddresses', 'int', $data['modifiedAddresses']);
        }
        if (!is_int($data['modifiedUsers'])) {
            throw DtoValidationException::invalidType('modifiedUsers', 'int', $data['modifiedUsers']);
        }
        if (!is_int($data['modifiedDkim'])) {
            throw DtoValidationException::invalidType('modifiedDkim', 'int', $data['modifiedDkim']);
        }
        if (!is_int($data['modifiedAliases'])) {
            throw DtoValidationException::invalidType('modifiedAliases', 'int', $data['modifiedAliases']);
        }

        return new self(
            modifiedAddresses: $data['modifiedAddresses'],
            modifiedUsers: $data['modifiedUsers'],
            modifiedDkim: $data['modifiedDkim'],
            modifiedAliases: $data['modifiedAliases'],
        );
    }
}
