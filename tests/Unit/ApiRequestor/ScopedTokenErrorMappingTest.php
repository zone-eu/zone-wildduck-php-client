<?php

declare(strict_types=1);

namespace Tests\Unit\ApiRequestor;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Zone\Wildduck\ApiRequestor;
use Zone\Wildduck\Exception\MasterTokenNotEligibleException;
use Zone\Wildduck\Exception\MasterTokenRequiredException;
use Zone\Wildduck\Exception\ScopedTokenNotFoundException;
use Zone\Wildduck\Exception\MfaRequiredException;
use Zone\Wildduck\Exception\PasswordChangeRequiredException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\UnsupportedAuthScopeException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Exception\WildduckException;

use function json_encode;

class ScopedTokenErrorMappingTest extends TestCase
{
    private function makeRequestor(): ApiRequestor
    {
        return new ApiRequestor('test-token', 'http://localhost:9080');
    }

    #[DataProvider('errorCodesProvider')]
    public function testErrorCodesMapToTypedExceptions(
        string $code,
        string $error,
        int $httpCode,
        string $exceptionClass
    ): void {
        $resp = ['error' => $error, 'code' => $code];

        try {
            $this->makeRequestor()->handleErrorResponse((string)json_encode($resp), $httpCode, $resp);
            $this->fail(
                sprintf('Expected %s for machine-readable code %s', $exceptionClass, $code)
            );
        } catch (WildduckException $exception) {
            $this->assertInstanceOf(
                $exceptionClass,
                $exception,
                sprintf('Code %s should map to %s', $code, $exceptionClass)
            );
            $this->assertSame($error, $exception->getMessage());
            $this->assertSame($httpCode, $exception->getCode());
            $this->assertSame($code, $exception->getErrorCode());
        }
    }

    /**
     * @return array<string, array{string, string, int, class-string<WildduckException>}>
     */
    public static function errorCodesProvider(): array
    {
        return [
            'master token required' => [
                'MasterTokenRequired',
                'A master API token is required',
                403,
                MasterTokenRequiredException::class,
            ],
            'unsupported scope' => [
                'UnsupportedAuthScope',
                'Unsupported authentication scope',
                400,
                UnsupportedAuthScopeException::class,
            ],
            'master token not eligible' => [
                'MasterTokenNotEligible',
                'The master API token is no longer eligible to create scoped tokens',
                403,
                MasterTokenNotEligibleException::class,
            ],
            'password change required' => [
                'PasswordChangeRequired',
                'The temporary password must be replaced before creating a scoped token',
                403,
                PasswordChangeRequiredException::class,
            ],
            'mfa required' => [
                'MfaRequired',
                'Multi-factor authentication must be completed first',
                403,
                MfaRequiredException::class,
            ],
            'scoped token not found' => [
                'ScopedTokenNotFound',
                'This scoped token does not exist',
                404,
                ScopedTokenNotFoundException::class,
            ],
            'legacy mcp token not found' => [
                'McpTokenNotFound',
                'This MCP token does not exist',
                404,
                ScopedTokenNotFoundException::class,
            ],
        ];
    }

    public function testInputValidationErrorStillMapsToValidationException(): void
    {
        $resp = ['error' => "scope must be shorter than or equal to 64 characters", 'code' => 'InputValidationError'];

        $this->expectException(ValidationException::class);

        $this->makeRequestor()->handleErrorResponse((string)json_encode($resp), 400, $resp);
    }

    public function testUnknownCodeStillFallsBackToRequestFailedException(): void
    {
        $resp = ['error' => 'Something novel happened', 'code' => 'TotallyNewCode'];

        try {
            $this->makeRequestor()->handleErrorResponse((string)json_encode($resp), 418, $resp);
            $this->fail('Expected RequestFailedException for unknown code');
        } catch (RequestFailedException $exception) {
            $this->assertSame('TotallyNewCode', $exception->getErrorCode());
        }
    }
}
