<?php

namespace Zone\Wildduck;

// FIXME: Unable to use typed class properties since PHP doesn't understand that they are initialized by WildduckObject __get/__set?

/**
 * @property string $id
 * @property string[] $type
 * @property string $user User ID or null
 * @property string $url
 */
class Webhook extends ApiResource
{
    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Delete;

    const DKIM_CREATED = 'dkim.created';
    const DKIM_UPDATED = 'dkim.updated';
    const DKIM_DELETED = 'dkim.deleted';
    const CERT_CREATED = 'cert.created';
    const CERT_UPDATED = 'cert.updated';
    const CERT_DELETED = 'cert.deleted';
    const DOMAINALIAS_CREATED = 'domainalias.created';
    const DOMAINALIAS_DELETED = 'domainalias.deleted';
    const ADDRESS_USER_CREATED = 'address.user.created';
    const ADDRESS_USER_DELETED = 'address.user.deleted';
    const ADDRESS_FORWARDED_CREATED = 'address.forwarded.created';
    const ADDRESS_FORWARDED_DELETED = 'address.forwarded.deleted';
    const ADDRESS_DOMAIN_RENAMED = 'address.domain.renamed';
    const FILTER_DELETED = 'filter.deleted';
    const FILTER_CREATED = 'filter.created';
    const ASP_CREATED = 'asp.created';
    const ASP_DELETED = 'asp.deleted';
    const USER_CREATED = 'user.created';
    const USER_PASSWORD_CHANGED = 'user.password.changed';
    const USER_DELETE_STARTED = 'user.delete.started';
    const USER_DELETE_COMPLETED = 'user.delete.completed';
    const USER_DELETE_CANCELLED = 'user.delete.cancelled';
    const AUTOREPLY_USER_ENABLED = 'autoreply.user.enabled';
    const AUTOREPLY_USER_DISABLED = 'autoreply.user.disabled';
    const MFA_TOTP_ENABLED = 'mfa.totp.enabled';
    const MFA_TOTP_DISABLED = 'mfa.totp.disabled';
    const MFA_CUSTOM_ENABLED = 'mfa.custom.enabled';
    const MFA_CUSTOM_DISABLED = 'mfa.custom.disabled';
    const MFA_U2F_ENABLED = 'mfa.u2f.enabled';
    const MFA_U2F_DISABLED = 'mfa.u2f.disabled';
    const MFA_DISABLED = 'mfa.disabled';
    const MAILBOX_CREATED = 'mailbox.created';
    const MAILBOX_RENAMED = 'mailbox.renamed';
    const MAILBOX_DELETED = 'mailbox.deleted';
    const MARKED_SPAM = 'marked.spam';
    const MARKED_HAM = 'marked.ham';
}
