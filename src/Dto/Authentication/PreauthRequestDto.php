<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Authentication;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for pre-authentication check
 */
class PreauthRequestDto implements RequestDtoInterface
{
    /**
     * @param 'master'|'imap'|'smtp'|'pop3' $scope
     */
    public function __construct(
        public string $username,
        public ?string $scope = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'username' => $this->username,
        ];

        if ($this->scope !== null) {
            $data['scope'] = $this->scope;
        }

        return $data;
    }
}
