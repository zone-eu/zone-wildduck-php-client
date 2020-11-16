<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\File;

class StorageService extends AbstractService
{

    public function delete(string $user, string $file, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/users/%s/storage/%s', $user, $file), $params, $opts);
    }

    public function download(string $user, string $file, $params = null, $opts = null)
    {
        $opts['raw'] = true;
        return $this->request('get', $this->buildPath('/users/%s/storage/%s', $user, $file), $params, $opts);
    }

    public function list(string $user, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/storage', $user), $params, $opts);
    }

    public function upload(string $user, $filename, $contentType, $content, $opts = null)
    {
        return $this->file('post', $this->buildPath('/users/%s/storage?filename=%s&contentType=%', $user, $filename, $contentType), $content, $opts);
    }

    protected function getObjectName()
    {
        return File::OBJECT_NAME;
    }
}