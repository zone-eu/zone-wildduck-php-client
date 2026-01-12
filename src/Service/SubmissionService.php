<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\Submission\SubmitMessageRequestDto;
use Zone\Wildduck\Dto\Submission\SubmitMessageResponseDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

class SubmissionService extends AbstractService
{
    /**
     * @param string $user
     * @param SubmitMessageRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return SubmitMessageResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function submit(string $user, SubmitMessageRequestDto $params, array|null $opts = null): SubmitMessageResponseDto
    {
        return $this->requestDto('post', $this->buildPath('/users/%s/submit', $user), $params, SubmitMessageResponseDto::class, $opts);
    }
}
