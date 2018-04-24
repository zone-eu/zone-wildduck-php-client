<?php

namespace Wildduck\Api;

use Wildduck\Exceptions\InvalidRequestException;
use Wildduck\Http\Request;
use Wildduck\Util\Uri;

class ApplicationPasswords
{

    public function all(array $params)
    {
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::get(Uri::get('asps.list', $params));
    }

    public function create(array $params)
    {
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'description' => 'required|string',
            'scopes' => 'required|array',
            'generateMobileConfig' => 'sometimes|boolean',
            'sess' => 'sometimes|string',
            'ip' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        $args = [
            'user' => $params['user'],
        ];

        unset($params['user']);
        return Request::post(Uri::get('asps.create', $args), $params);
    }

    public function delete(array $params)
    {
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'asp' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::delete(Uri::get('asps.delete', $params));
    }
}