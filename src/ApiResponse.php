<?php

namespace Zone\Wildduck;

use Zone\Wildduck\Util\CaseInsensitiveArray;

/**
 * Class ApiResponse.
 */
class ApiResponse
{
    /**
     * @var null|array|CaseInsensitiveArray
     */
    public CaseInsensitiveArray|array|null $headers;

    /**
     * @var string
     */
    public string $body;

    /**
     * @var array|null
     */
    public array|null $json;

    /**
     * @var int
     */
    public int $code;

    /**
     * @param string $body
     * @param int $code
     * @param array|null|CaseInsensitiveArray $headers
     * @param array|null $json
     */
    public function __construct(string $body, int $code, array|null|CaseInsensitiveArray $headers, array|null $json)
    {
        $this->body = $body;
        $this->code = $code;
        $this->headers = $headers;
        $this->json = $json;
    }
}
