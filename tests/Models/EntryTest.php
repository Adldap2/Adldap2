<?php

namespace Adldap\Tests\Models;

use Adldap\Models\Entry;
use Adldap\Tests\TestCase;
use Adldap\Schemas\ActiveDirectory;
use Adldap\Objects\BatchModification;

class EntryTest extends TestCase
{
    protected function newModel(array $attributes = [], $builder = null, $schema = null)
    {
        $builder = $builder ?: $this->newBuilder();

        return new Entry($attributes, $builder, $schema);
    }

    public function test_construct()
    {
        $attributes = [
            'cn'             => ['Common Name'],
            'samaccountname' => ['Account Name'],
        ];

        $entry = $this->newModel($attributes, $this->newBuilder());

        $this->assertEquals($attributes, $entry->getAttributes());
    }

    public function test_set_raw_attributes()
    {
        $rawAttributes = [
            'cn'                => ['Common Name'],
            'samaccountname'    => ['Account Name'],
        ];

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('read')->once()->andReturn($connection);
        $connection->shouldReceive('getEntries')->once()->andReturn([$rawAttributes]);

        $entry = $this->newModel([], $this->newBuilder($connection));

        $entry->setRawAttributes($rawAttributes);

        $this->assertTrue($entry->exists);
        $this->assertEquals($rawAttributes, $entry->getAttributes());
    }

    public function test_set_attribute()
    {
        $attributes = [
            'cn'             => ['Common Name'],
            'samaccountname' => ['Account Name'],
        ];

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('read')->once()->andReturn($connection);
        $connection->shouldReceive('getEntries')->once()->andReturn([$attributes]);

        $entry = $this->newModel([], $this->newBuilder($connection));

        $entry->setRawAttributes($attributes);

        $entry->setCommonName('New Common Name');
        $entry->samaccountname = ['New Account Name'];

        $this->assertEquals('New Common Name', $entry->getCommonName());
        $this->assertEquals(['New Account Name'], $entry->samaccountname);
    }

    public function test_set_attribute_forces_lowercase_keys()
    {
        $entry = $this->newModel();

        $entry->setAttribute('TEST', 'test');

        $this->assertEquals('test', key($entry->getAttributes()));
    }

    public function test_update_attribute()
    {
        $attributes = [
            'cn'                => ['Common Name'],
            'samaccountname'    => ['Account Name'],
            'dn'                => 'dc=corp,dc=org',
        ];

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('read')->once()->andReturn($connection);
        $connection->shouldReceive('getEntries')->once()->andReturn([$attributes]);

        $connection->shouldReceive('modReplace')->once()->withArgs(['dc=corp,dc=org', ['cn' => 'John Doe']])->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $entry = $this->newModel([], $this->newBuilder($connection));

        $entry->setRawAttributes($attributes);
        $this->assertTrue($entry->updateAttribute('cn', 'John Doe'));
    }

    public function test_delete_attribute_with_string()
    {
        $attributes = [
            'cn'                => ['Common Name'],
            'samaccountname'    => ['Account Name'],
            'dn'                => 'dc=corp,dc=org',
        ];

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('read')->once()->andReturn($connection);
        $connection->shouldReceive('getEntries')->once()->andReturn([$attributes]);

        $connection->shouldReceive('modDelete')->once()->withArgs(['dc=corp,dc=org', ['cn' => []]])->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $entry = $this->newModel([], $this->newBuilder($connection));

        $entry->setRawAttributes($attributes);

        $this->assertTrue($entry->deleteAttribute('cn'));
    }

    public function test_delete_attribute_with_array()
    {
        $attributes = [
            'cn'                => ['Common Name'],
            'samaccountname'    => ['Account Name'],
            'dn'                => 'dc=corp,dc=org',
        ];

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('read')->once()->andReturn($connection);
        $connection->shouldReceive('getEntries')->once()->andReturn([$attributes]);

        $connection->shouldReceive('modDelete')->once()->withArgs(['dc=corp,dc=org', [
            'cn' => [], 'memberof' => []
        ]])->andReturn(true);

        $connection->shouldReceive('close')->once()->andReturn(true);

        $entry = $this->newModel([], $this->newBuilder($connection));

        $entry->setRawAttributes($attributes);

        $this->assertTrue($entry->deleteAttribute([
            'cn' => [],
            'memberof' => [],
        ]));
    }

