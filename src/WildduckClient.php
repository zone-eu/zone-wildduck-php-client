<?php

namespace Zone\Wildduck;

/**
 * Client used to send requests to Wildduck's API.
 *
 * @property \Zone\Wildduck\Service\AddressService $addresses
 * @property \Zone\Wildduck\Service\ApplicationPasswordService $applicationPasswords
 * @property \Zone\Wildduck\Service\ArchiveService $archives
 * @property \Zone\Wildduck\Service\AuditService $audits
 * @property \Zone\Wildduck\Service\AuthenticationService $authentication
 * @property \Zone\Wildduck\Service\AutoreplyService $autoreplies
 * @property \Zone\Wildduck\Service\DkimService $dkim
 * @property \Zone\Wildduck\Service\DomainAliasService $domainAliases
 * @property \Zone\Wildduck\Service\EventService $events
 * @property \Zone\Wildduck\Service\FilterService $filters
 * @property \Zone\Wildduck\Service\MailboxService $mailboxes
 * @property \Zone\Wildduck\Service\MessageService $messages
 * @property \Zone\Wildduck\Service\StorageService $storage
 * @property \Zone\Wildduck\Service\SubmissionService $submission
 * @property \Zone\Wildduck\Service\TwoFactorAuthenticationService $twoFactor
 * @property \Zone\Wildduck\Service\UserService $users
 */
class WildduckClient extends BaseWildduckClient
{
    /**
     * @var \Zone\Wildduck\Service\CoreServiceFactory
     */
    private $coreServiceFactory;

    public function __get($name)
    {
        if (null === $this->coreServiceFactory) {
            $this->coreServiceFactory = new \Zone\Wildduck\Service\CoreServiceFactory($this);
        }

        return $this->coreServiceFactory->__get($name);
    }
}
