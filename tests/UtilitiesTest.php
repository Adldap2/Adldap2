<?php

namespace Adldap\Tests;

use Adldap\Utilities;
use Adldap\Tests\UnitTestCase;

class UtilitiesTest extends UnitTestCase
{
    public function testExplodeDn()
    {
        $dn = 'cn=Testing,ou=Folder,dc=corp,dc=org';

        $split = Utilities::explodeDn($dn);

        $expected = [
            'count' => 4,
            0       => 'Testing',
            1       => 'Folder',
            2       => 'corp',
            3       => 'org',
        ];

        $this->assertEquals($expected, $split);
    }

    public function testEscapeManual()
    {
        $mockedUtilities = $this->mock('Adldap\Utilities')->makePartial();

        $mockedUtilities->shouldAllowMockingProtectedMethods();

        $escape = '<>!=,#$%^testing';

        $result = $mockedUtilities->escapeManual($escape);

        $expected = '\3c\3e\21\3d\2c\23\24\25\5e\74\65\73\74\69\6e\67';

        $this->assertEquals($expected, $result);
    }

    public function testEscapeManualWithIgnore()
    {
        $mockedUtilities = $this->mock('Adldap\Utilities')->makePartial();

        $mockedUtilities->shouldAllowMockingProtectedMethods();

        $escape = '**<>!=,#$%^testing';

        $ignore = '*<>!';

        $result = $mockedUtilities->escapeManual($escape, $ignore);

        $expected = '**<>!\3d\2c\23\24\25\5e\74\65\73\74\69\6e\67';

        $this->assertEquals($expected, $result);
    }

    public function testEscapeManualWithIgnoreAndFlagTwo()
    {
        $mockedUtilities = $this->mock('Adldap\Utilities')->makePartial();

        $mockedUtilities->shouldAllowMockingProtectedMethods();

        $escape = '**<>!=,#$%^testing';

        $ignore = '*';

        // Flag integer 2 means we're escaping a value for a distinguished name.
        $flag = 2;

        $result = $mockedUtilities->escapeManual($escape, $ignore, $flag);

        $expected = '**\3c\3e!\3d\2c\23$%^testing';

        $this->assertEquals($expected, $result);
    }

    public function testEscapeManualWithIgnoreAndFlagThree()
    {
        $mockedUtilities = $this->mock('Adldap\Utilities')->makePartial();

        $mockedUtilities->shouldAllowMockingProtectedMethods();

        $escape = '*^&.:foo()-=';

        $ignore = '*';

        $flag = 3;

        $result = $mockedUtilities->escapeManual($escape, $ignore, $flag);

        $expected = '*^&.:foo\28\29-\3d';

        $this->assertEquals($expected, $result);
    }

    public function testUnescape()
    {
        $unescaped = 'testing';

        $mockedUtilities = $this->mock('Adldap\Utilities')->makePartial();

        $mockedUtilities->shouldAllowMockingProtectedMethods();

        $escaped = $mockedUtilities->escapeManual($unescaped);

        $this->assertEquals($unescaped, Utilities::unescape($escaped));
    }

    public function testEncodePassword()
    {
        $password = 'password';

        $encoded = Utilities::encodePassword($password);

        $expected = '2200700061007300730077006f00720064002200';

        $this->assertEquals($expected, bin2hex($encoded));
    }
}
