<?php

namespace Wildduck\Api;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Validation\Validator;
use Wildduck\Http\Request;

class Authentication
{

    public function authenticate(array $params)
    {
        $validator = Validator::make($params, [
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
            $res = Request::post('authentication.authenticate', $params);
        } catch (ServerException $e) {
            dd($e->getResponse()->getStatusCode() . ' ' . $e->getResponse()->getReasonPhrase());
        } catch (GuzzleException $e) {

        }
    }
}
