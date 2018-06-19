<?php

namespace Wildduck\Api;

use Wildduck\Exceptions\InvalidRequestException;
use Wildduck\Http\Request;
use Wildduck\Util\Uri;

class Addresses
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
            'address' => 'required|email',
            'name' => 'sometimes|string',
            'tags' => 'sometimes|array',
            'main' => 'sometimes|boolean',
            'allowWildcard' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        $args = [
            'user' => $params['user'],
        ];

        unset($params['user']);

        return Request::post(Uri::get('addresses.create', $args), $params);
    }

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function createForwarded(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'address' => 'required|email',
            'name' => 'sometimes|string',
            'targets' => 'sometimes|array',
            'forwards' => 'sometimes|integer',
            'allowWildcard' => 'sometimes|boolean',
            'tags' => 'sometimes|array',
            'autoreply.status' => 'sometimes|boolean',
            'autoreply.start' => 'sometimes',
            'autoreply.end' => 'sometimes',
            'autoreply.name' => 'sometimes|string',
            'autoreply.text' => 'sometimes|string',
            'autoreply.html' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::post(Uri::get('addresses.create.forwarded'), $params);
    }

    /**
     * @param array $params
     * @return array|mixed|\Psr\Http\Message\array
     * @throws InvalidRequestException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function deleteForwarded(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::delete(Uri::get('addresses.delete.forwarded', $params));
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
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::delete(Uri::get('addresses.delete', $params));
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
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::get(Uri::get('addresses.get', $params));
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
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::get(Uri::get('addresses.list', $params));
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
            'id' => 'required|string',
            'name' => 'sometimes|string',
            'address' => 'sometimes|email',
            'main' => 'sometimes|boolean',
            'tags' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        $args = [
            'user' => $params['user'],
            'id' => $params['id'],
        ];

        unset($params['user']);
        unset($params['id']);

        return Request::put(Uri::get('addresses.update', $args), $params);
    }
}