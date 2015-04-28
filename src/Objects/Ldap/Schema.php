<?php

namespace Adldap\Objects\Ldap;

use Adldap\Objects\Schema as AdldapSchema;
use Adldap\Objects\AbstractObject;

/**
 * Class LdapSchema.
 */
class Schema extends AbstractObject
{
    /**
     * Constructor.
     *
     * Accepts a Schema object and constructs an LDAP
     * schema using it's attributes.
     *
     * @param AdldapSchema $schema
     */
    public function __construct(AdldapSchema $schema)
    {
        $this->constructLdapSchema($schema);
    }

    /**
     * When setting attributes, we need to assign them
     * in their own int(0) key due to LDAP parsing.
     *
     * @param int|string $key
     * @param mixed      $value
     *
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key][0] = $value;

        return $this;
    }

    /**
     * Constructs a schema compatible with the LDAP protocol.
     *
     * @param AdldapSchema $schema
     *
     * @return $this
     */
    private function constructLdapSchema($schema)
    {
        // Set all the LDAP attributes
        $this->setAttribute('l', $schema->getAttribute('address_city'));

        $this->setAttribute('postalCode', $schema->getAttribute('address_code'));

        $this->setAttribute('c', $schema->getAttribute('address_country'));

        $this->setAttribute('postOfficeBox', $schema->getAttribute('address_pobox'));

        $this->setAttribute('st', $schema->getAttribute('address_state'));

        $this->setAttribute('streetAddress', $schema->getAttribute('address_street'));

        $this->setAttribute('company', $schema->getAttribute('company'));

        $this->setAttribute('pwdLastSet', ($schema->getAttribute('change_password') == 0) ? -1 : 0);

        $this->setAttribute('department', $schema->getAttribute('department'));

        $this->setAttribute('description', $schema->getAttribute('description'));

        $this->setAttribute('displayName', $schema->getAttribute('display_name'));

        $this->setAttribute('mail', $schema->getAttribute('email'));

        $this->setAttribute('employeeId', $schema->getAttribute('employee_id'));

        $this->setAttribute('accountExpires', $schema->getAttribute('expires'));

        $this->setAttribute('givenName', $schema->getAttribute('firstname'));

        $this->setAttribute('homeDirectory', $schema->getAttribute('home_directory'));

        $this->setAttribute('homeDrive', $schema->getAttribute('home_drive'));

        $this->setAttribute('initials', $schema->getAttribute('initials'));

        $this->setAttribute('userPrincipalName', $schema->getAttribute('logon_name'));

        $this->setAttribute('manager', $schema->getAttribute('manager'));

        $this->setAttribute('physicalDeliveryOfficeName', $schema->getAttribute('office'));

        $this->setAttribute('unicodePwd', $schema->getAttribute('password'));

        $this->setAttribute('profilepath', $schema->getAttribute('profile_path'));

        $this->setAttribute('scriptPath', $schema->getAttribute('script_path'));

        $this->setAttribute('sn', $schema->getAttribute('surname'));

        $this->setAttribute('title', $schema->getAttribute('title'));

        $this->setAttribute('telephoneNumber', $schema->getAttribute('telephone'));

        $this->setAttribute('mobile', $schema->getAttribute('mobile'));

        $this->setAttribute('pager', $schema->getAttribute('pager'));

        $this->setAttribute('ipphone', $schema->getAttribute('ipphone'));

        $this->setAttribute('wWWHomePage', $schema->getAttribute('web_page'));

        $this->setAttribute('facsimileTelephoneNumber', $schema->getAttribute('fax'));

        $this->setAttribute('userAccountControl', $schema->getAttribute('enabled'));

        $this->setAttribute('homephone', $schema->getAttribute('homephone'));

        // Distribution List specific schema
        $this->setAttribute('dlMemSubmitPerms', $schema->getAttribute('group_sendpermission'));

        $this->setAttribute('dlMemRejectPerms', $schema->getAttribute('group_rejectpermission'));

        // Exchange Schema
        $this->setAttribute('homeMDB', $schema->getAttribute('exchange_homemdb'));

        $this->setAttribute('mailNickname', $schema->getAttribute('exchange_mailnickname'));

        $this->setAttribute('proxyAddresses', $schema->getAttribute('exchange_proxyaddress'));

        $this->setAttribute('mDBUseDefaults', $schema->getAttribute('exchange_usedefaults'));

        $this->setAttribute('msExchPoliciesExcluded', $schema->getAttribute('exchange_policyexclude'));

        $this->setAttribute('msExchPoliciesIncluded', $schema->getAttribute('exchange_policyinclude'));

        $this->setAttribute('showInAddressBook', $schema->getAttribute('exchange_addressbook'));

        $this->setAttribute('altRecipient', $schema->getAttribute('exchange_altrecipient'));

        $this->setAttribute('deliverAndRedirect', $schema->getAttribute('exchange_deliverandredirect'));

        // This schema is designed for contacts
        $this->setAttribute('msExchHideFromAddressLists', $schema->getAttribute('exchange_hidefromlists'));

        $this->setAttribute('targetAddress', $schema->getAttribute('contact_email'));

        return $this;
    }
}