    public function test_create_attribute()
    {
        $attributes = [
            'cn'                => ['Common Name'],
            'samaccountname'    => ['Account Name'],
            'dn'                => 'dc=corp,dc=org',
        ];

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('read')->once()->andReturn($connection);
        $connection->shouldReceive('getEntries')->once()->andReturn([$attributes]);

        $connection->shouldReceive('modAdd')->once()->withArgs(['dc=corp,dc=org', ['givenName' => 'John Doe']])->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $entry = $this->newModel([], $this->newBuilder($connection));

        $entry->setRawAttributes($attributes);

        $this->assertTrue($entry->createAttribute('givenName', 'John Doe'));
    }

    public function test_modifications()
    {
        $attributes = [
            'cn'             => ['Common Name'],
            'samaccountname' => ['Account Name'],
            'name'           => ['Name'],
        ];

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('read')->once()->andReturn($connection);
        $connection->shouldReceive('getEntries')->once()->andReturn([$attributes]);

        $entry = $this->newModel([], $this->newBuilder($connection));

        $entry->setRawAttributes($attributes);

        $entry->cn = null;
        $entry->samaccountname = 'Changed';
        $entry->test = 'New Attribute';
        $entry->setName('New Name');

        $modifications = $entry->getModifications();

        // Removed 'cn' attribute
        $this->assertEquals('cn', $modifications[0]['attrib']);
        $this->assertFalse(isset($modifications[0]['values']));
        $this->assertEquals(18, $modifications[0]['modtype']);

        // Modified 'samaccountname' attribute
        $this->assertEquals('samaccountname', $modifications[1]['attrib']);
        $this->assertEquals(['Changed'], $modifications[1]['values']);
        $this->assertEquals(3, $modifications[1]['modtype']);

        // Modified 'name' attribute
        $this->assertEquals('name', $modifications[2]['attrib']);
        $this->assertEquals(['New Name'], $modifications[2]['values']);
        $this->assertEquals(3, $modifications[2]['modtype']);

        // New 'test' attribute
        $this->assertEquals('test', $modifications[3]['attrib']);
        $this->assertEquals(['New Attribute'], $modifications[3]['values']);
        $this->assertEquals(1, $modifications[3]['modtype']);
    }

    public function test_create()
    {
        $attributes = [
            'cn'        => ['John Doe'],
            'givenname' => ['John'],
            'sn'        => ['Doe'],
        ];

        $returnedRaw = [
            'count' => 1,
            [
                'cn'        => ['John Doe'],
                'givenname' => ['John'],
                'sn'        => ['Doe'],
            ],
        ];

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('add')->withArgs(['cn=John Doe,ou=Accounting,dc=corp,dc=org', $attributes])->andReturn(true);
        $connection->shouldReceive('read')->withArgs(['cn=John Doe,ou=Accounting,dc=corp,dc=org', '(objectclass=*)', [], false, 0])->andReturn('resource');
        $connection->shouldReceive('getEntries')->andReturn($returnedRaw);

        $connection->shouldReceive('read')->andReturn($connection);
        $connection->shouldReceive('getEntries')->andReturn($returnedRaw);

        $connection->shouldReceive('close')->andReturn(true);

        $entry = $this->newModel($attributes, $this->newBuilder($connection));

        $entry->setDn('cn=John Doe,ou=Accounting,dc=corp,dc=org');

        $this->assertTrue($entry->create());
        $this->assertEquals($attributes['cn'][0], $entry->getCommonName());
        $this->assertEquals($attributes['sn'][0], $entry->sn[0]);
    }

    public function test_update()
    {
        $connection = $this->newConnectionMock();

        $dn = 'cn=Testing,ou=Accounting,dc=corp,dc=org';

        $attributes = ['dn' => $dn];

        $connection->shouldReceive('read')->andReturn($connection);
        $connection->shouldReceive('getEntries')->andReturn($attributes);

        $connection->shouldReceive('modifyBatch')->once()->withArgs([$dn, []])->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $entry = $this->newModel([], $this->newBuilder($connection));

        $entry->setRawAttributes($attributes);

        $this->assertTrue($entry->update());
    }

