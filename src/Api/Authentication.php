<?php

namespace Wildduck\Api;

use Validator;
use Wildduck\Exceptions\UriNotFoundException;
use Wildduck\Http\Request;
use Wildduck\Util\Uri;

class Authentication
{

    public function authenticate(array $params)
    {
        $validator = app()['validator']->make($params, [
            'username' => 'required|string',
            'password' => 'required|string',
            'protocol' => 'sometimes|string',
            'scope' => 'sometimes|in:master,imap,smtp,pop3',
            'sess' => 'sometimes|string',
            'ip' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator->errors()->all());
        }

        try {
            return Request::post(Uri::get('authentication.authenticate'), $params);
        } catch (UriNotFoundException $e) {

        }
    }
}
