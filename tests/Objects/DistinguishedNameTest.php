<?php

namespace Adldap\Tests\Objects;

use Adldap\Objects\DistinguishedName;
use Adldap\Tests\UnitTestCase;

class DistinguishedNameTest extends UnitTestCase
{
    public function testConstructBase()
    {
        $base = 'dc=corp,dc=org';

        $dn = new DistinguishedName($base);

        $this->assertEquals($base, $dn->get());
    }

    public function testConstructInvalidBase()
    {
        $base = 'invalid base';

        $dn = new DistinguishedName($base);

        $this->assertEmpty($dn->get());
    }

    public function testConstructWithAnotherDnObject()
    {
        $base = new DistinguishedName();

        $base->addDc('org');
        $base->addDc('corp');

        $dn = new DistinguishedName($base);

        $this->assertEquals($base->get(), $dn->get());
    }
}
