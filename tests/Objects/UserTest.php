<?php

namespace Adldap\Tests\Objects;

use Adldap\Objects\Ldap\User;
use Adldap\Tests\FunctionalTestCase;

class UserTest extends FunctionalTestCase
{
    /**
     * @return array
     */
    protected function stubbedUserAttributes()
    {
        return [
            'objectclass' => [
                'count' => 4,
                'top',
                'person',
                'organizationalPerson',
                'user',
            ],
            'userprincipalname' => [
                'count' => 1,
                'tuser@company.com',
            ],
            'samaccountname' => [
                'count' => 1,
                'tuser',
            ],
            'name' => [
                'count' => 1,
                'Test User',
            ],
            'cn' => [
                'count' => 1,
                'Test User',
            ],
            'sn' => [
                'count' => 1,
                'User'
            ],
            'givenname' => [
                'count' => 1,
                'Test',
            ],
            'distinguishedname' => [
                'count' => 1,
                'CN=Test User,OU=User Accounts,OU=Company,DC=corp,DC=company,DC=org'
            ],
            'whencreated' => [
                'count' => 1,
                '20150710214753.0Z',
            ],
            'whenchanged' => [
                'count' => 1,
                '20150710215229.0Z',
            ],
        ];
    }

    public function testUserConstruct()
    {
        $user = new User($this->stubbedUserAttributes(), $this->newConnectionMock());

        $this->assertEquals('CN=Test User,OU=User Accounts,OU=Company,DC=corp,DC=company,DC=org', $user->getDn());
        $this->assertEquals('tuser', $user->getAccountName());
        $this->assertEquals('Test', $user->getFirstName());
        $this->assertEquals('User', $user->getLastName());
        $this->assertEquals('tuser@company.com', $user->getEmail());
    }
}