<?php


namespace Adldap\Tests\Models;

use Adldap\Models\Entry;
use Adldap\Tests\TestCase;

class ModelAccessorMutatorTest extends TestCase
{
    protected function newEntry(array $attributes = [], $builder = null)
    {
        $builder = $builder ?: $this->newBuilder();

        return new Entry($attributes, $builder);
    }

    public function test_get_distinguished_name()
    {
        $dn = 'cn=jdoe,dc=acme,dc=org';

        $m = $this->newEntry(['dn' => $dn]);

        $this->assertEquals($dn, $m->getDn());
        $this->assertEquals($dn, $m->getDistinguishedName());
    }

    public function test_set_distinguished_name()
    {
        $dn = 'cn=jdoe,dc=acme,dc=org';

        $m = $this->newEntry()->setDistinguishedName($dn);

        $this->assertEquals(['distinguishedname' => [$dn]], $m->getAttributes());
        $this->assertEquals($dn, $m->getDn());
        $this->assertEquals($dn, $m->getDistinguishedName());
    }

    public function test_get_object_sid()
    {
        $sid = 'S-1-5-21-977923109-2952828257-175163757-387119';

        $m = $this->newEntry(['objectsid' => $sid]);

        $this->assertEquals($sid, $m->getObjectSid());
    }

    public function test_get_object_sid_binary()
    {
        $hex = '010500000000000515000000dcf4dc3b833d2b46828ba62800020000';

        $m = $this->newEntry(['objectsid' => hex2bin($hex)]);

        $this->assertEquals(hex2bin($hex), $m->getObjectSid());
        $this->assertEquals('S-1-5-21-1004336348-1177238915-682003330-512', $m->getConvertedSid());
    }

    public function test_get_object_guid()
    {
        $guid = '270db4d0-249d-46a7-9cc5-eb695d9af9ac';

        $m = $this->newEntry(['objectguid' => $guid]);

        $this->assertEquals($guid, $m->getObjectGuid());
    }

    public function test_get_object_guid_binary()
    {
        $hex = 'd0b40d279d24a7469cc5eb695d9af9ac';

        $m = $this->newEntry(['objectguid' => hex2bin($hex)]);

        $this->assertEquals(hex2bin($hex), $m->getObjectGuid());
        $this->assertEquals('270db4d0-249d-46a7-9cc5-eb695d9af9ac', $m->getConvertedGuid());
    }

    public function test_get_common_name()
    {
        $cn = 'John Doe';

        $m = $this->newEntry(['cn' => $cn]);

        $this->assertEquals($cn, $m->getCommonName());
    }

    public function test_set_common_name()
    {
        $cn = 'John Doe';

        $m = $this->newEntry()->setCommonName($cn);

        $this->assertEquals(['cn' => [$cn]], $m->getAttributes());
        $this->assertEquals($cn, $m->getCommonName());
    }

    public function test_get_name()
    {
        $name = 'Suzy Doe';

        $m = $this->newEntry(['name' => $name]);

        $this->assertEquals($name, $m->getName());
    }

    public function test_set_name()
    {
        $name = 'Suzy Doe';

        $m = $this->newEntry()->setName($name);

        $this->assertEquals(['name' => [$name]], $m->getAttributes());
        $this->assertEquals($name, $m->getName());
    }

    public function test_get_display_name()
    {
        $name = 'Doe, Suzy';

        $m = $this->newEntry(['displayname' => $name]);

        $this->assertEquals($name, $m->getDisplayName());
    }

    public function test_set_display_name()
    {
        $name = 'Doe, Suzy';

        $m = $this->newEntry()->setDisplayName($name);

        $this->assertEquals(['displayname' => [$name]], $m->getAttributes());
        $this->assertEquals($name, $m->getDisplayName());
    }

    public function test_get_account_name()
    {
        $an = 'jdoe';

        $m = $this->newEntry(['samaccountname' => $an]);

        $this->assertEquals($an, $m->getAccountName());
    }

    public function test_set_account_name()
    {
        $an = 'jdoe';

        $m = $this->newEntry()->setAccountName($an);

        $this->assertEquals(['samaccountname' => [$an]], $m->getAttributes());
        $this->assertEquals($an, $m->getAccountName());
    }

    public function test_get_account_type()
    {
        // User account type.
        $type = 0x30000000;

        $m = $this->newEntry(['samaccounttype' => $type]);

        $this->assertEquals($type, $m->getAccountType());
    }

