<?php

namespace Adldap\Tests;

use Adldap\Connections\Ldap;

class ConnectionTest extends FunctionalTestCase
{
    public function testConnectionSuppressErrors()
    {
        $ldap = new Ldap();

        $ldap->suppressErrors();

        $this->assertFalse($ldap->bind('test', 'test'));
    }

    public function testConnectionShowErrors()
    {
        $ldap = new Ldap();

        $ldap->showErrors();

        $ldap->connect('test');

        try
        {
            $ldap->bind('test', 'test');

            $passes = false;
        } catch(\Exception $e)
        {
            $passes = true;
        }

        $this->assertTrue($passes);
    }
}