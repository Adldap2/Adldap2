<?php

namespace Adldap\Tests\Models\Attributes;

use Adldap\Tests\TestCase;
use Adldap\Models\Attributes\Guid;

class GuidTest extends TestCase
{
    public function test_can_convert_guid_from_string()
    {
        $guid = '270db4d0-249d-46a7-9cc5-eb695d9af9ac';

        $expected = new Guid($guid);

        $this->assertEquals(
            'd0b40d279d24a7469cc5eb695d9af9ac',
            bin2hex($expected->getBinary())
        );
    }

    public function test_can_convert_guid_from_binary()
    {
        $hex = 'd0b40d279d24a7469cc5eb695d9af9ac';

        $expected = new Guid(hex2bin($hex));

        $this->assertEquals(
            '270db4d0-249d-46a7-9cc5-eb695d9af9ac',
            $expected->getValue()
        );
    }
}