    public function test_save_for_create()
    {
        $connection = $this->newConnectionMock();

        $attributes = [
            'cn'        => ['John Doe'],
            'givenname' => ['John'],
            'sn'        => ['Doe'],
        ];

        $dn = 'cn=John Doe,ou=Accounting,dc=corp,dc=org';

        $returnedRaw = [
            'count' => 1,
            [
                'cn'        => ['John Doe'],
                'givenname' => ['John'],
                'sn'        => ['Doe'],
                'dn'        => $dn,
            ],
        ];

        $connection->shouldReceive('add')->withArgs([$dn, $attributes])->andReturn(true);
        $connection->shouldReceive('read')->withArgs([$dn, '(objectclass=*)', [], false, 0])->andReturn('resource');
        $connection->shouldReceive('getEntries')->andReturn($returnedRaw);

        $connection->shouldReceive('read')->andReturn($connection);
        $connection->shouldReceive('getEntries')->andReturn($returnedRaw);

        $connection->shouldReceive('close')->once()->andReturn(true);

        $entry = $this->newModel($attributes, $this->newBuilder($connection));

        $entry->setDn($dn);

        $this->assertTrue($entry->save());
        $this->assertEquals($attributes['cn'][0], $entry->getCommonName());
        $this->assertEquals($attributes['sn'][0], $entry->sn[0]);
        $this->assertEquals($attributes['givenname'][0], $entry->givenname[0]);
    }

    public function test_save_for_create_with_attributes()
    {
        $connection = $this->newConnectionMock();

        $attributes = [
            'cn'        => ['John Doe'],
            'givenname' => ['John'],
            'sn'        => ['Doe'],
        ];

        $dn = 'cn=John Doe,ou=Accounting,dc=corp,dc=org';

        $returnedRaw = [
            'count' => 1,
            [
                'cn'        => ['John Doe'],
                'givenname' => ['John'],
                'sn'        => ['Doe'],
                'dn'        => $dn,
            ],
        ];

        $connection->shouldReceive('add')->withArgs([$dn, $attributes])->andReturn(true);
        $connection->shouldReceive('read')->withArgs([$dn, '(objectclass=*)', [], false, 0])->andReturn('resource');
        $connection->shouldReceive('getEntries')->andReturn($returnedRaw);

        $connection->shouldReceive('read')->andReturn($connection);
        $connection->shouldReceive('getEntries')->andReturn($returnedRaw);

        $connection->shouldReceive('close')->once()->andReturn(true);

        $entry = $this->newModel([], $this->newBuilder($connection));

        $entry->setDn($dn);

        $this->assertTrue($entry->save($attributes));
        $this->assertEquals($attributes['cn'][0], $entry->getCommonName());
        $this->assertEquals($attributes['sn'][0], $entry->sn[0]);
        $this->assertEquals($attributes['givenname'][0], $entry->givenname[0]);
    }

    public function test_save_for_update()
    {
        $connection = $this->newConnectionMock();

        $dn = 'cn=Testing,ou=Accounting,dc=corp,dc=org';

        $returnedRaw = [['dn' => $dn]];

        $connection->shouldReceive('read')->andReturn($connection);
        $connection->shouldReceive('getEntries')->andReturn($returnedRaw);

        $connection->shouldReceive('modifyBatch')->once()->withArgs([$dn, []])->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $entry = $this->newModel([], $this->newBuilder($connection));

        $entry->setRawAttributes(['dn' => $dn]);

        $this->assertTrue($entry->save());
    }

    public function test_save_for_update_with_attributes()
    {
        $connection = $this->newConnectionMock();

        $dn = 'cn=Testing,ou=Accounting,dc=corp,dc=org';

        $returnedRaw = [['dn' => $dn]];

        $attributes = [
            'cn' => ['John Doe'],
            'sn' => ['Doe'],
        ];

        $modifications = [
            [
                'attrib' => 'cn',
                'modtype' => 1,
                'values' => [
                    'John Doe',
                ]
            ],
            [
                'attrib' => 'sn',
                'modtype' => 1,
                'values' => [
                    'Doe',
                ]
            ]
        ];

        $connection->shouldReceive('read')->andReturn($connection);
        $connection->shouldReceive('getEntries')->andReturn($returnedRaw);

        $connection->shouldReceive('modifyBatch')->once()->withArgs([$dn, $modifications])->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $entry = $this->newModel([], $this->newBuilder($connection));

        $entry->setRawAttributes(['dn' => $dn]);

        $this->assertTrue($entry->save($attributes));
    }

