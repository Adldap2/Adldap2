<?php

namespace Adldap\tests\Models;

use Adldap\Models\Entry;
use Adldap\Tests\UnitTestCase;

class EntryTest extends UnitTestCase
{
    protected function newAdldapPartialMock()
    {
        return $this->mock('Adldap\Adldap')->makePartial();
    }

    protected function newConnectionMock()
    {
        return $this->mock('Adldap\Connections\Ldap');
    }

    public function testConstruct()
    {
        $attributes = [
            'cn'             => 'Common Name',
            'samaccountname' => 'Account Name',
        ];

        $entry = new Entry($attributes, $this->newAdldapPartialMock());

        $this->assertEquals($attributes, $entry->getAttributes());
    }

    public function testSetRawAttributes()
    {
        $attributes = [
            'cn'             => ['Common Name'],
            'samaccountname' => ['Account Name'],
        ];

        $entry = new Entry([], $this->newAdldapPartialMock());

        $entry->setRawAttributes($attributes);

        $this->assertTrue($entry->exists);
        $this->assertEquals($attributes, $entry->getAttributes());
    }

    public function testSetAttribute()
    {
        $attributes = [
            'cn'             => ['Common Name'],
            'samaccountname' => ['Account Name'],
        ];

        $entry = new Entry([], $this->newAdldapPartialMock());

        $entry->setRawAttributes($attributes);

        $entry->setCommonName('New Common Name');
        $entry->samaccountname = ['New Account Name'];

        $this->assertEquals('New Common Name', $entry->getCommonName());
        $this->assertEquals(['New Account Name'], $entry->samaccountname);
    }

    public function testDeleteAttribute()
    {
        $attributes = [
            'cn'             => ['Common Name'],
            'samaccountname' => ['Account Name'],
            'dn'             => 'dc=corp,dc=org',
        ];

        $adldap = $this->newAdldapPartialMock();

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('modDelete')->once()->withArgs(['dc=corp,dc=org', ['cn' => []]])->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $adldap->setConnection($connection);

        $entry = new Entry([], $adldap);

        $entry->setRawAttributes($attributes);

        $this->assertTrue($entry->deleteAttribute('cn'));
    }

    public function testCreateAttribute()
    {
        $attributes = [
            'cn'             => ['Common Name'],
            'samaccountname' => ['Account Name'],
            'dn'             => 'dc=corp,dc=org',
        ];

        $adldap = $this->newAdldapPartialMock();

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('modAdd')->once()->withArgs(['dc=corp,dc=org', ['givenName' => 'John Doe']])->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $adldap->setConnection($connection);

        $entry = new Entry([], $adldap);

        $entry->setRawAttributes($attributes);

        $this->assertTrue($entry->createAttribute('givenName', 'John Doe'));
    }

    public function testModifications()
    {
        $attributes = [
            'cn'             => ['Common Name'],
            'samaccountname' => ['Account Name'],
        ];

        $entry = new Entry([], $this->newAdldapPartialMock());

        $entry->setRawAttributes($attributes);

        $entry->cn = null;
        $entry->samaccountname = 'Changed';
        $entry->test = 'New Attribute';

        $modifications = $entry->getModifications();

        // Removed 'cn' attribute
        $this->assertEquals('cn', $modifications[0]['attrib']);
        $this->assertEquals([null], $modifications[0]['values']);
        $this->assertEquals(2, $modifications[0]['modtype']);

        // Modified 'samaccountname' attribute
        $this->assertEquals('samaccountname', $modifications[1]['attrib']);
        $this->assertEquals(['Changed'], $modifications[1]['values']);
        $this->assertEquals(3, $modifications[1]['modtype']);

        // New 'test' attribute
        $this->assertEquals('test', $modifications[2]['attrib']);
        $this->assertEquals(['New Attribute'], $modifications[2]['values']);
        $this->assertEquals(1, $modifications[2]['modtype']);
    }

    public function testCreate()
    {
        $adldap = $this->newAdldapPartialMock();

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('add')->once()->withArgs([null, ['testing']])->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $adldap->setConnection($connection);

        $entry = new Entry(['testing'], $adldap);

        $this->assertTrue($entry->create());
    }

    public function testUpdate()
    {
        $connection = $this->newConnectionMock();

        $dn = 'cn=Testing,ou=Accounting,dc=corp,dc=org';

        $connection->shouldReceive('modifyBatch')->once()->withArgs([$dn, []])->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $adldap = $this->newAdldapPartialMock();

        $adldap->setConnection($connection);

        $entry = new Entry([], $adldap);

        $entry->setRawAttributes(['dn' => $dn]);

        $this->assertTrue($entry->update());
    }

    public function testSaveForCreate()
    {
        $connection = $this->newConnectionMock();

        $connection->shouldReceive('add')->once()->withArgs([null, ['testing']])->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $adldap = $this->newAdldapPartialMock();

        $adldap->setConnection($connection);

        $entry = new Entry(['testing'], $adldap);

        $this->assertTrue($entry->save());
    }

    public function testSaveForUpdate()
    {
        $connection = $this->newConnectionMock();

        $dn = 'cn=Testing,ou=Accounting,dc=corp,dc=org';

        $connection->shouldReceive('modifyBatch')->once()->withArgs([$dn, []])->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $adldap = $this->newAdldapPartialMock();

        $adldap->setConnection($connection);

        $entry = new Entry([], $adldap);

        $entry->setRawAttributes(['dn' => $dn]);

        $this->assertTrue($entry->save());
    }

    public function testDeleteFailure()
    {
        $entry = new Entry([], $this->newAdldapPartialMock());

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $entry->delete();
    }

    public function testDelete()
    {
        $connection = $this->newConnectionMock();

        $dn = 'cn=Testing,ou=Accounting,dc=corp,dc=org';

        $connection->shouldReceive('delete')->once()->withArgs([$dn])->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $adldap = $this->newAdldapPartialMock();

        $adldap->setConnection($connection);

        $entry = new Entry([], $adldap);

        $entry->setRawAttributes(['dn' => $dn]);

        $this->assertTrue($entry->delete());
    }

    public function testConvertStringToBool()
    {
        $entry = $this->mock('Adldap\Models\Entry')->makePartial();

        $entry->shouldAllowMockingProtectedMethods();

        $this->assertNull($entry->convertStringToBool('test'));

        $this->assertTrue($entry->convertStringToBool('true'));
        $this->assertTrue($entry->convertStringToBool('TRUE'));
        $this->assertTrue($entry->convertStringToBool('TRue'));

        $this->assertFalse($entry->convertStringToBool('false'));
        $this->assertFalse($entry->convertStringToBool('FALSE'));
        $this->assertFalse($entry->convertStringToBool('FAlse'));
    }
}
