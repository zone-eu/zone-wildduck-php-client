<?php

namespace Wildduck\Api;

use Wildduck\Exceptions\InvalidRequestException;
use Wildduck\Exceptions\UriNotFoundException;
use Wildduck\Http\Request;
use Wildduck\Util\Uri;

class Filters
{

    public function get(array $params)
    {
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'filter' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        try {
            return Request::get(Uri::get('filters.get', $params));
        } catch (UriNotFoundException $e) {

        }
    }

    public function getByUser(array $params)
    {
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        try {
            return Request::get(Uri::get('filters.user', $params));
        } catch (UriNotFoundException $e) {

        }
    }

    public function create(array $params)
    {
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
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
        ];

        unset($params['user']);

        try {
            return Request::post(Uri::get('filters.create', $args), $params);
        } catch (UriNotFoundException $e) {

        }
    }

    public function update(array $params)
    {
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

        try {
            return Request::put(Uri::get('filters.update', $args), $params);
        } catch (UriNotFoundException $e) {

        }
    }

    public function delete(array $params)
    {
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'filter' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        try {
            return Request::delete(Uri::get('filters.get', $params));
        } catch (UriNotFoundException $e) {

        }
    }
}
