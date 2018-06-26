<?php

namespace Wildduck\Api;

use Wildduck\Exceptions\InvalidRequestException;
use Wildduck\Http\Request;
use Wildduck\Util\Uri;

class Mailboxes
{

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
            'user' => 'required|string',
            'path' => 'required|string',
            'retention' => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        $args = [
            'user' => $params['user'],
        ];

        unset($params['user']);

        return Request::post(Uri::get('mailboxes.create', $args), $params);
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
            'user' => 'required|string',
            'mailbox' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::delete(Uri::get('mailboxes.delete', $params));
    }

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function list(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'counters' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        $args = [
            'user' => $params['user'],
        ];

        unset($params['user']);

        return Request::get(Uri::get('mailboxes.list', $args), $params);
    }

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
            'user' => 'required|string',
            'mailbox' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::get(Uri::get('mailboxes.get', $params));
    }

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function update(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'mailbox' => 'required|string',
            'path' => 'sometimes|string',
            'retention' => 'sometimes|string',
            'subscribed' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        $args = [
            'user' => $params['user'],
            'mailbox' => $params['mailbox'],
        ];

        unset($params['user']);
        unset($params['mailbox']);

        return Request::put(Uri::get('mailboxes.update', $args), $params);
    }
}