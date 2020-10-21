<?php

namespace Zone\Wildduck\Service;

class SubmissionService extends AbstractService
{

    public function submit(string $user, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/users/%s/submit', $user), $params, $opts);
    }
}