<?php

namespace Wildduck\Api;

use Wildduck\Exceptions\InvalidRequestException;
use Wildduck\Exceptions\UriNotFoundException;
use Wildduck\Http\Request;
use Wildduck\Util\Uri;

class Filters
{

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws UriNotFoundException
     */
    public function get(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'filter' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::get(Uri::get('filters.get', $params));
    }

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws UriNotFoundException
     */
    public function getByUser(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::get(Uri::get('filters.user', $params));
    }

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws UriNotFoundException
     */
    public function create(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'name' => 'sometimes|string',
            'query.from' => 'sometimes|string',
            'query.to' => 'sometimes|string',
            'query.subject' => 'sometimes|string',
            'query.text' => 'sometimes|string',
            'query.ha' => 'sometimes|boolean',
            'query.size' => 'sometimes|integer',
            'action.seen' => 'sometimes|boolean',
            'action.flag' => 'sometimes|boolean',
            'action.delete' => 'sometimes|boolean',
            'action.spam' => 'sometimes|boolean',
            'action.mailbox' => 'sometimes|string',
            'action.targets' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        $args = [
            'user' => $params['user'],
        ];

        unset($params['user']);

        return Request::post(Uri::get('filters.create', $args), $params);
    }

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws UriNotFoundException
     */
    public function update(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'filter' => 'required|string',
            'name' => 'string',
            'query.from' => 'string',
            'query.to' => 'string',
            'query.subject' => 'string',
            'query.text' => 'string',
            'query.ha' => 'boolean',
            'query.size' => 'integer',
            'action.seen' => 'boolean',
            'action.flag' => 'boolean',
            'action.delete' => 'boolean',
            'action.spam' => 'boolean',
            'action.mailbox' => 'string',
            'action.targets' => 'array',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        $args = [
            'user' => $params['user'],
            'filter' => $params['filter'],
        ];

        unset($params['user']);
        unset($params['filter']);

        return Request::put(Uri::get('filters.update', $args), $params);
    }

    /**
     * @param array $params
     * @return array|mixed|\Psr\Http\Message\array
     * @throws InvalidRequestException
     * @throws UriNotFoundException
     */
    public function delete(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'filter' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::delete(Uri::get('filters.get', $params));
    }
}
