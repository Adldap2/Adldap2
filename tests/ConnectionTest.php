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

    public function testEscapeManual()
    {
        $ldap = $this->mock('Adldap\Connections\Ldap')->makePartial();

        $ldap->shouldAllowMockingProtectedMethods(true);

        $expected = '\74\65\73\74\69\6e\67\3d\2b\3c\3e\22\22\3b\3a\23\28\29*\5c\78\30\30';

        $result = $ldap->escapeManual('testing=+<>"";:#()*\x00', '*');

        $this->assertEquals($expected, $result);
    }

    public function testEscapeManualFilter()
    {
        $ldap = $this->mock('Adldap\Connections\Ldap')->makePartial();

        $ldap->shouldAllowMockingProtectedMethods(true);

        $expected = 'testing=+<>"";:#\28\29*\5cx00';

        $result = $ldap->escapeManual('testing=+<>"";:#()*\x00', '*', 1);

        $this->assertEquals($expected, $result);
    }

    public function testEscapeManualDn()
    {
        $ldap = $this->mock('Adldap\Connections\Ldap')->makePartial();

        $ldap->shouldAllowMockingProtectedMethods(true);

        $expected = 'testing\3d\2b\3c\3e\22\22\3b:\23()*\5cx00';

        $result = $ldap->escapeManual('testing=+<>"";:#()*\x00', '*', 2);

        $this->assertEquals($expected, $result);
    }

    public function testEscapeManualBothFilterAndDn()
    {
        $ldap = $this->mock('Adldap\Connections\Ldap')->makePartial();

        $ldap->shouldAllowMockingProtectedMethods(true);

        $expected = 'testing\3d\2b\3c\3e\22\22\3b:\23\5c28\5c29*\5c5cx00';

        $result = $ldap->escapeManual('testing=+<>"";:#()*\x00', '*', 3);

        $this->assertEquals($expected, $result);
    }
}