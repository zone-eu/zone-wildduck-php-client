<?php

namespace Zone\Wildduck\ApiOperations;

use Zone\Wildduck\Address;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Mailbox;
use Zone\Wildduck\User;
use Zone\Wildduck\Webhook;

/**
 * Trait for deletable resources. Adds a `delete()` method to the class.
 *
 * This trait should only be applied to classes that derive from WildduckObject.
 *
 * @deprecated
 */
trait Delete
{
    /**
     * @param array|null $params
     * @param array|string|null $opts
     * @return Address|Mailbox|User|Webhook the deleted resource
     *
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     * @throws RequestFailedException
     * @throws ValidationException
     *
     * @deprecated
     */
    public function delete(array|null $params = null, array|null|string $opts = null): Address|Mailbox|User|Webhook
    {
        self::_validateParams($params);

        $url = $this->instanceUrl();
        [$response, $opts] = $this->_request('delete', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }
}
