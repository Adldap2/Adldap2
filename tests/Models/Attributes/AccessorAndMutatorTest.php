<?php

namespace Adldap\Tests\Models\Attributes;

use Adldap\Models\Computer;
use Adldap\Models\Container;
use Adldap\Models\Entry;
use Adldap\Models\Group;
use Adldap\Models\OrganizationalUnit;
use Adldap\Models\Printer;
use Adldap\Models\RootDse;
use Adldap\Tests\TestCase;

class AccessorAndMutatorTest extends TestCase
{
    protected function newEntry(array $attributes = [], $builder = null)
    {
        return new Entry($attributes, $builder ?: $this->newBuilder());
    }

    protected function newComputer(array $attributes = [], $builder = null)
    {
        return new Computer($attributes, $builder ?: $this->newBuilder());
    }

    protected function newContainer(array $attributes = [], $builder = null)
    {
        return new Container($attributes, $builder ?: $this->newBuilder());
    }

    protected function newGroup(array $attributes = [], $builder = null)
    {
        return new Group($attributes, $builder ?: $this->newBuilder());
    }

    protected function newOu(array $attributes = [], $builder = null)
    {
        return new OrganizationalUnit($attributes, $builder ?: $this->newBuilder());
    }

    protected function newPrinter(array $attributes = [], $builder = null)
    {
        return new Printer($attributes, $builder ?: $this->newBuilder());
    }

    protected function newRootDse(array $attributes = [], $builder = null)
    {
        return new RootDse($attributes, $builder ?: $this->newBuilder());
    }

    /*
    |--------------------------------------------------------------------------
    | Entry Accessor / Mutator Tests
    |--------------------------------------------------------------------------
    |
    */

    public function test_get_distinguished_name()
    {
        $dn = 'cn=jdoe,dc=acme,dc=org';

        $m = $this->newEntry(['dn' => $dn]);

        $this->assertEquals($dn, $m->getDn());
        $this->assertEquals($dn, $m->getDistinguishedName());
    }

    public function test_get_dn_components()
    {
        $dn = 'cn=jdoe,dc=acme,dc=org';

        $m = $this->newEntry(['dn' => $dn]);

        $this->assertEquals([
            'jdoe',
            'acme',
            'org',
        ], $m->getDnComponents());

        $this->assertEquals([
            'cn=jdoe',
            'dc=acme',
            'dc=org',
        ], $m->getDnComponents(false));

        $m->setDn('invalid');

        $this->assertEquals([], $m->getDnComponents());
    }