    /**
     * @expectedException \Adldap\AdldapException
     */
    public function test_delete_failure()
    {
        $entry = $this->newModel();

        $entry->delete();
    }

    public function test_delete()
    {
        $connection = $this->newConnectionMock();

        $dn = 'cn=Testing,ou=Accounting,dc=corp,dc=org';

        $connection->shouldReceive('delete')->once()->withArgs([$dn])->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $entry = $this->newModel([], $this->newBuilder($connection));

        $entry->setRawAttributes(['dn' => $dn]);

        $this->assertTrue($entry->delete());
    }

    public function test_created_and_updated_at()
    {
        $date = '20150519034950.0Z';

        $model = $this->newModel()->setRawAttributes([
            'whencreated' => [$date],
            'whenchanged' => [$date],
        ]);

        $this->assertEquals($date, $model->getCreatedAt());
        $this->assertEquals($date, $model->getUpdatedAt());
        $this->assertEquals('2015-05-19 03:49:50', $model->getCreatedAtDate());
        $this->assertEquals('2015-05-19 03:49:50', $model->getUpdatedAtDate());
        $this->assertInternalType('int', $model->getCreatedAtTimestamp());
        $this->assertInternalType('int', $model->getUpdatedAtTimestamp());
    }

    public function test_convert_string_to_bool()
    {
        $entry = $this->mock('Adldap\Models\Entry')->makePartial();

        $entry->setSchema(new ActiveDirectory());

        $entry->shouldAllowMockingProtectedMethods();

        $this->assertNull($entry->convertStringToBool('test'));

        $this->assertTrue($entry->convertStringToBool('true'));
        $this->assertTrue($entry->convertStringToBool('TRUE'));
        $this->assertTrue($entry->convertStringToBool('TRue'));

        $this->assertFalse($entry->convertStringToBool('false'));
        $this->assertFalse($entry->convertStringToBool('FALSE'));
        $this->assertFalse($entry->convertStringToBool('FAlse'));
    }

    public function test_recursive_filtering_for_raw_attributes()
    {
        $rawAttributes = [
            'count' => 1,
            'one'  => [
                'count' => 1,
                'two'  => [
                    'count' => 1,
                    'three'  => [
                        'count' => 1,
                        'four'  => [
                            'count' => 1,
                        ],
                    ],
                ],
            ],
        ];

        $expected = [
            'one' => [
                'two' => [
                    'three' => [
                        'four' => [],
                    ],
                ],
            ],
        ];

        $entry = $this->newModel();

        $entry->setRawAttributes($rawAttributes);

        $this->assertEquals($expected, $entry->getAttributes());
    }

    public function test_move()
    {
        $rawAttributes = [
            'dn' => 'cn=Doe,dc=corp,dc=acme,dc=org',
        ];

        $connection = $this->newConnectionMock();

        $args = [
            'cn=Doe,dc=corp,dc=acme,dc=org',
            'cn=John',
            'ou=Accounts,dc=corp,dc=amce,dc=org',
            true,
        ];

        $connection
            ->shouldReceive('rename')->once()->withArgs($args)->andReturn(true)
            ->shouldReceive('read')->once()
            ->shouldReceive('getEntries')->once();

        $entry = $this->newModel([], $this->newBuilder($connection));

        $entry->setRawAttributes($rawAttributes);

        $this->assertTrue($entry->move($args[1], $args[2]));
    }

    public function test_dn_is_constructed_when_none_given_and_create_is_called()
    {
        $connection = $this->newConnectionMock();

        $addArgs = [
            'cn=John Doe,dc=corp,dc=local',
            ['cn' => ['John Doe']],
        ];

        $readArgs = [
            'cn=John Doe,dc=corp,dc=local',
            '(objectclass=*)',
            [],
            false,
            1
        ];

        $connection->shouldReceive('add')->once()->withArgs($addArgs)->andReturn(true);
        $connection->shouldReceive('read')->once()->withArgs($readArgs)->andReturn(true);
        $connection->shouldReceive('getEntries')->once()->andReturn([]);

        $builder = $this->newBuilder($connection);

        $builder->setDn('DC=corp,DC=local');

        $entry = $this->newModel([], $builder);

        $entry->setCommonName('John Doe');

        $this->assertTrue($entry->create());
    }

