<?php

namespace Adldap\tests\Models;

use Adldap\Models\Entry;
use Adldap\Tests\UnitTestCase;

class EntryTest extends UnitTestCase
{
    public function testConstruct()
    {
        $attributes = [
            'cn'             => 'Common Name',
            'samaccountname' => 'Account Name',
        ];

        $entry = new Entry($attributes, $this->newBuilder());

        $this->assertEquals($attributes, $entry->getAttributes());
    }

    public function testSetRawAttributes()
    {
        $rawAttributes = [
            'cn'             => ['Common Name'],
            'samaccountname' => ['Account Name'],
            'dn'            => ['dn'],
        ];

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('read')->once()->andReturn($connection);
        $connection->shouldReceive('getEntries')->once()->andReturn([$rawAttributes]);

        $entry = new Entry([], $this->newBuilder($connection));

        $entry->setRawAttributes($rawAttributes);

        $this->assertTrue($entry->exists);
        $this->assertEquals($rawAttributes, $entry->getAttributes());
    }

    public function testSetAttribute()
    {
        $attributes = [
            'cn'             => ['Common Name'],
            'samaccountname' => ['Account Name'],
        ];

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('read')->once()->andReturn($connection);
        $connection->shouldReceive('getEntries')->once()->andReturn([$attributes]);

        $entry = new Entry([], $this->newBuilder($connection));

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

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('read')->once()->andReturn($connection);
        $connection->shouldReceive('getEntries')->once()->andReturn([$attributes]);

        $connection->shouldReceive('modDelete')->once()->withArgs(['dc=corp,dc=org', ['cn' => []]])->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $entry = new Entry([], $this->newBuilder($connection));

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

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('read')->once()->andReturn($connection);
        $connection->shouldReceive('getEntries')->once()->andReturn([$attributes]);

        $connection->shouldReceive('modAdd')->once()->withArgs(['dc=corp,dc=org', ['givenName' => 'John Doe']])->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $entry = new Entry([], $this->newBuilder($connection));

        $entry->setRawAttributes($attributes);

        $this->assertTrue($entry->createAttribute('givenName', 'John Doe'));
    }

    public function testModifications()
    {
        $attributes = [
            'cn'             => ['Common Name'],
            'samaccountname' => ['Account Name'],
        ];

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('read')->once()->andReturn($connection);
        $connection->shouldReceive('getEntries')->once()->andReturn([$attributes]);

        $entry = new Entry([], $this->newBuilder($connection));

        $entry->setRawAttributes($attributes);

        $entry->cn = null;
        $entry->samaccountname = 'Changed';
        $entry->test = 'New Attribute';

        $modifications = $entry->getModifications();

        // Removed 'cn' attribute
        $this->assertEquals('cn', $modifications[0]['attrib']);
        $this->assertFalse(isset($modifications[0]['values']));
        $this->assertEquals(18, $modifications[0]['modtype']);

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
        $attributes = [
            'cn' => 'John Doe',
            'givenname' => 'John',
            'sn' => 'Doe',
        ];

        $returnedRaw = [
            'count' => 1,
            [
                'cn' => ['John Doe'],
                'givenname' => ['John'],
                'sn' => ['Doe'],
            ]
        ];

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('add')->withArgs(['cn=John Doe,ou=Accounting,dc=corp,dc=org', $attributes])->andReturn(true);
        $connection->shouldReceive('read')->withArgs(['cn=John Doe,ou=Accounting,dc=corp,dc=org', '(objectclass=*)', []])->andReturn('resource');
        $connection->shouldReceive('getEntries')->andReturn($returnedRaw);

        $connection->shouldReceive('read')->andReturn($connection);
        $connection->shouldReceive('getEntries')->andReturn($returnedRaw);

        $connection->shouldReceive('close')->andReturn(true);

        $entry = new Entry($attributes, $this->newBuilder($connection));

        $entry->setDn('cn=John Doe,ou=Accounting,dc=corp,dc=org');

        $this->assertTrue($entry->create());
        $this->assertEquals($attributes['cn'], $entry->getCommonName());
        $this->assertEquals($attributes['sn'], $entry->sn[0]);
        $this->assertEquals($attributes['givenname'], $entry->givenname[0]);
    }

