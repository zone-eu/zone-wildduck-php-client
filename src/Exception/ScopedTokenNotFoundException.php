<?php

declare(strict_types=1);

namespace Zone\Wildduck\Exception;

/**
 * Thrown when revoking a scoped token whose identifier is unknown or was already revoked.
 *
 * WildDuck machine-readable error code: ScopedTokenNotFound
 */
class ScopedTokenNotFoundException extends WildduckException
{
    public const string ERROR_CODE = 'ScopedTokenNotFound';

    /**
     * Legacy code returned by the MCP-specific revocation route
     * (DELETE /authenticate/mcp/:token) until it is replaced by the
     * symmetric DELETE /authenticate/:scope/:token route
     */
    public const string LEGACY_ERROR_CODE = 'McpTokenNotFound';

    private readonly string $errorCode;

    /**
     * @param string $message
     * @param string $errorCode Machine-readable code returned by WildDuck
     */
    public function __construct(string $message = '', string $errorCode = self::ERROR_CODE)
    {
        if ($message === '') {
            $message = 'This scoped token does not exist';
        }

        $this->errorCode = $errorCode;

        parent::__construct($message, 404);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}