    public function test_get_original()
    {
        $model = $this->newModel()
            ->setRawAttributes(['cn' => ['John Doe']]);

        $model->cn = 'New Common Name';

        $this->assertEquals(['New Common Name'], $model->getAttributes()['cn']);
        $this->assertEquals(['John Doe'], $model->getOriginal()['cn']);
    }

    public function test_set_first_attribute()
    {
        $model = $this->newModel();

        $model->setFirstAttribute('cn', 'John Doe');

        $this->assertEquals(['cn' => ['John Doe']], $model->getAttributes());
    }

    public function test_get_first_attribute()
    {
        $model = $this->newModel([
            'cn' => 'John Doe',
        ]);

        $this->assertEquals('John Doe', $model->getFirstAttribute('cn'));
    }

    public function test_sync_raw()
    {
        $connection = $this->newConnectionMock();

        $model = $this->newModel([], $this->newBuilder($connection));

        $dn = 'cn=John Doe,dc=corp,dc=acme';

        $model->setRawAttributes(compact('dn'));

        $connection->shouldReceive('read')->once()->withArgs([$dn, "(objectclass=*)", [], false, 1]);
        $connection->shouldReceive('getEntries')->once()->andReturn(['count' => 1, ['dn' => 'cn=Jane Doe']]);

        $this->assertTrue($model->syncRaw());
        $this->assertEquals('cn=Jane Doe', $model->getDn());
    }

    public function test_modifications_are_cleared_on_save()
    {
        $connection = $this->newConnectionMock();

        $modification = new BatchModification('cn', 3, ['Jane Doe']);

        $connection->shouldReceive('modifyBatch')->once()->withArgs(['cn=John Doe,dc=acme,dc=org', [$modification->get()]])->andReturn(true);
        $connection->shouldReceive('read')->once()->andReturn(true);
        $connection->shouldReceive('getEntries')->once();

        $builder = $this->newBuilder($connection);

        $model = $this->newModel([], $builder)
            ->setRawAttributes([
                'dn' => 'cn=John Doe,dc=acme,dc=org',
                'cn' => ['John Doe']
            ]);

        $model->addModification($modification);

        $this->assertCount(1, $model->getModifications());

        $model->save();

        $this->assertCount(0, $model->getModifications());
    }

    public function test_adding_modification()
    {
        $model = $this->newModel();

        $mod = ['modtype' => 18, 'attrib' => 'mail'];

        $model->addModification($mod);

        $this->assertEquals($mod, $model->getModifications()[0]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_adding_invalid_modification()
    {
        $model = $this->newModel();

        $mod = 'test';

        $model->addModification($mod);

        $this->assertEmpty($model->getModifications());
    }

    public function test_adding_empty_non_existing_attribute()
    {
        $model = $this->newModel();

        $model->exists = true;

        $model->description = '';

        // Since the model exists and a non existent property is set to being
        // empty, no modification will be generated for the attribute.
        // Since no modifications are generated, the LDAP connection
        // isn't called, and will return true by default.
        $this->assertTrue($model->save());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_adding_invalid_modification_with_array()
    {
        $model = $this->newModel();

        $mod = ['modtype' => 18];

        $model->addModification($mod);
    }

    public function test_dn_builder_is_set_to_base_when_empty()
    {
        $model = $this->newModel();

        $dn = 'dc=base,dc=org';

        $model->getQuery()->setDn($dn);

        $this->assertEquals($dn, $model->getDnBuilder()->get());
    }

    public function test_max_password_in_days()
    {
        $model = $this->newModel(['maxPwdAge' => -8640000000000]);

        $this->assertEquals(10, $model->getMaxPasswordAgeDays());
    }

    public function test_max_password_in_days_returns_zero_on_null()
    {
        $model = $this->newModel(['maxPwdAge' => null]);

        $this->assertEquals(0, $model->getMaxPasswordAgeDays());
    }
}
