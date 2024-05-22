<?php

namespace Zone\Wildduck\Service;

use Override;
use Zone\Wildduck\Event;
use Zone\Wildduck\Collection2;

class EventService extends AbstractService
{
    public function all(string $user, array|null $params = null, array|null $opts = null): Collection2
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/authlog', $user), $params, $opts);
    }

    public function get(string $user, string $event, array|null $params = null, array|null $opts = null): Event
    {
        return $this->request('get', $this->buildPath('/users/%s/authlog/%s', $user, $event), $params, $opts);
    }

	#[Override]
	protected function getObjectName(): string
	{
		return Event::OBJECT_NAME;
	}
}
