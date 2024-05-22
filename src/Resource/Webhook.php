<?php

namespace Zone\Wildduck\Resource;

use Zone\Wildduck\ApiOperations\All;
use Zone\Wildduck\ApiOperations\Create;
use Zone\Wildduck\ApiOperations\Delete;

/**
 * @property string $id
 * @property string[] $type
 * @property string $user User ID or null
 * @property string $url
 */
class Webhook extends ApiResource
{
    /**
     * @deprecated
     */
	use All;
	use Create;
	use Delete;

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
}
