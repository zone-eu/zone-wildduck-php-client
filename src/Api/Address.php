<?php

namespace Wildduck\Api;

use Illuminate\Validation\Validator;
use Wildduck\Exceptions\InvalidRequestException;

class Address
{

    public function create($params)
    {
        $validator = Validator::make($params, [
            'user' => 'required|string',
            'address' => 'required|email',
            'name' => 'sometimes|string',
            'tags' => 'sometimes|array',
            'main' => 'sometimes|boolean',
            'allowWildcard' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException($validator->errors()->all());
        }


    }
}
