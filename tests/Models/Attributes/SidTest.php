<?php

namespace Adldap\Tests\Models\Attributes;

use Adldap\Tests\TestCase;
use Adldap\Models\Attributes\Sid;

class SidTest extends TestCase
{
    /** @test */
    public function can_be_converted_to_string()
    {
        $hex = '010500000000000515000000dcf4dc3b833d2b46828ba62800020000';

        $expected = (new Sid(hex2bin($hex)));

        $this->assertEquals(
            'S-1-5-21-1004336348-1177238915-682003330-512',
            $expected->getValue()
        );
    }

    /** @test */
    public function can_be_converted_to_binary()
    {
        $hex = '010500000000000515000000dcf4dc3b833d2b46828ba62800020000';
        $sid = 'S-1-5-21-1004336348-1177238915-682003330-512';

        $expected = (new Sid($sid));

        $this->assertEquals(hex2bin($hex), $expected->getBinary());
    }

    /** @test */
    public function can_convert_built_in_account_sid_from_binary()
    {
        $hex = '01020000000000052000000020020000';
        $sid = 'S-1-5-32-544';

        $expected = new Sid(hex2bin($hex));

        $this->assertEquals($sid, $expected->getValue());
    }

    /** @test */
    public function can_convert_builtin_account_sid_from_string()
    {
        $hex = '01020000000000052000000020020000';
        $sid = 'S-1-5-32-544';

        $expected = new Sid($sid);

        $this->assertEquals(hex2bin($hex), $expected->getBinary());
    }

    /** @test */
    public function can_convert_well_known_nobody_sid_from_binary()
    {
        $hex = '010100000000000000000000';
        $sid = 'S-1-0-0';

        $expected = new Sid(hex2bin($hex));

        $this->assertEquals($sid, $expected->getValue());
    }

    /** @test */
    public function can_convert_well_known_nobody_sid_from_string()
    {
        $hex = '010100000000000000000000';
        $sid = 'S-1-0-0';

        $expected = new Sid($sid);

        $this->assertEquals(hex2bin($hex), $expected->getBinary());
    }

    /** @test */
    public function can_convert_well_known_self_sid_from_binary()
    {
        $hex = '01010000000000050a000000';
        $sid = 'S-1-5-10';

        $expected = new Sid(hex2bin($hex));

        $this->assertEquals($sid, $expected->getValue());
    }

    /** @test */
    public function can_convert_well_known_self_sid_from_string()
    {
        $hex = '01010000000000050a000000';
        $sid = 'S-1-5-10';

        $expected = new Sid($sid);

        $this->assertEquals(hex2bin($hex), $expected->getBinary());
    }
}
