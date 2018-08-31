<?php

namespace Wildduck\Validation\Rules;

use Illuminate\Validation\Rule;

class SubmissionReferenceObject extends Rule
{

    public function passes($attribute, $value)
    {
        if (!$value) {
            return true;
        }

        if ($value['mailbox'] && $value['id'] && $value['action']) {
            return true;
        }

        return false;
    }

    public function message()
    {
        return ':attribute must have mailbox, id and action defined if present';
    }
}