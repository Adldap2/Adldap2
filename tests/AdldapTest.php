<?php

namespace adLDAP\Tests;

use adLDAP\adLDAP;

class AdldapTest extends FunctionalTestCase
{
    public function testAdldapConstructDomainControllerFailure()
    {
        $this->setExpectedException('adLDAP\adLDAPException');

        $config = array(
            'domain_controllers' => 'test'
        );

        new adLDAP($config);
    }

    public function testAdldapConstructLdapNotSupportedFailure()
    {
        $this->setExpectedException('adLDAP\adLDAPException');

        $connection = $this->mock('adLDAP\Connections\Ldap');

        $connection->shouldReceive('isSupported')->andReturn(false);

        new adLDAP(array(), $connection);
    }
}