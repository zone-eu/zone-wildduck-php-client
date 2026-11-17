<?php

declare(strict_types=1);

namespace Tests\Unit\ApiRequestor;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Zone\Wildduck\ApiRequestor;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\MasterTokenNotEligibleException;
use Zone\Wildduck\Exception\MasterTokenRequiredException;
use Zone\Wildduck\Exception\MfaRequiredException;
use Zone\Wildduck\Exception\OverQuotaException;
use Zone\Wildduck\Exception\PasswordChangeRequiredException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ScopedTokenNotFoundException;
use Zone\Wildduck\Exception\UnexpectedValueException;
use Zone\Wildduck\Exception\UnsupportedAuthScopeException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Exception\WildduckException;

use function json_encode;

class HttpStatusPropagationTest extends TestCase
{
    private function makeRequestor(): ApiRequestor
    {
        return new ApiRequestor('test-token', 'http://localhost:9080');
    }

    #[DataProvider('mappedErrorProvider')]
    public function testMappedErrorsCarryTheWildduckHttpStatus(
        string $code,
        int $httpCode,
        string $exceptionClass
    ): void {
        $resp = ['error' => 'Stubbed error', 'code' => $code];

        try {
            $this->makeRequestor()->handleErrorResponse((string) json_encode($resp), $httpCode, $resp);
            $this->fail(
                sprintf('Expected %s for machine-readable code %s', $exceptionClass, $code)
            );
        } catch (WildduckException $exception) {
            $this->assertInstanceOf(
                $exceptionClass,
                $exception,
                sprintf('Code %s should map to %s', $code, $exceptionClass)
            );
            $this->assertSame($httpCode, $exception->getHttpStatus());
        }
    }

    /**
     * @return array<string, array{string, int, class-string<WildduckException>}>
     */
    public static function mappedErrorProvider(): array
    {
        return [
            'invalid token' => ['InvalidToken', 401, InvalidAccessTokenException::class],
            'auth failed' => ['AuthFailed', 401, AuthenticationFailedException::class],
            'input validation error' => ['InputValidationError', 422, ValidationException::class],
            'invalid database' => ['InternalDatabaseError', 500, InvalidDatabaseException::class],
            'over quota' => ['OverQuotaError', 413, OverQuotaException::class],
            'master token required' => ['MasterTokenRequired', 403, MasterTokenRequiredException::class],
            'unsupported auth scope' => ['UnsupportedAuthScope', 400, UnsupportedAuthScopeException::class],
            'master token not eligible' => ['MasterTokenNotEligible', 403, MasterTokenNotEligibleException::class],
            'password change required' => ['PasswordChangeRequired', 403, PasswordChangeRequiredException::class],
            'mfa required' => ['MfaRequired', 403, MfaRequiredException::class],
            'scoped token not found' => ['ScopedTokenNotFound', 404, ScopedTokenNotFoundException::class],
        ];
    }

    public function testUnknownCodeFallbackCarriesTheWildduckHttpStatus(): void
    {
        $resp = ['error' => 'Something novel happened', 'code' => 'TotallyNewCode'];

        try {
            $this->makeRequestor()->handleErrorResponse((string) json_encode($resp), 500, $resp);
            $this->fail('Expected RequestFailedException for unknown code');
        } catch (RequestFailedException $exception) {
            $this->assertSame('TotallyNewCode', $exception->getErrorCode());
            $this->assertSame(500, $exception->getHttpStatus());
        }
    }

    public function testResponseWithoutErrorOrCodeCarriesTheWildduckHttpStatus(): void
    {
        try {
            $this->makeRequestor()->handleErrorResponse('{"success":false}', 502, []);
            $this->fail('Expected UnexpectedValueException for a response without error or code');
        } catch (UnexpectedValueException $exception) {
            $this->assertSame(502, $exception->getHttpStatus());
        }
    }

    public function testConnectionErrorsHaveNoHttpStatus(): void
    {
        $exception = new ApiConnectionException('Could not connect to Wildduck');

        $this->assertSame(500, $this->resolveHttpStatus($exception));
    }

    public function testExceptionsThrownOutsideWildduckResponsesHaveNoHttpStatus(): void
    {
        $this->assertNull((new RequestFailedException('Local failure'))->getHttpStatus());
        $this->assertNull((new InvalidAccessTokenException('token is invalid'))->getHttpStatus());
    }

    /**
     * Mirrors the transport-layer resolution: the status only counts when
     * the exception actually carries one.
     */
    private function resolveHttpStatus(\Exception $exception): int
    {
        if (
            ($exception instanceof WildduckException || $exception instanceof UnexpectedValueException)
            && $exception->getHttpStatus() !== null
        ) {
            return $exception->getHttpStatus();
        }

        return 500;
    }
}
