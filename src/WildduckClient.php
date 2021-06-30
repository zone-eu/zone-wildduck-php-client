<?php

namespace Zone\Wildduck;

use Zone\Wildduck\Service\AddressService;
use Zone\Wildduck\Service\ApplicationPasswordService;
use Zone\Wildduck\Service\ArchiveService;
use Zone\Wildduck\Service\AuditService;
use Zone\Wildduck\Service\AuthenticationService;
use Zone\Wildduck\Service\AutoreplyService;
use Zone\Wildduck\Service\CoreServiceFactory;
use Zone\Wildduck\Service\DkimService;
use Zone\Wildduck\Service\DomainAliasService;
use Zone\Wildduck\Service\EventService;
use Zone\Wildduck\Service\FilterService;
use Zone\Wildduck\Service\MailboxService;
use Zone\Wildduck\Service\MessageService;
use Zone\Wildduck\Service\StorageService;
use Zone\Wildduck\Service\SubmissionService;
use Zone\Wildduck\Service\TwoFactorAuthenticationService;
use Zone\Wildduck\Service\UserService;

/**
 * Client used to send requests to Wildduck's API.
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
 */
class WildduckClient extends BaseWildduckClient
{
    /**
     * @var CoreServiceFactory
     */
    private CoreServiceFactory $coreServiceFactory;

    public function __get($name)
    {
        if (null === $this->coreServiceFactory) {
            $this->coreServiceFactory = new CoreServiceFactory($this);
        }

        return $this->coreServiceFactory->__get($name);
    }
}
