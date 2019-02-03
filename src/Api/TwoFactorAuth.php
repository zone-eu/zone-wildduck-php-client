<?php

namespace Wildduck\Api;

use Illuminate\Validation\Validator;
use Wildduck\Exceptions\InvalidRequestException;
use Wildduck\Http\Request;
use Wildduck\Util\Uri;

class TwoFactorAuth
{

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws \ErrorException
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function enableCustom(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::put(Uri::get('two-factor.custom.enable', $params));
    }

    /**
     * @param array $params
     * @return array|string
     * @throws InvalidRequestException
     * @throws \ErrorException
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function enableTOTP(array $params)
    {
        /** @var Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        $args = [
            'user' => $params['user'],
        ];

        unset($params['user']);

        return Request::post(Uri::get('two-factor.enable.totp', $args), $params);
    }

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws \ErrorException
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function disable(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::delete(Uri::get('two-factor.disable', $params));
    }

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws \ErrorException
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function disableCustom(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::delete(Uri::get('two-factor.disable.custom', $params));
    }

    /**
     * @param array $params
     * @return array|string
     * @throws InvalidRequestException
     * @throws \ErrorException
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function disableTOTP(array $params)
    {
        /** @var Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::delete(Uri::get('two-factor.disable.totp', $params));
    }

    /**
     * @param array $params
     * @return array|string
     * @throws InvalidRequestException
     * @throws \ErrorException
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function generateTOTP(array $params)
    {
        /** @var Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'label' => 'sometimes|string',
            'issuer' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        $args = [
            'user' => $params['user'],
        ];

        unset($params['user']);

        return Request::post(Uri::get('two-factor.generate.totp', $args), $params);
    }

    /**
     * @param array $params
     * @return array|string
     * @throws InvalidRequestException
     * @throws \ErrorException
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function validateTOTP(array $params)
    {
        /** @var Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        $args = [
            'user' => $params['user'],
        ];

        unset($params['user']);

        return Request::post(Uri::get('two-factor.validate.totp', $args), $params);
    }

    /**
     * @param array $params
     * @return array|string
     * @throws InvalidRequestException
     * @throws \ErrorException
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function generateU2F(array $params)
    {
        /** @var Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'appId' => 'sometimes|url',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        $args = [
            'user' => $params['user'],
        ];

        unset($params['user']);

        return Request::post(Uri::get('two-factor.generate.u2f', $args), $params);
    }

    /**
     * @param array $params
     * @return array|string
     * @throws InvalidRequestException
     * @throws \ErrorException
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function enableU2F(array $params)
    {
        /** @var Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'errorCode' => 'sometimes|numeric',
            'clientData' => 'sometimes|string',
            'registrationData' => 'sometimes|string',
            'version' => 'sometimes|in:U2F_V2',
            'challenge' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        $args = [
            'user' => $params['user'],
        ];

        unset($params['user']);

        return Request::post(Uri::get('two-factor.enable.u2f', $args), $params);
    }

    /**
     * @param array $params
     * @return array|string
     * @throws InvalidRequestException
     * @throws \ErrorException
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function disableU2F(array $params)
    {
        /** @var Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        return Request::delete(Uri::get('two-factor.disable.u2f', $params));
    }

    /**
     * @param array $params
     * @return array|string
     * @throws InvalidRequestException
     * @throws \ErrorException
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function startU2F(array $params)
    {
        /** @var Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'appId' => 'sometimes|url',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        $args = [
            'user' => $params['user'],
        ];

        unset($params['user']);

        return Request::post(Uri::get('two-factor.start.u2f', $args), $params);
    }

    /**
     * @param array $params
     * @return array|string
     * @throws InvalidRequestException
     * @throws \ErrorException
     * @throws \Wildduck\Exceptions\RequestFailedException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function validateU2F(array $params)
    {
        /** @var Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'errorCode' => 'sometimes|numeric',
            'clientData' => 'sometimes|string',
            'signatureData' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        $args = [
            'user' => $params['user'],
        ];

        unset($params['user']);

        return Request::post(Uri::get('two-factor.validate.u2f', $args), $params);
    }
}
