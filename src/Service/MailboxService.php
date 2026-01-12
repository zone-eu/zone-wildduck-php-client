<?php

declare(strict_types=1);

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\Shared\CreatedResourceResponseDto;
use Zone\Wildduck\Dto\Mailbox\CreateMailboxRequestDto;
use Zone\Wildduck\Dto\Mailbox\DeleteAllMessagesRequestDto;
use Zone\Wildduck\Dto\Mailbox\DeleteAllMessagesResponseDto;
use Zone\Wildduck\Dto\Mailbox\GetMailboxRequestDto;
use Zone\Wildduck\Dto\Mailbox\ListMailboxesRequestDto;
use Zone\Wildduck\Dto\Mailbox\MailboxResponseDto;
use Zone\Wildduck\Dto\Mailbox\UpdateMailboxRequestDto;
use Zone\Wildduck\Dto\PaginatedResultDto;
use Zone\Wildduck\Dto\Shared\SuccessResponseDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

/**
 * Mailbox service for managing user mailboxes
 */
class MailboxService extends AbstractService
{
    /**
     * Create a new mailbox
     *
     * @param string $user
     * @param CreateMailboxRequestDto $params
     * @param array|null $opts
     * @return CreatedResourceResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function create(string $user, CreateMailboxRequestDto $params, array|null $opts = null): CreatedResourceResponseDto
    {
        return $this->requestDto('post', $this->buildPath('/users/%s/mailboxes', $user), $params, CreatedResourceResponseDto::class, $opts);
    }

    /**
     * Delete a mailbox
     *
     * @param string $user
     * @param string $mailbox
     * @param array|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function delete(string $user, string $mailbox, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('delete', $this->buildPath('/users/%s/mailboxes/%s', $user, $mailbox), null, SuccessResponseDto::class, $opts);
    }

    /**
     * Delete all messages in a mailbox
     *
     * @param string $user
     * @param string $mailbox
     * @param DeleteAllMessagesRequestDto $params
     * @param array|null $opts
     * @return DeleteAllMessagesResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function deleteAllMessages(string $user, string $mailbox, DeleteAllMessagesRequestDto $params, array|null $opts = null): DeleteAllMessagesResponseDto
    {
        return $this->requestDto('delete', $this->buildPath('/users/%s/mailboxes/%s/messages', $user, $mailbox), $params, DeleteAllMessagesResponseDto::class, $opts);
    }

    /**
     * Get list of all mailboxes for a user
     *
     * @param string $user
     * @param ListMailboxesRequestDto|null $params
     * @param array|null $opts
     * @return PaginatedResultDto<MailboxResponseDto>
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function all(string $user, ?ListMailboxesRequestDto $params = null, array|null $opts = null): PaginatedResultDto
    {
        return $this->requestPaginatedDto('get', $this->buildPath('/users/%s/mailboxes', $user), $params, MailboxResponseDto::class, $opts);
    }

    /**
     * Get mailbox information
     *
     * @param string $user
     * @param string $mailbox
     * @param GetMailboxRequestDto|null $params
     * @param array|null $opts
     * @return MailboxResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function get(string $user, string $mailbox, GetMailboxRequestDto|null $params = null, array|null $opts = null): MailboxResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/users/%s/mailboxes/%s', $user, $mailbox), $params, MailboxResponseDto::class, $opts);
    }

    /**
     * Update mailbox information
     *
     * @param string $user
     * @param string $mailbox
     * @param UpdateMailboxRequestDto $params
     * @param array|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function update(string $user, string $mailbox, UpdateMailboxRequestDto $params, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('put', $this->buildPath('/users/%s/mailboxes/%s', $user, $mailbox), $params, SuccessResponseDto::class, $opts);
    }
}
