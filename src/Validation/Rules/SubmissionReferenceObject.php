<?php

namespace Wildduck\Validation\Rules;

use Illuminate\Validation\Rule;

class SubmissionReferenceObject extends Rule
{

    public function passes($attribute, $value)
    {
        dd($attribute, $value);
    }

    public function message()
    {
        return '';
    }
}