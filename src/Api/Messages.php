<?php

namespace Wildduck\Api;

use Wildduck\Exceptions\InvalidRequestException;
use Wildduck\Http\Request;
use Wildduck\Util\Uri;

class Messages
{
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
            'mailbox' => 'required|string',
            'limit' => 'sometimes|integer',
            'page' => 'sometimes|integer',
            'order' => 'sometimes|in:asc,desc',
            'next' => 'sometimes|number',
            'previous' => 'sometimes|number',
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

        return Request::get(Uri::get('messages.list', $args), $params);
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
            'message' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::get(Uri::get('messages.get', $params), ['markAsSeen' => true]);
    }

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function downloadAttachment(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'mailbox' => 'required|string',
            'message' => 'required|integer',
            'attachment' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::get(Uri::get('messages.downloadAttachment', $params));
    }

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function events(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'mailbox' => 'required|string',
            'message' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::get(Uri::get('messages.events', $params));
    }

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function source(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'mailbox' => 'required|string',
            'message' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::get(Uri::get('messages.source', $params));
    }
}