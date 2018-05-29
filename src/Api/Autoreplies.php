<?php

namespace Wildduck\Api;

use Wildduck\Exceptions\InvalidRequestException;
use Wildduck\Http\Request;
use Wildduck\Util\Uri;

class Autoreplies
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
            'user' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::get(Uri::get('autoreplies.get', $params));
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
            'status' => 'boolean',
            'name' => 'string',
            'subject' => 'string',
            'html' => 'string',
            'text' => 'string',
            'start' => 'string',
            'end' => 'string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        $args = [
            'user' => $params['user'],
        ];

        unset($params['user']);

        return Request::put(Uri::get('autoreplies.update', $args), $params);
    }

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function delete(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::get(Uri::get('autoreplies.delete', $params));
    }
}
