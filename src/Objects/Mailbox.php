<?php

namespace Adldap\Objects;

use Adldap\Exceptions\AdldapException;

/**
 * Class Mailbox.
 */
class Mailbox extends AbstractObject
{
    /**
     * The mandatory attributes.
     *
     * @var array
     */
    protected $required = [
        'username',
        'storageGroup',
        'emailAddress',
    ];

    /**
     * Validates the objects required attributes.
     *
     * @param array $only
     *
     * @return bool
     *
     * @throws AdldapException
     */
    public function validateRequired($only = [])
    {
        parent::validateRequired();

        if (!is_array($this->getAttribute('storageGroup'))) {
            $message = 'Storage Group attribute must be an array';

            throw new AdldapException($message);
        }

        return true;
    }

    /**
     * Returns the Mailboxes attributes to an LDAP compatible array.
     *
     * @return array
     */
    public function toLdapArray()
    {
        return [
            'exchange_homemdb' => $this->container.','.$this->baseDn,
            'exchange_proxyaddress' => 'SMTP:'.$this->emailAddress,
            'exchange_mailnickname' => $this->mailNickname,
            'exchange_usedefaults' => $this->mdbUseDefaults,
        ];
    }
}
