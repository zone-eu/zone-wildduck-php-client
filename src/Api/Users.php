<?php

namespace Wildduck\Api;

use Wildduck\Client;
use Wildduck\Exceptions\InvalidRequestException;
use Wildduck\Http\Request;
use Wildduck\Util\Uri;

class Users
{

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \ErrorException
     */
    public function get(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'id' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::get(Uri::get('users.get', $params));
    }

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \ErrorException
     */
    public function create(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'username' => 'required|alpha_num',
            'password' => 'required',
            'address' => 'sometimes|email',
            'emptyAddress' => 'sometimes|boolean',
            'requirePasswordChange' => 'sometimes|boolean',
            'tags' => 'sometimes|array',
            'addTagsToAddress' => 'sometimes|boolean',
            'retention' => 'sometimes|integer',
            'encryptMessages' => 'sometimes|boolean',
            'encryptForwarded' => 'sometimes|boolean',
            'pubKey' => 'sometimes|string',
            'language' => 'sometimes|string',
            'targets' => 'sometimes|array',
            'spamLevel' => 'sometimes|integer',
            'quota' => 'sometimes|integer',
            'recipients' => 'sometimes|integer',
            'forwards' => 'sometimes|integer',
            'imapMaxUpload' => 'sometimes|integer',
            'imapMaxDownload' => 'sometimes|integer',
            'pop3MaxDownload' => 'sometimes|integer',
            'sess' => 'sometimes|string',
            'ip' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::post(Uri::get('users.create'), $params);
    }

    /**
     * @param array $params
     * @return array|mixed|\Psr\Http\Message\array
     * @throws InvalidRequestException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \ErrorException
     */
    public function delete(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'id' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::delete(Uri::get('users.delete', $params));
    }

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \ErrorException
     */
    public function resolve(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'username' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::get(Uri::get('users.resolve', $params));
    }

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \ErrorException
     */
    public function update(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'id' => 'required|string',
            'name' => 'sometimes|string',
            'existingPassword' => 'sometimes|string',
            'password' => 'sometimes|string',
            'tags' => 'sometimes|array',
            'retention' => 'sometimes|integer',
            'encryptMessages' => 'sometimes|boolean',
            'encryptForwarded' => 'sometimes|boolean',
            'pubKey' => 'sometimes|string',
            'language' => 'sometimes|string',
            'targets' => 'sometimes|array',
            'spamLevel' => 'sometimes|integer',
            'quota' => 'sometimes|integer',
            'recipients' => 'sometimes|integer',
            'forwards' => 'sometimes|integer',
            'imapMaxUpload' => 'sometimes|integer',
            'imapMaxDownload' => 'sometimes|integer',
            'pop3MaxDownload' => 'sometimes|integer',
            'receivedMax' => 'sometimes|integer',
            'disable2fa' => 'sometimes|boolean',
            'disabled' => 'sometimes|boolean',
            'sess' => 'sometimes|string',
            'ip' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        $args = [
            'id' => $params['id'],
        ];

        unset($params['id']);

        return Request::put(Uri::get('users.update', $args), $params);
    }

    public function getSourceUrl(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        $args = [
            'id' => $params['user'],
        ];

        $baseUrl = Client::instance()->getHost();
        $uri = Uri::get('users.stream', $args);

        return $baseUrl . $uri;
    }

    /**
     * TODO: Doesn't work this way. Maybe figure out at some point. Get eventSource url through getSourceUrl() instead.
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws \ErrorException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function stream(array $params)
    {

        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        $args = [
            'id' => $params['user'],
        ];

        return Request::get(Uri::get('users.stream', $args));
    }
}