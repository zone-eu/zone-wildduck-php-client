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
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \ErrorException
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
            'next' => 'sometimes|string',
            'previous' => 'sometimes|string',
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
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \ErrorException
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
     * @throws \ErrorException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function update(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'mailbox' => 'required|string',
            'message' => 'required|string',
            'moveTo' => 'sometimes|string',
            'seen' => 'sometimes|boolean',
            'flagged' => 'sometimes|boolean',
            'draft' => 'sometimes|boolean',
            'expires' => 'sometimes|string',
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

        return Request::put(Uri::get('messages.update', $args), $params);
    }

    public function delete(array $params)
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

        return Request::delete(Uri::get('messages.delete', $params));
    }

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \ErrorException
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
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \ErrorException
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
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \ErrorException
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

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws \ErrorException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function search(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'mailbox' => 'sometimes|string',
            'thread' => 'sometimes|string',
            'query' => 'sometimes|string',
            'datestart' => 'sometimes|string',
            'dateend' => 'sometimes|string',
            'from' => 'sometimes|string',
            'to' => 'sometimes|string',
            'subject' => 'sometimes|string',
            'attachments' => 'sometimes|boolean',
            'flagged' => 'sometimes|boolean',
            'searchable' => 'sometimes|boolean',
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

        return Request::get(Uri::get('messages.search', $args), $params);
    }

    public function submit(array $params)
    {
        /** @var  \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'mailbox' => 'required|string',
            'message' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::post(Uri::get('messages.submit', $params));
    }

    public function upload(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'mailbox' => 'required|string',
            'unseen' => 'boolean',
            'draft' => 'boolean',
            'flagged' => 'boolean',
            'raw' => 'string',
            'from.name' => 'required|string',
            'from.address' => 'required|string',
            'to.*.name' => 'string',
            'to.*.address' => 'string',
            'cc.*.name' => 'string',
            'cc.*.address' => 'string',
            'bcc.*.name' => 'string',
            'bcc.*.address' => 'string',
            'subject' => 'string',
            'text' => 'string',
            'html' => 'string',
            'headers.*.key' => 'string',
            'headers.*.value' => 'string',
            'attachments.*.content' => 'string',
            'attachments.*.filename' => 'string',
            'attachments.*.contentType' => 'string',
            'attachments.*.cid' => 'string',
            'metaData' => 'string',
            'reference.mailbox' => 'string',
            'reference.id' => 'integer',
            'reference.action' => 'string',
            'reference.attachments' => 'boolean',
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

        return Request::post(Uri::get('messages.upload', $args), $params);
    }
}