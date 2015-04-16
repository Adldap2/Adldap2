<?php

namespace Adldap\Tests;

use Adldap\Connections\Ldap;

class ConnectionTest extends FunctionalTestCase
{
    /**
     * This tests that no exception is thrown when trying
     * to perform an LDAP method while suppressing errors.
     */
    public function testConnectionSuppressErrors()
    {
        $ldap = new Ldap();

        $ldap->suppressErrors();

        $this->assertFalse($ldap->bind('test', 'test'));
    }

    /**
     * This tests that an exception is thrown
     * when trying to perform an LDAP method while
     * showing errors.
     */
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