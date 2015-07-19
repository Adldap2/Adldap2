<?php

namespace Adldap\Classes;

use Adldap\Objects\Contact;

class Contacts extends AbstractQueryable
{
    /**
     * The contacts object class name.
     *
     * @var string
     */
    public $objectClass = 'contact';

    /**
     * Mail enable a contact. Allows email to be sent to them through Exchange.
     *
     * @param string $contactName  The contacts name
     * @param string $emailAddress The contacts email address
     * @param null   $mailNickname
     *
     * @return bool
     */
    public function contactMailEnable($contactName, $emailAddress, $mailNickname = null)
    {
        $contactDn = $this->dn($contactName);

        if ($contactDn) {
            return $this->adldap->exchange()->contactMailEnable($contactDn, $emailAddress, $mailNickname);
        }

        return false;
    }
}
