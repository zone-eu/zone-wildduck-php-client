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
}
