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

        $schemaAttributes = array(
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
            'pwdLastSet' => array(0),
            'department' => array('Department'),
            'description' => array('Description'),
            'displayName' => array('Display Name'),
            'mail' => array('Email'),
            'employeeId' => array('Employee ID'),
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

    /**
     * This tests that the adldap global search
     * method properly parses returned ldap results.
     */
    public function testAdldapSearch()
    {
        $ad = $this->newAdldapMock()->makePartial();

        $connection = $this->newConnectionMock();

        $ad->setLdapConnection($connection);

        $returnedLdapEntries = array(
            'count' => 3,
            0 => array(
                0 => 'distinguishedname',
                'count' => 1,
                'dn' => 'CN=Karen Berge,CN=admin,DC=corp,DC=Fabrikam,DC=COM',
                'distinguishedname' => array(
                    'count' => 1,
                    'CN=Karen Berge,CN=admin,DC=corp,DC=Fabrikam,DC=COM',
                ),
            ),
            1 => array(
                0 => 'distinguishedname',
                'count' => 1,
                'dn' => 'CN=Doe\, John,CN=admin,DC=corp,DC=Fabrikam,DC=COM',
                'distinguishedname' => array(
                    'count' => 1,
                    'CN=Doe\, John,CN=admin,DC=corp,DC=Fabrikam,DC=COM',
                ),
            ),
            2 => array(
                0 => 'cn',
                'cn' => array(
                    'count' => 1,
                    0 => 'Test',
                ),
                'distinguishedname' => array(
                    'count' => 1,
                    0 => 'CN=Bauman\, Steve,OU=Users,OU=Developers,OU=User Accounts,OU=Canada,DC=corp,DC=Fabrikam,DC=COM',
                ),
                1 => 'distinguishedname',
                'displayname' => array(
                    'count' => 1,
                    0 => 'Bauman, Steve'
                ),
                2 => 'displayname',
                'samaccountname' => array(
                    'count' => 1,
                    0 => 'stevebauman',
                ),
                3 => 'samaccountname',
                'count' => 4,
                'dn' => 'CN=Bauman\, Steve,OU=Users,OU=Developers,OU=User Accounts,OU=Canada,DC=corp,DC=Fabrikam,DC=COM'
            )
        );

        $explodedDnsToReturn = array(
            ldap_explode_dn($returnedLdapEntries[0]['dn'], 1),
            ldap_explode_dn($returnedLdapEntries[1]['dn'], 1),
            ldap_explode_dn($returnedLdapEntries[2]['dn'], 1)
        );

        $connection
            ->shouldReceive('escape')->once()->andReturn()
            ->shouldReceive('search')->once()->andReturn('resource')
            ->shouldReceive('getEntries')->once()->andReturn($returnedLdapEntries)
            ->shouldReceive('explodeDn')->times(3)->andReturnValues($explodedDnsToReturn)
            ->shouldReceive('close')->andReturn(true);

        $expectedResults = array(
            array(
                'dn' => 'CN=Karen Berge,CN=admin,DC=corp,DC=Fabrikam,DC=COM',
                'dn_array' => array(
                    'count' => 5,
                    0 => 'Karen Berge',
                    1 => 'admin',
                    2 => 'corp',
                    3 => 'Fabrikam',
                    4 => 'COM',
                ),
            ),
            array(
                'dn' => 'CN=Doe\, John,CN=admin,DC=corp,DC=Fabrikam,DC=COM',
                'dn_array' => array(
                    'count' => 5,
                    0 => 'Doe\2C John',
                    1 => 'admin',
                    2 => 'corp',
                    3 => 'Fabrikam',
                    4 => 'COM',
                ),
            ),
            array(
                'cn' => 'Test',
                'displayname' => 'Bauman, Steve',
                'samaccountname' => 'stevebauman',
                'dn' => 'CN=Bauman\, Steve,OU=Users,OU=Developers,OU=User Accounts,OU=Canada,DC=corp,DC=Fabrikam,DC=COM',
                'dn_array' => array(
                    'count' => 8,
                    0 => 'Bauman\2C Steve',
                    1 => 'Users',
                    2 => 'Developers',
                    3 => 'User Accounts',
                    4 => 'Canada',
                    5 => 'corp',
                    6 => 'Fabrikam',
                    7 => 'COM',
                ),
            ),
        );

        $actualResults = $ad->search()->all();

        $this->assertEquals($expectedResults, $actualResults);
    }
}