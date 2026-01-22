<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Storage;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Request DTO for uploading a file
 */
class UploadFileRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $content,
        public string $filename,
        public string $contentType,
        public ?string $encoding = null,
        public ?string $cid = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'content' => $this->content,
            'filename' => $this->filename,
            'contentType' => $this->contentType,
        ];

        if ($this->encoding !== null) {
            $data['encoding'] = $this->encoding;
        }
        if ($this->cid !== null) {
            $data['cid'] = $this->cid;
        }

        return $data;
    }
}
