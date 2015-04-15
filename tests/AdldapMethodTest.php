<?php

namespace Adldap\Tests;

class AdldapMethodTest extends FunctionalTestCase
{
    protected function newAdldapMock()
    {
        return $this->mock('Adldap\Adldap');
    }

    public function testAdldapSetPort()
    {
        $ad = $this->newAdldapMock()->makePartial();

        $ad->setPort(500);

        $this->assertEquals(500, $ad->getPort());
    }

    public function testAdldapSetPortFailure()
    {
        $ad = $this->newAdldapMock()->makePartial();

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $ad->setPort('Test');
    }

    public function testAdldapSetDomainControllers()
    {
        $ad = $this->newAdldapMock()->makePartial();

        $controllers = array('dc1', 'dc2');

        $ad->setDomainControllers($controllers);

        $this->assertEquals($controllers, $ad->getDomainControllers());
    }

    /**
     * This tests that the type hinted setDomainControllers method
     * fails when supplying something other than an array.
     */
    public function testAdldapSetDomainControllersFailure()
    {
        $ad = $this->newAdldapMock()->makePartial();

        $controllers = 'String';

        try
        {
            $ad->setDomainControllers($controllers);

            $passes = false;
        } catch(\Exception $e)
        {
            $passes = true;
        }

        $this->assertTrue($passes);
    }

    public function testAdldapSetLdapConnection()
    {
        $ad = $this->newAdldapMock()->makePartial();

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('close')->andReturn(true);

        $ad->setLdapConnection($connection);

        $this->assertEquals($connection, $ad->getLdapConnection());
    }

    public function testAdldapSetLdapConnectionFailure()
    {
        $ad = $this->newAdldapMock()->makePartial();

        $connection = 'Invalid Connection';

        try
        {
            $ad->setLdapConnection($connection);

            $passes = false;
        } catch (\Exception $e)
        {
            $passes = true;
        }

        $this->assertTrue($passes);
    }

    /**
     * This tests that all the inserted schema attributes are
     * correctly applied and returned using the ldapSchema($attributes)
     * method.
     */
    public function testAdldapSchema()
    {
        $ad = $this->newAdldapMock()->makePartial();

        $schemaAttributes = array(
            'address_city' => 'Address City',
            'address_code' => 'Address Code',
            'address_country' => 'Address Country',
            'address_pobox' => 'Address PBOX',
            'address_state' => 'Address State',
            'address_street' => 'Address Street',
            'company' => 'Company',
            'change_password' => 'Change Password',
            'department' => 'Department',
            'description' => 'Description',
            'display_name' => 'Display Name',
            'email' => 'Email',
            'expires' => 'Expires',
            'firstname' => 'First Name',
            'home_directory' => 'Home Directory',
            'home_drive' => 'Home Drive',
            'initials' => 'Initials',
            'logon_name' => 'Logon Name',
            'manager' => 'Manager',
            'office' => 'Office',
            'password' => 'Password',
            'profile_path' => 'Profile Path',
            'script_path' => 'Script Path',
            'surname' => 'Surname',
            'title' => 'Title',
            'telephone' => 'Telephone',
            'mobile' => 'Mobile',
            'pager' => 'Pager',
            'ipphone' => 'IP Phone',
            'web_page' => 'Web Page',
            'fax' => 'Fax',
            'enabled' => true,
            'homephone' => 'Home Phone',
            'group_sendpermission' => 'Group Send Permission',
            'group_rejectpermission' => 'Group Reject Permission',

            'exchange_homemdb' => 'Exchange Home',
            'exchange_mailnickname' => 'Exchange Nickname',
            'exchange_proxyaddress' => 'Exchange Proxy',
            'exchange_usedefaults' => 'Exchange Use Defaults',
            'exchange_policyexclude' => 'Exchange Policy Exclude',
            'exchange_policyinclude' => 'Exchange Policy Include',
            'exchange_addressbook' => 'Exchange Address Book',
            'exchange_altrecipient' => 'Exchange Alt Recipient',
            'exchange_deliverandredirect' => 'Exchange Deliver And Redirect',
            'exchange_hidefromlists' => true,
            'contact_email' => 'Contact Email',
        );

        $ldapSchema = $ad->ldapSchema($schemaAttributes);

        $expectedSchema = array(
            'l' => array('Address City'),
            'postalCode' => array('Address Code'),
            'c' => array('Address Country'),
            'postOfficeBox' => array('Address PBOX'),
            'st' => array('Address State'),
            'streetAddress' => array('Address Street'),
            'company' => array('Company'),
            'pwdLastSet' => array('Change Password'),
            'department' => array('Department'),
            'description' => array('Description'),
            'displayName' => array('Display Name'),
            'mail' => array('Email'),
            'accountExpires' => array('Expires'),
            'givenName' => array('First Name'),
            'homeDirectory' => array('Home Directory'),
            'homeDrive' => array('Home Drive'),
            'initials' => array('Initials'),
            'userPrincipalName' => array('Logon Name'),
            'manager' => array('Manager'),
            'physicalDeliveryOfficeName' => array('Office'),
            'unicodePwd' => array('Password'),
            'profilepath' => array('Profile Path'),
            'scriptPath' => array('Script Path'),
            'sn' => array('Surname'),
            'title' => array('Title'),
            'telephoneNumber' => array('Telephone'),
            'mobile' => array('Mobile'),
            'pager' => array('Pager'),
            'ipphone' => array('IP Phone'),
            'wWWHomePage' => array('Web Page'),
            'facsimileTelephoneNumber' => array('Fax'),
            'userAccountControl' => array(true),
            'homephone' => array('Home Phone'),
            'dlMemSubmitPerms' => array('Group Send Permission'),
            'dlMemRejectPerms' => array('Group Reject Permission'),
            'homeMDB' => array('Exchange Home'),
            'mailNickname' => array('Exchange Nickname'),
            'proxyAddresses' => array('Exchange Proxy'),
            'mDBUseDefaults' => array('Exchange Use Defaults'),
            'msExchPoliciesExcluded' => array('Exchange Policy Exclude'),
            'msExchPoliciesIncluded' => array('Exchange Policy Include'),
            'showInAddressBook' => array('Exchange Address Book'),
            'altRecipient' => array('Exchange Alt Recipient'),
            'deliverAndRedirect' => array('Exchange Deliver And Redirect'),
            'msExchHideFromAddressLists' => array(true),
            'targetAddress' => array('Contact Email'),
        );

        $this->assertEquals($expectedSchema, $ldapSchema);
    }
}