<?php

declare(strict_types=1);

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\PaginatedResultDto;
use Zone\Wildduck\Dto\Shared\SuccessResponseDto;
use Zone\Wildduck\Dto\Webhook\CreateWebhookRequestDto;
use Zone\Wildduck\Dto\Webhook\CreateWebhookResponseDto;
use Zone\Wildduck\Dto\Webhook\ListWebhooksRequestDto;
use Zone\Wildduck\Dto\Webhook\WebhookResponseDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Service\Traits\RequiresGlobalToken;

/**
 * Webhook service for managing webhooks
 */
class WebhookService extends AbstractService
{
    use RequiresGlobalToken;

    /**
     * List registered webhooks
     *
     * @param ListWebhooksRequestDto|null $params Query parameters for filtering webhooks
     * @param array|null $opts
     * @return PaginatedResultDto<WebhookResponseDto>
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function all(?ListWebhooksRequestDto $params = null, array|null $opts = null): PaginatedResultDto
    {
        return $this->requestPaginatedDto('get', '/webhooks', $params, WebhookResponseDto::class, $opts);
    }

    /**
     * Create a new webhook
     *
     * @param CreateWebhookRequestDto $params
     * @param array|null $opts
     * @return CreateWebhookResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function create(CreateWebhookRequestDto $params, array|null $opts = null): CreateWebhookResponseDto
    {
        return $this->requestDto('post', '/webhooks', $params, CreateWebhookResponseDto::class, $opts);
    }

    /**
     * Delete a webhook
     *
     * @param string $id
     * @param array|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function delete(string $id, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('delete', $this->buildPath('/webhooks/%s', $id), null, SuccessResponseDto::class, $opts);
    }
}
