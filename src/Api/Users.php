<?php

namespace Wildduck\Api;

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
}