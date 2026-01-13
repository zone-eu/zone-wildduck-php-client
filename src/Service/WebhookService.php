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

    public const string OBJECT_NAME = 'webhooks';

    public const string DKIM_CREATED = 'dkim.created';

    public const string DKIM_UPDATED = 'dkim.updated';

    public const string DKIM_DELETED = 'dkim.deleted';

    public const string CERT_CREATED = 'cert.created';

    public const string CERT_UPDATED = 'cert.updated';

    public const string CERT_DELETED = 'cert.deleted';

    public const string DOMAINALIAS_CREATED = 'domainalias.created';

    public const string DOMAINALIAS_DELETED = 'domainalias.deleted';

    public const string ADDRESS_USER_CREATED = 'address.user.created';

    public const string ADDRESS_USER_DELETED = 'address.user.deleted';

    public const string ADDRESS_FORWARDED_CREATED = 'address.forwarded.created';

    public const string ADDRESS_FORWARDED_DELETED = 'address.forwarded.deleted';

    public const string ADDRESS_DOMAIN_RENAMED = 'address.domain.renamed';

    public const string FILTER_DELETED = 'filter.deleted';

    public const string FILTER_CREATED = 'filter.created';

    public const string ASP_CREATED = 'asp.created';

    public const string ASP_DELETED = 'asp.deleted';

    public const string USER_CREATED = 'user.created';

    public const string USER_PASSWORD_CHANGED = 'user.password.changed';

    public const string USER_DELETE_STARTED = 'user.delete.started';

    public const string USER_DELETE_COMPLETED = 'user.delete.completed';

    public const string USER_DELETE_CANCELLED = 'user.delete.cancelled';

    public const string AUTOREPLY_USER_ENABLED = 'autoreply.user.enabled';

    public const string AUTOREPLY_USER_DISABLED = 'autoreply.user.disabled';

    public const string MFA_TOTP_ENABLED = 'mfa.totp.enabled';

    public const string MFA_TOTP_DISABLED = 'mfa.totp.disabled';

    public const string MFA_CUSTOM_ENABLED = 'mfa.custom.enabled';

    public const string MFA_CUSTOM_DISABLED = 'mfa.custom.disabled';

    public const string MFA_U2F_ENABLED = 'mfa.u2f.enabled';

    public const string MFA_U2F_DISABLED = 'mfa.u2f.disabled';

    public const string MFA_DISABLED = 'mfa.disabled';

    public const string MAILBOX_CREATED = 'mailbox.created';

    public const string MAILBOX_RENAMED = 'mailbox.renamed';

    public const string MAILBOX_DELETED = 'mailbox.deleted';

    public const string MARKED_SPAM = 'marked.spam';

    public const string MARKED_HAM = 'marked.ham';
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
