<?php

namespace Adldap\Tests;

use Adldap\Utilities;

class UtilitiesTest extends TestCase
{
    public function test_explode_dn()
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

    public function test_escape_manual()
    {
        $mockedUtilities = $this->mock('Adldap\Utilities')->makePartial();

        $mockedUtilities->shouldAllowMockingProtectedMethods();

        $mockedUtilities->shouldReceive('isEscapingSupported')->andReturn(false);

        $escape = '<>!=,#$%^testing';

        $result = $mockedUtilities->escape($escape);

        $expected = '\3c\3e\21\3d\2c\23\24\25\5e\74\65\73\74\69\6e\67';

        $this->assertEquals($expected, $result);
    }

    public function test_escape_manual_with_ignore()
    {
        $mockedUtilities = $this->mock('Adldap\Utilities')->makePartial();

        $mockedUtilities->shouldAllowMockingProtectedMethods();

        $mockedUtilities->shouldReceive('isEscapingSupported')->andReturn(false);

        $escape = '**<>!=,#$%^testing';

        $ignore = '*<>!';

        $result = $mockedUtilities->escape($escape, $ignore);

        $expected = '**<>!\3d\2c\23\24\25\5e\74\65\73\74\69\6e\67';

        $this->assertEquals($expected, $result);
    }

    public function test_escape_manual_with_ignore_and_flag_two()
    {
        $mockedUtilities = $this->mock('Adldap\Utilities')->makePartial();

        $mockedUtilities->shouldAllowMockingProtectedMethods();

        $mockedUtilities->shouldReceive('isEscapingSupported')->andReturn(false);

        $escape = '**<>!=,#$%^testing';

        $ignore = '*';

        // Flag integer 2 means we're escaping a value for a distinguished name.
        $flag = 2;

        $result = $mockedUtilities->escape($escape, $ignore, $flag);

        $expected = '**\3c\3e!\3d\2c\23$%^testing';

        $this->assertEquals($expected, $result);
    }

    public function test_escape_manual_with_ignore_and_flag_three()
    {
        $mockedUtilities = $this->mock('Adldap\Utilities')->makePartial();

        $mockedUtilities->shouldAllowMockingProtectedMethods();

        $mockedUtilities->shouldReceive('isEscapingSupported')->andReturn(false);

        $escape = '*^&.:foo()-=';

        $ignore = '*';

        $flag = 3;

        $result = $mockedUtilities->escape($escape, $ignore, $flag);

        $expected = '*^&.:foo\28\29-\3d';

        $this->assertEquals($expected, $result);
    }

    public function test_unescape()
    {
        $unescaped = 'testing';

        $mockedUtilities = $this->mock('Adldap\Utilities')->makePartial();

        $mockedUtilities->shouldAllowMockingProtectedMethods();

        $escaped = $mockedUtilities->escape($unescaped);

        $this->assertEquals($unescaped, Utilities::unescape($escaped));
    }

    public function test_encode_password()
    {
        $password = 'password';

        $encoded = Utilities::encodePassword($password);

        $expected = '2200700061007300730077006f00720064002200';

        $this->assertEquals($expected, bin2hex($encoded));
    }

    public function test_is_valid_sid()
    {
        $sid1 = 'S-1-5-21-3623811015-3361044348-30300820-1013';
        $sid2 = 'S-1-5-21-362381101-336104434-3030082-101';

        $this->assertTrue(Utilities::isValidSid($sid1));
        $this->assertTrue(Utilities::isValidSid($sid2));

        $this->assertFalse(Utilities::isValidSid('Invalid SID'));
    }

    public function test_binary_sid_to_string()
    {
        $sid = '\01\05\00\00\00\00\00\05\15\00\00\00\dc\f4\dc\3b\83\3d\2b\46\82\8b\a6\28\00\02\00\00';

        $expected = 'S-1-5-21-1004336348-1177238915-682003330-512';

        $this->assertEquals($expected, Utilities::binarySidToString(pack('H*', str_replace('\\', '', $sid))));
        $this->assertNull(Utilities::binaryGuidToString(null));
        $this->assertNull(Utilities::binaryGuidToString('  '));
    }

    public function test_binary_guid_to_string()
    {
        $guid = '\d0\b4\0d\27\9d\24\a7\46\9c\c5\eb\69\5d\9a\f9\ac';

        $expected = '270db4d0-249d-46a7-9cc5-eb695d9af9ac';

        $this->assertEquals($expected, Utilities::binaryGuidToString(pack('H*', str_replace('\\', '', $guid))));
        $this->assertNull(Utilities::binaryGuidToString(null));
        $this->assertNull(Utilities::binaryGuidToString('  '));
    }
}
