<?php

namespace Adldap\Objects;

/**
 * Class Mailbox
 * @package Adldap\Objects
 */
class Mailbox extends AbstractObject
{
    /**
     * The mandatory attributes.
     *
     * @var array
     */
    protected $required = array(
        'username',
        'storageGroup',
        'emailAddress',
    );

    /**
     * Returns the Mailboxes attributes to an LDAP compatible array.
     *
     * @return array
     */
    public function toLdapArray()
    {
        return array(
            'exchange_homemdb' => $this->container . "," . $this->baseDn,
            'exchange_proxyaddress' => 'SMTP:' . $this->emailAddress,
            'exchange_mailnickname' => $this->mailNickname,
            'exchange_usedefaults' => $this->mdbUseDefaults,
        );
    }
}