    public function get_dn_root()
    {
        $dn = 'cn=jdoe,dc=acme,dc=org';

        $m = $this->newEntry(['dn' => $dn]);

        $this->assertEquals('dc=acme,dc=org', $m->getDnRoot());

        $m->setDn('invalid');

        $this->assertEmpty($m->getDnRoot());
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
            0       => 'Person',
            1       => 'Schema',
            2       => 'Configuration',
            3       => 'corp',
            4       => 'acme',
            5       => 'org',
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

    /*
    |--------------------------------------------------------------------------
    | Computer Accessor / Mutator Tests
    |--------------------------------------------------------------------------
    |
    */

    public function test_get_operating_system()
    {
        $os = 'Windows';

        $c = $this->newComputer(['operatingsystem' => $os]);

        $this->assertEquals($os, $c->getOperatingSystem());
    }

    public function test_get_operating_system_version()
    {
        $v = '10.0';

        $c = $this->newComputer(['operatingsystemversion' => $v]);

        $this->assertEquals($v, $c->getOperatingSystemVersion());
    }

    public function test_get_operating_system_service_pack()
    {
        $p = 'Service Pack 1';

        $c = $this->newComputer(['operatingsystemservicepack' => $p]);

        $this->assertEquals($p, $c->getOperatingSystemServicePack());
    }

    public function test_get_dns_host_name()
    {
        $host = 'COMP-101';

        $c = $this->newComputer(['dnshostname' => $host]);

        $this->assertEquals($host, $c->getDnsHostName());
    }

    public function test_get_bad_password_time()
    {
        $time = 1442315203;

        $c = $this->newComputer(['badpasswordtime' => $time]);

        $this->assertEquals($time, $c->getBadPasswordTime());
    }

    public function test_get_account_expiry()
    {
        // Never expires.
        $expiry = 9223372036854775807;

        $c = $this->newComputer(['accountexpires' => $expiry]);

        $this->assertEquals($expiry, $c->getAccountExpiry());
    }

    /*
    |--------------------------------------------------------------------------
    | Container Accessor / Mutator Tests
    |--------------------------------------------------------------------------
    |
    */

    public function test_get_system_flags()
    {
        $c = $this->newContainer(['systemflags' => 1]);

        $this->assertEquals(1, $c->getSystemFlags());
    }

    /*
    |--------------------------------------------------------------------------
    | Group Accessor / Mutator Tests
    |--------------------------------------------------------------------------
    |
    */

    public function test_get_members()
    {
        $members = ['cn=John Doe,dc=corp,dc=acme,dc=org'];

        $u = $this->newEntry(['dn' => $members[0]]);

        $b = $this->mock($this->newBuilder());

        $b
            ->shouldReceive('newInstance')->andReturnSelf()
            ->shouldReceive('findByDn')->withArgs([$members[0]])->andReturn($u);

        $g = $this->newGroup(['member' => $members], $b);

        $this->assertEquals($g->newCollection()->push($u), $g->getMembers());
    }

    public function test_set_members()
    {
        $members = ['cn=John Doe,dc=corp,dc=acme,dc=org'];

        $this->assertEquals($members, $this->newGroup()->setMembers($members)->getAttribute('member'));
    }

    public function test_get_group_type()
    {
        $type = 0x00000002;

        $g = $this->newGroup(['grouptype' => $type]);

        $this->assertEquals($type, $g->getGroupType());
    }

    /*
    |--------------------------------------------------------------------------
    | Organizational Unit Accessor / Mutator Tests
    |--------------------------------------------------------------------------
    |
    */

    public function test_get_ou()
    {
        $name = 'User Accounts';

        $ou = $this->newOu(['ou' => $name]);

        $this->assertEquals($name, $ou->getOu());
    }

    /*
    |--------------------------------------------------------------------------
    | Printer Accessor / Mutator Tests
    |--------------------------------------------------------------------------
    |
    */

    public function test_get_printer_name()
    {
        $name = 'Xerox';

        $p = $this->newPrinter(['printername' => $name]);

        $this->assertEquals($name, $p->getPrinterName());
    }

    public function test_get_printer_share_name()
    {
        $name = 'XEROX-ADMIN';

        $p = $this->newPrinter(['printsharename' => $name]);

        $this->assertEquals($name, $p->getPrinterShareName());
    }

    public function test_get_memory()
    {
        $memory = 1000;

        $p = $this->newPrinter(['printmemory' => $memory]);

        $this->assertEquals($memory, $p->getMemory());
    }

    public function test_get_url()
    {
        $url = 'http://192.168.1.10';

        $p = $this->newPrinter(['url' => $url]);

        $this->assertEquals($url, $p->getUrl());
    }

    public function test_get_location()
    {
        $location = 'Main Office';

        $p = $this->newPrinter(['location' => $location]);

        $this->assertEquals($location, $p->getLocation());
    }

    public function test_get_server_name()
    {
        $server = 'PRINT-SERVER';

        $p = $this->newPrinter(['servername' => $server]);

        $this->assertEquals($server, $p->getServerName());
    }

    public function test_get_color_supported()
    {
        $supported = 'TRUE';

        $p = $this->newPrinter(['printcolor' => $supported]);

        $this->assertTrue($p->getColorSupported());

        $supported = 'FALSE';

        $p = $this->newPrinter(['printcolor' => $supported]);

        $this->assertFalse($p->getColorSupported());
    }

    public function test_get_duplex_supported()
    {
        $supported = 'TRUE';

        $p = $this->newPrinter(['printduplexsupported' => $supported]);

        $this->assertTrue($p->getDuplexSupported());

        $supported = 'FALSE';

        $p = $this->newPrinter(['printduplexsupported' => $supported]);

        $this->assertFalse($p->getDuplexSupported());
    }

    public function test_get_stapling_supported()
    {
        $supported = 'TRUE';

        $p = $this->newPrinter(['printstaplingsupported' => $supported]);

        $this->assertTrue($p->getStaplingSupported());

        $supported = 'FALSE';

        $p = $this->newPrinter(['printstaplingsupported' => $supported]);

        $this->assertFalse($p->getStaplingSupported());
    }

    public function test_get_media_supported()
    {
        $supported = [
            'LEGAL',
            'A10',
            'A11',
        ];

        $p = $this->newPrinter(['printmediasupported' => $supported]);

        $this->assertEquals($supported, $p->getMediaSupported());
    }

    public function test_get_print_bin_names()
    {
        $bins = [
            'LEGAL',
            'A10',
            'A11',
        ];

        $p = $this->newPrinter(['printbinnames' => $bins]);

        $this->assertEquals($bins, $p->getPrintBinNames());
    }

    public function test_get_print_max_resolution()
    {
        $res = '1024x768';

        $p = $this->newPrinter(['printmaxresolutionsupported' => $res]);

        $this->assertEquals($res, $p->getPrintMaxResolution());
    }

    public function test_get_print_orientation()
    {
        $orientation = 270;

        $p = $this->newPrinter(['printorientationssupported' => $orientation]);

        $this->assertEquals($orientation, $p->getPrintOrientations());
    }

    public function test_get_driver_name()
    {
        $name = 'xerox-driver-64-bit';

        $p = $this->newPrinter(['drivername' => $name]);

        $this->assertEquals($name, $p->getDriverName());
    }

    public function test_get_driver_version()
    {
        $version = '1060.34';

        $p = $this->newPrinter(['driverversion' => $version]);

        $this->assertEquals($version, $p->getDriverVersion());
    }

    public function test_get_priority()
    {
        $priority = '1';

        $p = $this->newPrinter(['priority' => $priority]);

        $this->assertEquals($priority, $p->getPriority());
    }

    public function test_get_print_start_time()
    {
        $time = '60';

        $p = $this->newPrinter(['printstarttime' => $time]);

        $this->assertEquals($time, $p->getPrintStartTime());
    }

    public function test_get_print_end_time()
    {
        $time = '60';

        $p = $this->newPrinter(['printendtime' => $time]);

        $this->assertEquals($time, $p->getPrintEndTime());
    }

    public function test_get_port_name()
    {
        $portName = '10.0.0.1';

        $p = $this->newPrinter(['portname' => $portName]);

        $this->assertEquals($portName, $p->getPortName());
    }

    public function test_get_version_number()
    {
        $version = '4';

        $p = $this->newPrinter(['versionnumber' => $version]);

        $this->assertEquals($version, $p->getVersionNumber());
    }

    public function test_get_print_rate()
    {
        $rate = '36';

        $p = $this->newPrinter(['printrate' => $rate]);

        $this->assertEquals($rate, $p->getPrintRate());
    }

    public function test_get_print_rate_unit()
    {
        $rate = '36';

        $p = $this->newPrinter(['printrateunit' => $rate]);

        $this->assertEquals($rate, $p->getPrintRateUnit());
    }
}
