<?php

namespace Wildduck\Api;

use Wildduck\Exceptions\InvalidRequestException;
use Wildduck\Http\Request;
use Wildduck\Util\Uri;

class Submission
{

    /**
     * @param array $params
     * @return array
     * @throws InvalidRequestException
     * @throws \Wildduck\Exceptions\UriNotFoundException
     */
    public function submit(array $params)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app()['validator']->make($params, [
            'user' => 'required|string',
            'reference' => 'required_with:reference.mailbox,reference.id,reference.action',
            'mailbox' => 'string',
            'uploadOnly' => 'boolean',
            'isDraft' => 'boolean',
            'sendTime' => 'string',
            'envelope.from.address' => 'string',
            'envelope.to.address' => 'string',
            'from.name' => 'string',
            'from.address' => 'string',
            'to.name' => 'string',
            'to.address' => 'string',
            'cc.name' => 'string',
            'cc.address' => 'string',
            'bcc.name' => 'string',
            'bcc.address' => 'string',
            'subject' => 'required|string',
            'text' => 'required_without:html|string',
            'html' => 'required_without:text|string',
            'headers.*.key' => 'string',
            'headers.*.value' => 'string',
            'attachments.*.content' => 'required|string',
            'attachments.*.filename' => 'string',
            'attachments.*.contentType' => 'string',
            'attachments.*.cid' => 'string',
            'meta' => 'array',
            'sess' => 'string',
            'ip' => 'string',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator);
        }

        $args = [
            'user' => $params['user'],
        ];

        unset($params['user']);

        return Request::post(Uri::get('submission.submit', $args), $params);
    }
}