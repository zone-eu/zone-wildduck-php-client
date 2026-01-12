<?php

namespace Zone\Wildduck;

use Zone\Wildduck\Service\AddressService;
use Zone\Wildduck\Service\ApplicationPasswordService;
use Zone\Wildduck\Service\ArchiveService;
use Zone\Wildduck\Service\AuditService;
use Zone\Wildduck\Service\AuthenticationLogService;
use Zone\Wildduck\Service\AuthenticationService;
use Zone\Wildduck\Service\AutoreplyService;
use Zone\Wildduck\Service\CertsService;
use Zone\Wildduck\Service\DkimService;
use Zone\Wildduck\Service\DomainAccessService;
use Zone\Wildduck\Service\DomainAliasService;
use Zone\Wildduck\Service\ExportService;
use Zone\Wildduck\Service\FilterService;
use Zone\Wildduck\Service\HealthService;
use Zone\Wildduck\Service\MailboxService;
use Zone\Wildduck\Service\MessageService;
use Zone\Wildduck\Service\SettingsService;
use Zone\Wildduck\Service\StorageService;
use Zone\Wildduck\Service\SubmissionService;
use Zone\Wildduck\Service\TwoFactorAuthenticationService;
use Zone\Wildduck\Service\UserService;
use Zone\Wildduck\Service\WebhookService;

/**
 * Client used to send requests to Wildduck's API.
 */
class WildduckClient extends BaseWildduckClient
{
    private ?AddressService $addressService = null;
    private ?ApplicationPasswordService $applicationPasswordService = null;
    private ?ArchiveService $archiveService = null;
    private ?AuditService $auditService = null;
    private ?AuthenticationLogService $authenticationLogService = null;
    private ?AuthenticationService $authenticationService = null;
    private ?AutoreplyService $autoreplyService = null;
    private ?CertsService $certsService = null;
    private ?DkimService $dkimService = null;
    private ?DomainAccessService $domainAccessService = null;
    private ?DomainAliasService $domainAliasService = null;
    private ?ExportService $exportService = null;
    private ?FilterService $filterService = null;
    private ?HealthService $healthService = null;
    private ?MailboxService $mailboxService = null;
    private ?MessageService $messageService = null;
    private ?SettingsService $settingsService = null;
    private ?StorageService $storageService = null;
    private ?SubmissionService $submissionService = null;
    private ?TwoFactorAuthenticationService $twoFactorAuthenticationService = null;
    private ?UserService $userService = null;
    private ?WebhookService $webhookService = null;

    public function addresses(): AddressService
    {
        return $this->addressService ??= new AddressService($this);
    }

    public function applicationPasswords(): ApplicationPasswordService
    {
        return $this->applicationPasswordService ??= new ApplicationPasswordService($this);
    }

    public function archives(): ArchiveService
    {
        return $this->archiveService ??= new ArchiveService($this);
    }

    public function audits(): AuditService
    {
        return $this->auditService ??= new AuditService($this);
    }

    public function authentication(): AuthenticationService
    {
        return $this->authenticationService ??= new AuthenticationService($this);
    }

    public function authenticationLogs(): AuthenticationLogService
    {
        return $this->authenticationLogService ??= new AuthenticationLogService($this);
    }

    public function autoreplies(): AutoreplyService
    {
        return $this->autoreplyService ??= new AutoreplyService($this);
    }

    public function certs(): CertsService
    {
        return $this->certsService ??= new CertsService($this);
    }

    public function dkim(): DkimService
    {
        return $this->dkimService ??= new DkimService($this);
    }

    public function domainAccess(): DomainAccessService
    {
        return $this->domainAccessService ??= new DomainAccessService($this);
    }

    public function domainAliases(): DomainAliasService
    {
        return $this->domainAliasService ??= new DomainAliasService($this);
    }

    public function export(): ExportService
    {
        return $this->exportService ??= new ExportService($this);
    }

    public function filters(): FilterService
    {
        return $this->filterService ??= new FilterService($this);
    }

    public function health(): HealthService
    {
        return $this->healthService ??= new HealthService($this);
    }

    public function mailboxes(): MailboxService
    {
        return $this->mailboxService ??= new MailboxService($this);
    }

    public function messages(): MessageService
    {
        return $this->messageService ??= new MessageService($this);
    }

    public function settings(): SettingsService
    {
        return $this->settingsService ??= new SettingsService($this);
    }

    public function storage(): StorageService
    {
        return $this->storageService ??= new StorageService($this);
    }

    public function submission(): SubmissionService
    {
        return $this->submissionService ??= new SubmissionService($this);
    }

    public function twoFactor(): TwoFactorAuthenticationService
    {
        return $this->twoFactorAuthenticationService ??= new TwoFactorAuthenticationService($this);
    }

    public function users(): UserService
    {
        return $this->userService ??= new UserService($this);
    }

    public function webhooks(): WebhookService
    {
        return $this->webhookService ??= new WebhookService($this);
    }
}
