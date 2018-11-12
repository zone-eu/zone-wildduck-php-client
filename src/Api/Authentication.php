<?php

namespace Wildduck\Api;

use Wildduck\Exceptions\InvalidRequestException;
use Wildduck\Http\Request;
use Wildduck\Util\Uri;

class Authentication
{

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function authenticate(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'username' => 'required|string',
            'password' => 'required|string',
            'protocol' => 'sometimes|string',
            'scope' => 'sometimes|in:master,imap,smtp,pop3',
            'sess' => 'sometimes|string',
            'ip' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::post(Uri::get('authentication.authenticate'), $params);
    }

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function listEvents(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'action' => 'sometimes|string',
            'sess' => 'sometimes|string',
            'ip' => 'sometimes|string',
            'limit' => 'sometimes|integer',
            'page' => 'sometimes|integer',
            'next' => 'sometimes|string',
            'previous' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        $args = [
            'user' => $params['user'],
        ];

        unset($params['user']);

        return Request::get(Uri::get('authentication.listEvents', $args), $params);
    }

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function getEvent(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required',
            'event' => 'required',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::get(Uri::get('authentication.getEvent', $params));
    }
}
