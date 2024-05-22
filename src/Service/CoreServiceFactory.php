<?php

namespace Zone\Wildduck\Service;

use Override;

/**
 * Service factory class for API resources in the root namespace.
 *
 * @property AddressService $addresses
 * @property ApplicationPasswordService $applicationPasswords
 * @property ArchiveService $archives
 * @property AuditService $audits
 * @property AuthenticationService $authentication
 * @property AutoreplyService $autoreplies
 * @property DkimService $dkim
 * @property DomainAliasService $domainAliases
 * @property EventService $events
 * @property FilterService $filters
 * @property MailboxService $mailboxes
 * @property MessageService $messages
 * @property StorageService $storage
 * @property SubmissionService $submission
 * @property TwoFactorAuthenticationService $twoFactor
 * @property UserService $users
 * @property WebhookService $webhooks
 */

class CoreServiceFactory extends AbstractServiceFactory
{
    /**
     * @var array<string, string>
     */
    private static array $classMap = [
        'addresses' => AddressService::class,
        'applicationPasswords' => ApplicationPasswordService::class,
        'archives' => ArchiveService::class,
        'audits' => AuditService::class,
        'authentication' => AuthenticationService::class,
        'autoreplies' => AutoreplyService::class,
        'dkim' => DkimService::class,
        'domainAliases' => DomainAliasService::class,
        'events' => EventService::class,
        'filters' => FilterService::class,
        'mailboxes' => MailboxService::class,
        'messages' => MessageService::class,
        'storage' => StorageService::class,
        'submission' => SubmissionService::class,
        'twoFactor' => TwoFactorAuthenticationService::class,
        'users' => UserService::class,
        'webhooks' => WebhookService::class,
    ];

    #[Override]
    protected function getServiceClass($name): string|null
    {
        return self::$classMap[$name] ?? null;
    }
}
