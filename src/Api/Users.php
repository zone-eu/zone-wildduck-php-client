<?php

namespace Wildduck\Api;

use Wildduck\Exceptions\InvalidRequestException;
use Wildduck\Exceptions\UriNotFoundException;
use Wildduck\Http\Request;
use Wildduck\Util\Uri;

class Users
{

    public function create(array $params)
    {
        $validator = app()['validator']->make($params, [
            'username' => 'required|alpha_num',
            'password' => 'required',
            'address' => 'sometimes|email',
            'emptyAddress' => 'sometimes|boolean',
            'requirePasswordChange' => 'sometimes|boolean',
            'tags' => 'sometimes|array',
            'addTagsToAddress' => 'sometimes|boolean',
            'retention' => 'sometimes|integer',
            'encryptMessages' => 'sometimes|boolean',
            'encryptForwarded' => 'sometimes|boolean',
            'pubKey' => 'sometimes|string',
            'language' => 'sometimes|string',
            'targets' => 'sometimes|array',
            'spamLevel' => 'sometimes|integer',
            'quota' => 'sometimes|integer',
            'recipients' => 'sometimes|integer',
            'forwards' => 'sometimes|integer',
            'imapMaxUpload' => 'sometimes|integer',
            'imapMaxDownload' => 'sometimes|integer',
            'pop3MaxDownload' => 'sometimes|integer',
            'sess' => 'sometimes|string',
            'ip' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator->errors()->all()); // TODO: Pass in Validator instance instead
        }

        try {
            return Request::post(Uri::get('users.create'), $params);
        } catch (UriNotFoundException $e) {

        }
    }

    public function delete($params)
    {
        $validator = app()['validator']->make($params, [
            'id' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator->errors()->all());
        }

        try {
            return Request::delete(Uri::get('users.delete', $params));
        } catch (UriNotFoundException $e) {

        }
    }
}