    public function test_get_created_at()
    {
        $created = '20150915110643.0Z';

        $m = $this->newEntry(['whencreated' => $created]);

        $this->assertEquals($created, $m->getCreatedAt());
    }

    public function test_get_created_at_date()
    {
        $created = '20150915110643.0Z';

        $m = $this->newEntry(['whencreated' => $created]);

        $this->assertEquals('2015-09-15 11:06:43', $m->getCreatedAtDate());
    }

    public function test_get_created_at_timestamp()
    {
        $created = '20150915110643.0Z';

        $m = $this->newEntry(['whencreated' => $created]);

        $this->assertEquals(1442315203, $m->getCreatedAtTimestamp());
    }

    public function test_get_updated_at()
    {
        $updated = '20160915110643.0Z';

        $m = $this->newEntry(['whenchanged' => $updated]);

        $this->assertEquals($updated, $m->getUpdatedAt());
    }

    public function test_get_updated_at_date()
    {
        $updated = '20160915110643.0Z';

        $m = $this->newEntry(['whenchanged' => $updated]);

        $this->assertEquals('2016-09-15 11:06:43', $m->getUpdatedAtDate());
    }

    public function test_get_updated_at_timestamp()
    {
        $updated = '20160915110643.0Z';

        $m = $this->newEntry(['whenchanged' => $updated]);

        $this->assertEquals(1473937603, $m->getUpdatedAtTimestamp());
    }

    public function test_get_object_category()
    {
        $dn = 'CN=Person,CN=Schema,CN=Configuration,DC=corp,DC=acme,DC=org';

        $m = $this->newEntry(['objectcategory' => $dn]);

        $this->assertEquals('Person', $m->getObjectCategory());
    }

    public function test_get_object_category_dn()
    {
        $dn = 'CN=Person,CN=Schema,CN=Configuration,DC=corp,DC=acme,DC=org';

        $m = $this->newEntry(['objectcategory' => $dn]);

        $this->assertEquals($dn, $m->getObjectCategoryDn());
    }

    public function test_get_object_category_array()
    {
        $dn = 'CN=Person,CN=Schema,CN=Configuration,DC=corp,DC=acme,DC=org';

        $m = $this->newEntry(['objectcategory' => $dn]);

        $this->assertEquals([
            'count' => 6,
            0 => 'Person',
            1 => 'Schema',
            2 => 'Configuration',
            3 => 'corp',
            4 => 'acme',
            5 => 'org',
        ], $m->getObjectCategoryArray());
    }

    public function test_get_primary_group_id()
    {
        $id = '513';

        $m = $this->newEntry(['primarygroupid' => $id]);

        $this->assertEquals($id, $m->getPrimaryGroupId());
    }

    public function test_get_instance_type()
    {
        $type = '4';

        $m = $this->newEntry(['instancetype' => $type]);

        $this->assertEquals($type, $m->getInstanceType());
    }

    public function test_get_managed_by()
    {
        $dn = 'cn=John Doe,dc=corp,dc=acme,dc=org';

        $m = $this->newEntry(['managedby' => $dn]);

        $this->assertEquals($dn, $m->getManagedBy());
    }

    public function test_get_managed_by_user()
    {
        $dn = 'cn=John Doe,dc=corp,dc=acme,dc=org';

        $user = $this->newEntry();

        $b = $this->mock($this->newBuilder());

        $b
            ->shouldReceive('newInstance')->andReturnSelf()
            ->shouldReceive('findByDn')->withArgs([$dn])->andReturn($user);

        $m = $this->newEntry(['managedby' => $dn], $b);

        $this->assertEquals($user, $m->getManagedByUser());
    }

    public function test_set_managed_by()
    {
        $dn = 'cn=John Doe,dc=corp,dc=acme,dc=org';

        $user = $this->newEntry(['dn' => $dn]);

        $manager = 'cn=Suzy Doe,dc=corp,dc=acme,dc=org';

        $this->assertEquals($manager, $user->setManagedBy($manager)->getManagedBy());
        $this->assertEquals($manager, $user->setManagedBy($this->newEntry(['dn' => $manager]))->getManagedBy());
    }

    public function test_get_max_password_age()
    {
        // 60 Days.
        $max = -51840000000000;

        $m = $this->newEntry(['maxpwdage' => $max]);

        $this->assertEquals($max, $m->getMaxPasswordAge());
        $this->assertEquals(60, $m->getMaxPasswordAgeDays());
    }
}