    public function testUpdate()
    {
        $connection = $this->newConnectionMock();

        $dn = 'cn=Testing,ou=Accounting,dc=corp,dc=org';

        $attributes = ['dn' => $dn];

        $connection->shouldReceive('read')->andReturn($connection);
        $connection->shouldReceive('getEntries')->andReturn($attributes);

        $connection->shouldReceive('modifyBatch')->once()->withArgs([$dn, []])->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $entry = new Entry([], $this->newBuilder($connection));

        $entry->setRawAttributes($attributes);

        $this->assertTrue($entry->update());
    }

    public function testSaveForCreate()
    {
        $connection = $this->newConnectionMock();

        $attributes = [
            'cn' => 'John Doe',
            'givenname' => 'John',
            'sn' => 'Doe',
        ];

        $dn = 'cn=John Doe,ou=Accounting,dc=corp,dc=org';

        $returnedRaw = [
            'count' => 1,
            [
                'cn' => ['John Doe'],
                'givenname' => ['John'],
                'sn' => ['Doe'],
            ]
        ];

        $connection->shouldReceive('add')->withArgs([$dn, $attributes])->andReturn(true);
        $connection->shouldReceive('read')->withArgs([$dn, '(objectclass=*)', []])->andReturn('resource');
        $connection->shouldReceive('getEntries')->andReturn($returnedRaw);

        $connection->shouldReceive('read')->andReturn($connection);
        $connection->shouldReceive('getEntries')->andReturn($returnedRaw);

        $connection->shouldReceive('close')->once()->andReturn(true);

        $entry = new Entry($attributes, $this->newBuilder($connection));

        $entry->setDn($dn);

        $this->assertTrue($entry->save());
        $this->assertEquals($attributes['cn'], $entry->getCommonName());
        $this->assertEquals($attributes['sn'], $entry->sn[0]);
        $this->assertEquals($attributes['givenname'], $entry->givenname[0]);
    }

    public function testSaveForUpdate()
    {
        $connection = $this->newConnectionMock();

        $dn = 'cn=Testing,ou=Accounting,dc=corp,dc=org';

        $returnedRaw = [['dn' => $dn]];

        $connection->shouldReceive('read')->andReturn($connection);
        $connection->shouldReceive('getEntries')->andReturn($returnedRaw);

        $connection->shouldReceive('modifyBatch')->once()->withArgs([$dn, []])->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $entry = new Entry([], $this->newBuilder($connection));

        $entry->setRawAttributes(['dn' => $dn]);

        $this->assertTrue($entry->save());
    }

    public function testDeleteFailure()
    {
        $entry = new Entry([], $this->newBuilder());

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $entry->delete();
    }

    public function testDelete()
    {
        $connection = $this->newConnectionMock();

        $dn = 'cn=Testing,ou=Accounting,dc=corp,dc=org';

        $returnedRaw = [['dn' => $dn]];

        $connection->shouldReceive('read')->andReturn($connection);
        $connection->shouldReceive('getEntries')->andReturn($returnedRaw);

        $connection->shouldReceive('delete')->once()->withArgs([$dn])->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $entry = new Entry([], $this->newBuilder($connection));

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

    public function testFilterRawAttributes()
    {
        $rawAttributes = [
            'count' => 1,
            'test' => [
                'count' => 1,
                'test' => [
                    'count' => 1,
                    'test' => [
                        'count' => 1,
                        'test' => [
                            'count' => 1
                        ],
                    ]
                ]
            ]
        ];

        $expected = [
            'test' => [
                'test' => [
                    'test' => [
                        'test' => []
                    ]
                ]
            ]
        ];

        $entry = new Entry([], $this->newBuilder());

        $entry->setRawAttributes($rawAttributes);

        $this->assertEquals($expected, $entry->getAttributes());
    }

    public function testMove()
    {
        $rawAttributes = [
            'dn' => 'cn=Doe,dc=corp,dc=acme,dc=org',
        ];

        $connection = $this->newConnectionMock();

        $args = [
            'cn=Doe,dc=corp,dc=acme,dc=org',
            'cn=John',
            'ou=Accounts,dc=corp,dc=amce,dc=org',
            false,
        ];

        $connection->shouldReceive('rename')->once()->withArgs($args)->andReturn(true);

        $entry = new Entry([], $this->newBuilder($connection));

        $entry->setRawAttributes($rawAttributes);

        $this->assertTrue($entry->move($args[1], $args[2]));
    }
}
