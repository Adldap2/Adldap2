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

        $controllers = ['dc1', 'dc2'];

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

    public function testAdldapNewComputersClass()
    {
        $ad = $this->newAdldapMock()->makePartial();

        $this->assertEquals('Adldap\Classes\AdldapComputers', get_class($ad->computer()));
    }

    public function testAdldapNewContactsClass()
    {
        $ad = $this->newAdldapMock()->makePartial();

        $this->assertEquals('Adldap\Classes\AdldapContacts', get_class($ad->contact()));
    }

    public function testAdldapNewExchangeClass()
    {
        $ad = $this->newAdldapMock()->makePartial();

        $this->assertEquals('Adldap\Classes\AdldapExchange', get_class($ad->exchange()));
    }

    public function testAdldapNewFoldersClass()
    {
        $ad = $this->newAdldapMock()->makePartial();

        $this->assertEquals('Adldap\Classes\AdldapFolders', get_class($ad->folder()));
    }

    public function testAdldapNewGroupsClass()
    {
        $ad = $this->newAdldapMock()->makePartial();

        $this->assertEquals('Adldap\Classes\AdldapGroups', get_class($ad->group()));
    }

    public function testAdldapNewUsersClass()
    {
        $ad = $this->newAdldapMock()->makePartial();

        $this->assertEquals('Adldap\Classes\AdldapUsers', get_class($ad->user()));
    }

    public function testAdldapNewUtilityClass()
    {
        $ad = $this->newAdldapMock()->makePartial();

        $this->assertEquals('Adldap\Classes\AdldapUtils', get_class($ad->utilities()));
    }

    /**
     * This tests that all the inserted schema attributes are
     * correctly applied and returned using the ldapSchema($attributes)
     * method.
     */
    public function testAdldapSchema()
    {
        $ad = $this->newAdldapMock()->makePartial();

        $schemaAttributes = [
            'address_city' => 'Address City',
            'address_code' => 'Address Code',
            'address_country' => 'Address Country',
            'address_pobox' => 'Address PBOX',
            'address_state' => 'Address State',
            'address_street' => 'Address Street',
            'company' => 'Company',
            'change_password' => true,
            'department' => 'Department',
            'description' => 'Description',
            'display_name' => 'Display Name',
            'email' => 'Email',
            'employee_id' => 'Employee ID',
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
        ];

        $ldapSchema = $ad->ldapSchema($schemaAttributes);

        $expectedSchema = [
            'l' => ['Address City'],
            'postalCode' => ['Address Code'],
            'c' => ['Address Country'],
            'postOfficeBox' => ['Address PBOX'],
            'st' => ['Address State'],
            'streetAddress' => ['Address Street'],
            'company' => ['Company'],
            'pwdLastSet' => [0],
            'department' => ['Department'],
            'description' => ['Description'],
            'displayName' => ['Display Name'],
            'mail' => ['Email'],
            'employeeId' => ['Employee ID'],
            'accountExpires' => ['Expires'],
            'givenName' => ['First Name'],
            'homeDirectory' => ['Home Directory'],
            'homeDrive' => ['Home Drive'],
            'initials' => ['Initials'],
            'userPrincipalName' => ['Logon Name'],
            'manager' => ['Manager'],
            'physicalDeliveryOfficeName' => ['Office'],
            'unicodePwd' => ['Password'],
            'profilepath' => ['Profile Path'],
            'scriptPath' => ['Script Path'],
            'sn' => ['Surname'],
            'title' => ['Title'],
            'telephoneNumber' => ['Telephone'],
            'mobile' => ['Mobile'],
            'pager' => ['Pager'],
            'ipphone' => ['IP Phone'],
            'wWWHomePage' => ['Web Page'],
            'facsimileTelephoneNumber' => ['Fax'],
            'userAccountControl' => [true],
            'homephone' => ['Home Phone'],
            'dlMemSubmitPerms' => ['Group Send Permission'],
            'dlMemRejectPerms' => ['Group Reject Permission'],
            'homeMDB' => ['Exchange Home'],
            'mailNickname' => ['Exchange Nickname'],
            'proxyAddresses' => ['Exchange Proxy'],
            'mDBUseDefaults' => ['Exchange Use Defaults'],
            'msExchPoliciesExcluded' => ['Exchange Policy Exclude'],
            'msExchPoliciesIncluded' => ['Exchange Policy Include'],
            'showInAddressBook' => ['Exchange Address Book'],
            'altRecipient' => ['Exchange Alt Recipient'],
            'deliverAndRedirect' => ['Exchange Deliver And Redirect'],
            'msExchHideFromAddressLists' => [true],
            'targetAddress' => ['Contact Email'],
        ];

        $this->assertEquals($expectedSchema, $ldapSchema);
    }
}