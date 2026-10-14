<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\Message\CreateLabelRequestDto;
use Zone\Wildduck\Dto\Message\FlagCounterRequestDto;
use Zone\Wildduck\Dto\Message\FlagCounterResponseDto;
use Zone\Wildduck\Dto\Message\LabelCounterRequestDto;
use Zone\Wildduck\Dto\Message\LabelCounterResponseDto;
use Zone\Wildduck\Dto\Message\LabelResponseDto;
use Zone\Wildduck\Dto\Message\ListLabelsRequestDto;
use Zone\Wildduck\Dto\Message\RenameLabelRequestDto;
use Zone\Wildduck\Dto\Message\UserLabelsResponseDto;
use Zone\Wildduck\Dto\Shared\SuccessResponseDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidArgumentException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

/**
 * Label service for managing user labels and label counters
 */
class LabelService extends AbstractService
{
    /**
     * List all labels of a user, with optional message counters
     *
     * @param string $user
     * @param bool $counters Include total/unseen counters per label
     * @param bool $searchable Count only searchable mailboxes (Junk, Trash and non-searchable excluded)
     * @param array|null $opts
     * @return UserLabelsResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function list(
        string $user,
        bool $counters = false,
        bool $searchable = true,
        array|null $opts = null
    ): UserLabelsResponseDto {
        return $this->requestDto(
            'get',
            $this->buildPath('/users/%s/labels', $user),
            new ListLabelsRequestDto($counters, $searchable),
            UserLabelsResponseDto::class,
            $opts
        );
    }

    /**
     * Get total/unseen counters for a custom label
     *
     * @param string $user
     * @param string $labelName Label name (percent-encoded in the path)
     * @param bool $searchable Count only searchable mailboxes (Junk, Trash and non-searchable excluded)
     * @param array|null $opts
     * @return LabelCounterResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function getCounter(
        string $user,
        string $labelName,
        bool $searchable = true,
        array|null $opts = null
    ): LabelCounterResponseDto {
        if ('' === trim($labelName)) {
            throw new InvalidArgumentException('The label name cannot be null or whitespace.');
        }

        // Label names may contain spaces. buildPath() encodes with urlencode(),
        // which turns spaces into "+"; WildDuck percent-decodes path segments,
        // so "+" stays a literal plus and the exact-name lookup misses.
        // Encode the name with rawurlencode() and append it outside buildPath().
        $path = $this->buildPath('/users/%s', $user) . '/label-counters/' . rawurlencode($labelName);

        return $this->requestDto(
            'get',
            $path,
            new LabelCounterRequestDto($searchable),
            LabelCounterResponseDto::class,
            $opts
        );
    }

    /**
     * Get total/unseen counters for a system flag (flagged/unread)
     *
     * @param string $user
     * @param string $flag One of: flagged, unread
     * @param bool $searchable Count only searchable mailboxes (Junk, Trash and non-searchable excluded)
     * @param array|null $opts
     * @return FlagCounterResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function getFlagCounter(
        string $user,
        string $flag,
        bool $searchable = true,
        array|null $opts = null
    ): FlagCounterResponseDto {
        return $this->requestDto(
            'get',
            $this->buildPath('/users/%s/flag-counters/%s', $user, $flag),
            new FlagCounterRequestDto($searchable),
            FlagCounterResponseDto::class,
            $opts
        );
    }

    /**
     * Create a label (repeated creation returns the existing label)
     *
     * @param string $user
     * @param string $name
     * @param array|null $opts
     * @return LabelResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function create(string $user, string $name, array|null $opts = null): LabelResponseDto
    {
        return $this->requestDto(
            'post',
            $this->buildPath('/users/%s/labels', $user),
            new CreateLabelRequestDto($name),
            LabelResponseDto::class,
            $opts
        );
    }

    /**
     * Get a single label by its id
     *
     * @param string $user
     * @param string $label
     * @param array|null $opts
     * @return LabelResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function get(string $user, string $label, array|null $opts = null): LabelResponseDto
    {
        return $this->requestDto(
            'get',
            $this->buildPath('/users/%s/labels/%s', $user, $label),
            null,
            LabelResponseDto::class,
            $opts
        );
    }

    /**
     * Rename a label, preserving its id and message assignments
     *
     * @param string $user
     * @param string $label
     * @param string $newName
     * @param array|null $opts
     * @return LabelResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function rename(string $user, string $label, string $newName, array|null $opts = null): LabelResponseDto
    {
        return $this->requestDto(
            'put',
            $this->buildPath('/users/%s/labels/%s', $user, $label),
            new RenameLabelRequestDto($newName),
            LabelResponseDto::class,
            $opts
        );
    }

    /**
     * Delete a label (removal is scheduled asynchronously by WildDuck)
     *
     * @param string $user
     * @param string $label
     * @param array|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function delete(string $user, string $label, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto(
            'delete',
            $this->buildPath('/users/%s/labels/%s', $user, $label),
            null,
            SuccessResponseDto::class,
            $opts
        );
    }
}
