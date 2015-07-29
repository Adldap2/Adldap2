<?php

namespace Adldap\Tests\Classes;

use Adldap\Classes\Utilities;
use Adldap\Tests\UnitTestCase;

class UtilitiesTest extends UnitTestCase
{
    public function testExplodeDn()
    {
        $dn = 'cn=Testing,ou=Folder,dc=corp,dc=org';

        $split = Utilities::explodeDn($dn);

        $expected = [
            'count' => 4,
            0 => 'Testing',
            1 => 'Folder',
            2 => 'corp',
            3 => 'org',
        ];

        $this->assertEquals($expected, $split);
    }

    public function testEscapeManual()
    {
        $mockedUtilities = $this->mock('Adldap\Classes\Utilities')->makePartial();

        $mockedUtilities->shouldAllowMockingProtectedMethods();

        $escape = '<>!=,#$%^testing';

        $result = $mockedUtilities->escapeManual($escape);

        $expected = '\3c\3e\21\3d\2c\23\24\25\5e\74\65\73\74\69\6e\67';

        $this->assertEquals($expected, $result);
    }
}
