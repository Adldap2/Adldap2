<?php

namespace Adldap\Classes;

use Adldap\Objects\Contact;

/**
 * Ldap Contacts Management.
 *
 * Class AdldapContacts
 */
class AdldapContacts extends AbstractAdldapQueryable
{
    /**
     * The contacts object class name.
     *
     * @var string
     */
    public $objectClass = 'contact';

    /**
     * Create a contact.
     *
     * @param array $attributes The attributes to set to the contact
     *
     * @return bool|string
     */
    public function create(array $attributes)
    {
        $contact = new Contact($attributes);

        $contact->validateRequired();

        // Translate the schema
        $add = $this->adldap->ldapSchema($attributes);

        // Set the cn to the contacts display name
        $add['cn'][0] = $contact->{'display_name'};

        $add['objectclass'][0] = 'top';
        $add['objectclass'][1] = 'person';
        $add['objectclass'][2] = 'organizationalPerson';
        $add['objectclass'][3] = 'contact';

        if (!$contact->hasAttribute('exchange_hidefromlists')) {
            $add['msExchHideFromAddressLists'][0] = 'TRUE';
        }

        // Determine the container
        $attributes['container'] = array_reverse($attributes['container']);

        $container = 'OU='.implode(',OU=', $attributes['container']);

        $dn = 'CN='.$this->adldap->utilities()->escapeCharacters($add['cn'][0]).', '.$container.','.$this->adldap->getBaseDn();

        // Add the entry
        return $this->connection->add($dn, $add);
    }

    /**
     * Modify a contact. Note if you set the enabled
     * attribute you must not specify any other attributes.
     *
     * @param string $contactName The contact to query
     * @param array  $attributes  The attributes to modify
     *
     * @return bool|string
     */
    public function modify($contactName, $attributes)
    {
        $contactDn = $this->dn($contactName);

        if ($contactDn) {
            // Translate the update to the LDAP schema
            $mod = $this->adldap->ldapSchema($attributes);

            // Check to see if this is an enabled status update
            if (!$mod) {
                return false;
            }

            // Do the update
            return $this->connection->modify($contactDn, $mod);
        }

        return false;
    }

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
