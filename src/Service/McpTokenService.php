<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\McpToken\CreateMcpTokenRequestDto;
use Zone\Wildduck\Dto\McpToken\CreateMcpTokenResponseDto;
use Zone\Wildduck\Dto\McpToken\ListMcpTokensResponseDto;
use Zone\Wildduck\Dto\Shared\SuccessResponseDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

class McpTokenService extends AbstractService
{
    /**
     * Create a read-only MCP access token
     *
     * @param string $user
     * @param CreateMcpTokenRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return CreateMcpTokenResponseDto
     *
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function create(string $user, CreateMcpTokenRequestDto $params, array|null $opts = null): CreateMcpTokenResponseDto
    {
        return $this->requestDto('post', $this->buildPath('/users/%s/mcp-tokens', $user), $params, CreateMcpTokenResponseDto::class, $opts);
    }

    /**
     * List the user's MCP access tokens, newest first
     *
     * @param string $user
     * @param array<string, mixed>|null $opts
     * @return ListMcpTokensResponseDto
     *
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function all(string $user, array|null $opts = null): ListMcpTokensResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/users/%s/mcp-tokens', $user), null, ListMcpTokensResponseDto::class, $opts);
    }

    /**
     * Revoke a single MCP access token by its record id
     *
     * @param string $user
     * @param string $tokenId
     * @param array<string, mixed>|null $opts
     * @return SuccessResponseDto
     *
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function delete(string $user, string $tokenId, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('delete', $this->buildPath('/users/%s/mcp-tokens/%s', $user, $tokenId), null, SuccessResponseDto::class, $opts);
    }
}
