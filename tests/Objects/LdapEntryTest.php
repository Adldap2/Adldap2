<?php

namespace Adldap\Tests\Objects;

use Adldap\Objects\LdapEntry;
use Adldap\Tests\FunctionalTestCase;

class LdapEntryTest extends FunctionalTestCase
{
    public function testEntryConstruct()
    {
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

        $connection = $this->newConnectionMock();

        $connection
            ->shouldReceive('explodeDn')->times(3)->andReturnValues($explodedDnsToReturn)
            ->shouldReceive('close')->andReturn(true);

        $expectedResults = array(
            array(
                'distinguishedname' => 'CN=Karen Berge,CN=admin,DC=corp,DC=Fabrikam,DC=COM',
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
                'distinguishedname' => 'CN=Doe\, John,CN=admin,DC=corp,DC=Fabrikam,DC=COM',
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
                'distinguishedname' => 'CN=Bauman\, Steve,OU=Users,OU=Developers,OU=User Accounts,OU=Canada,DC=corp,DC=Fabrikam,DC=COM',
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

        $entries = array();

        for ($i = 0; $i < $returnedLdapEntries["count"]; $i++)
        {
            $entry = new LdapEntry($returnedLdapEntries[$i], $connection);

            $entries[] = $entry->getAttributes();
        }

        $this->assertEquals($expectedResults, $entries);
    }
}