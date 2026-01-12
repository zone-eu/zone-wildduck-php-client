<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\Message\BulkUpdateMessagesRequestDto;
use Zone\Wildduck\Dto\Message\ForwardMessageRequestDto;
use Zone\Wildduck\Dto\Message\ListMessagesRequestDto;
use Zone\Wildduck\Dto\Message\SearchApplyMessagesRequestDto;
use Zone\Wildduck\Dto\Message\SearchMessagesRequestDto;
use Zone\Wildduck\Dto\Message\UploadMessageRequestDto;
use Zone\Wildduck\Dto\Shared\SuccessResponseDto;
use Zone\Wildduck\Dto\Message\BulkUpdateMessagesResponseDto;
use Zone\Wildduck\Dto\Message\ForwardMessageResponseDto;
use Zone\Wildduck\Dto\Message\MessagePaginatedResponseDto;
use Zone\Wildduck\Dto\Message\MessageResponseDto;
use Zone\Wildduck\Dto\Message\UploadMessageResponseDto;
use Zone\Wildduck\Dto\PaginatedResultDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

class MessageService extends AbstractService
{
    /**
     * @param string $user
     * @param string $mailbox
     * @param int $message
     * @param array<string, mixed>|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function delete(string $user, string $mailbox, int $message, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto(
            'delete',
            $this->buildPath('/users/%s/mailboxes/%s/messages/%s', $user, $mailbox, $message),
            null,
            SuccessResponseDto::class,
            $opts
        );
    }

    /**
     * @param string $user
     * @param string $queueId
     * @param array<string, mixed>|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function deleteOutbound(string $user, string $queueId, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('delete', $this->buildPath('/users/%s/outbound/%s', $user, $queueId), null, SuccessResponseDto::class, $opts);
    }

    /**
     * @param string $user
     * @param string $mailbox
     * @param int $message
     * @param string $attachment
     * @param array<string, mixed>|null $opts
     * @return string Binary attachment content
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function downloadAttachment(string $user, string $mailbox, int $message, string $attachment, array|null $opts = null): string
    {
        $opts = $opts ?? [];
        $opts['raw'] = true;
        $response = $this->requestResponse(
            'get',
            $this->buildPath(
                '/users/%s/mailboxes/%s/messages/%s/attachments/%s',
                $user,
                $mailbox,
                $message,
                $attachment
            ),
            null,
            $opts
        );

        // When raw is true, requestResponse returns an ApiResponse object
        if ($response instanceof \Zone\Wildduck\ApiResponse) {
            return $response->body ?? '';
        }

        return $response;
    }

    /**
     * @param string $user
     * @param string $mailbox
     * @param int $message
     * @param ForwardMessageRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return ForwardMessageResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function forward(string $user, string $mailbox, int $message, ForwardMessageRequestDto $params, array|null $opts = null): ForwardMessageResponseDto
    {
        return $this->requestDto(
            'post',
            $this->buildPath('/users/%s/mailboxes/%s/messages/%s/forward', $user, $mailbox, $message),
            $params,
            ForwardMessageResponseDto::class,
            $opts
        );
    }

    /**
     * @param string $user
     * @param string $mailbox
     * @param int $message
     * @param array<string, mixed>|null $opts
     * @return string Raw message source (RFC822)
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function source(string $user, string $mailbox, int $message, array|null $opts = null): string
    {
        $opts = $opts ?? [];
        $opts['raw'] = true;
        $response = $this->requestResponse(
            'get',
            $this->buildPath(
                '/users/%s/mailboxes/%s/messages/%s/message.eml',
                $user,
                $mailbox,
                $message
            ),
            null,
            $opts
        );

        // When raw is true, requestResponse returns an ApiResponse object
        if ($response instanceof \Zone\Wildduck\ApiResponse) {
            return $response->body ?? '';
        }

        return $response;
    }

    /**
     * @param string $user
     * @param string $mailbox
     * @param ListMessagesRequestDto|null $params
     * @param array<string, mixed>|null $opts
     * @return MessagePaginatedResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function all(string $user, string $mailbox, ListMessagesRequestDto|null $params = null, array|null $opts = null): MessagePaginatedResponseDto
    {
        return $this->requestDto(
            'get',
            $this->buildPath('/users/%s/mailboxes/%s/messages', $user, $mailbox),
            $params,
            MessagePaginatedResponseDto::class,
            $opts
        );
    }

    /**
     * @param string $user
     * @param string $mailbox
     * @param int $message
     * @param array<string, mixed>|null $opts
     * @return MessageResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function get(string $user, string $mailbox, int $message, array|null $opts = null): MessageResponseDto
    {
        return $this->requestDto(
            'get',
            $this->buildPath('/users/%s/mailboxes/%s/messages/%s', $user, $mailbox, $message),
            null,
            MessageResponseDto::class,
            $opts
        );
    }

    /**
     * @param string $user
     * @param SearchMessagesRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return PaginatedResultDto<MessageResponseDto>
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     * @throws InvalidDatabaseException
     */
    public function search(string $user, SearchMessagesRequestDto $params, array|null $opts = null): PaginatedResultDto
    {
        return $this->requestPaginatedDto('get', $this->buildPath('/users/%s/search', $user), $params, MessageResponseDto::class, $opts);
    }

    /**
     * @param string $user
     * @param SearchApplyMessagesRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return BulkUpdateMessagesResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     * @link https://docs.wildduck.email/api/#operation/searchApplyMessages
     */
    public function searchApplyMessages(string $user, SearchApplyMessagesRequestDto $params, array|null $opts = null): BulkUpdateMessagesResponseDto
    {
        return $this->requestDto('post', $this->buildPath('/users/%s/search', $user), $params, BulkUpdateMessagesResponseDto::class, $opts);
    }

    /**
     * @param string $user
     * @param string $mailbox
     * @param int $message
     * @param array<string, mixed>|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function submitDraft(string $user, string $mailbox, int $message, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto(
            'post',
            $this->buildPath('/users/%s/mailboxes/%s/messages/%s/submit', $user, $mailbox, $message),
            null,
            SuccessResponseDto::class,
            $opts
        );
    }

    /**
     * @param string $user
     * @param string $mailbox
     * @param BulkUpdateMessagesRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return BulkUpdateMessagesResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function update(string $user, string $mailbox, BulkUpdateMessagesRequestDto $params, array|null $opts = null): BulkUpdateMessagesResponseDto
    {
        return $this->requestDto(
            'put',
            $this->buildPath('/users/%s/mailboxes/%s/messages', $user, $mailbox),
            $params,
            BulkUpdateMessagesResponseDto::class,
            $opts
        );
    }

    /**
     * @param string $user
     * @param string $mailbox
     * @param UploadMessageRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return UploadMessageResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function upload(string $user, string $mailbox, UploadMessageRequestDto $params, array|null $opts = null): UploadMessageResponseDto
    {
        return $this->requestDto(
            'post',
            $this->buildPath('/users/%s/mailboxes/%s/messages', $user, $mailbox),
            $params,
            UploadMessageResponseDto::class,
            $opts
        );
    }
}
