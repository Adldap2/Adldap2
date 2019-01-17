<?php

namespace Adldap\Tests\Models;

use Adldap\Adldap;
use Adldap\Tests\TestCase;
use Adldap\Models\Entry;
use Adldap\Models\Model;
use Adldap\Models\BatchModification;
use Adldap\Models\Events\Created;
use Adldap\Models\Events\Creating;
use Adldap\Models\Events\Deleted;
use Adldap\Models\Events\Deleting;
use Adldap\Models\Events\Updated;
use Adldap\Models\Events\Updating;
use Adldap\Schemas\OpenLDAP;
use Adldap\Schemas\ActiveDirectory;

class ModelTest extends TestCase
{
    protected function newModel(array $attributes = [], $builder = null, $schema = null)
    {
        $builder = $builder ?: $this->newBuilder();

        return new Entry($attributes, $builder, $schema);
    }

    public function test_construct()
    {
        $attributes = [
            'cn' => ['Common Name'],
            'samaccountname' => ['Account Name'],
        ];

        $entry = $this->newModel($attributes, $this->newBuilder());

        $this->assertEquals($attributes, $entry->getAttributes());
    }

    public function test_set_raw_attributes()
    {
        $rawAttributes = [
            'cn' => ['Common Name'],
            'samaccountname' => ['Account Name'],
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
            'cn' => ['Common Name'],
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
            'cn' => ['Common Name'],
            'samaccountname' => ['Account Name'],
            'dn' => 'dc=corp,dc=org',
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
            'cn' => ['Common Name'],
            'samaccountname' => ['Account Name'],
            'dn' => 'dc=corp,dc=org',
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
            'cn' => ['Common Name'],
            'samaccountname' => ['Account Name'],
            'dn' => 'dc=corp,dc=org',
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
            'cn' => ['Common Name'],
            'samaccountname' => ['Account Name'],
            'dn' => 'dc=corp,dc=org',
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
            'cn' => ['Common Name'],
            'samaccountname' => ['Account Name'],
            'name' => ['Name'],
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
            'cn' => ['John Doe'],
            'givenname' => ['John'],
            'sn' => ['Doe'],
        ];

        $returnedRaw = [
            'count' => 1,
            [
                'cn' => ['John Doe'],
                'givenname' => ['John'],
                'sn' => ['Doe'],
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
            'cn' => ['John Doe'],
            'givenname' => ['John'],
            'sn' => ['Doe'],
        ];

        $dn = 'cn=John Doe,ou=Accounting,dc=corp,dc=org';

        $returnedRaw = [
            'count' => 1,
            [
                'cn' => ['John Doe'],
                'givenname' => ['John'],
                'sn' => ['Doe'],
                'dn' => $dn,
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
            'cn' => ['John Doe'],
            'givenname' => ['John'],
            'sn' => ['Doe'],
        ];

        $dn = 'cn=John Doe,ou=Accounting,dc=corp,dc=org';

        $returnedRaw = [
            'count' => 1,
            [
                'cn' => ['John Doe'],
                'givenname' => ['John'],
                'sn' => ['Doe'],
                'dn' => $dn,
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
            'one' => [
                'count' => 1,
                'two' => [
                    'count' => 1,
                    'three' => [
                        'count' => 1,
                        'four' => [
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

    public function test_rename()
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

        $this->assertTrue($entry->rename($args[1], $args[2]));
    }

    public function test_move()
    {
        $rawAttributes = [
            'dn' => 'cn=Doe,dc=corp,dc=acme,dc=org',
        ];

        $connection = $this->newConnectionMock();

        $args = [
            'cn=Doe,dc=corp,dc=acme,dc=org',
            'cn=Doe',
            'ou=Accounts,dc=corp,dc=amce,dc=org',
            true,
        ];

        $connection
            ->shouldReceive('rename')->once()->withArgs($args)->andReturn(true)
            ->shouldReceive('read')->once()
            ->shouldReceive('getEntries')->once();

        $entry = $this->newModel([], $this->newBuilder($connection));

        $entry->setRawAttributes($rawAttributes);

        $this->assertTrue($entry->move($args[2]));
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
            ['*'],
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

        $connection->shouldReceive('read')->once()->withArgs([$dn, "(objectclass=*)", ['*'], false, 1]);
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

    public function test_get_dirty_single_value()
    {
        $model = $this->newModel(['cn' => 'John Doe']);

        $model->setCommonName('New Name');

        $this->assertEquals(['cn' => ['New Name']], $model->getDirty());
    }

    public function test_get_dirty_same_value()
    {
        $model = $this->newModel(['cn' => 'John Doe']);

        $model->syncOriginal();

        $model->setCommonName('John Doe');

        $this->assertEmpty($model->getDirty());

        $model->setCommonName('John D');

        $this->assertEquals(['cn' => ['John D']], $model->getDirty());
    }

    public function test_get_dirty_multiple_values()
    {
        $attributes = [
            'proxyaddress' => [
                'one',
                'two',
                'three',
            ],
        ];

        $model = $this->newModel($attributes);

        $model->syncOriginal();

        $model->setAttribute('proxyaddresses', [
            'one',
        ]);

        $this->assertEquals([
            'proxyaddresses' => [0 => 'one']
        ], $model->getDirty());
    }

    public function test_get_dirty_multiple_values_with_indices_reset_keeps_order()
    {
        $attributes = [
            'proxyaddress' => [
                'one',
                'two',
                'three',
            ],
        ];

        $model = $this->newModel($attributes);

        $model->syncOriginal();

        $model->setAttribute('proxyaddresses', [
            3 => 'three',
            2 => 'two',
        ]);

        $this->assertEquals([
            'proxyaddresses' => [
                0 => 'three',
                1 => 'two',
            ]
        ], $model->getDirty());
    }

    public function test_get_dirty_from_null_value()
    {
        $model = $this->newModel();

        $this->assertNull($model->getCommonName());

        $model->setCommonName('New Name');

        $this->assertEquals(['cn' => ['New Name']], $model->getDirty());
    }

    public function test_get_dirty_to_null_value()
    {
        $model = $this->newModel(['cn' => 'John Doe']);

        $model->syncOriginal();

        $model->setCommonName(null);

        $this->assertEquals(['cn' => [null]], $model->getDirty());

        $this->assertEquals([0 => [
            'attrib' => 'cn',
            'modtype' => 18,
        ]], $model->getModifications());
    }

    public function test_models_constructed_with_an_array_dn_is_set_properly()
    {
        $model = $this->newModel();

        $dn = 'cn=Jdoe,dc=acme,dc=org';

        $model->setRawAttributes(['dn' => [$dn]]);

        $this->assertEquals($model->getDistinguishedName(), $dn);
    }

    public function test_get_managed_by_user_queries_for_user()
    {
        $dn = 'cn=Jdoe,dc=acme,dc=org';

        $managedByUser = $this->newModel(compact('dn'));

        $b = $this->newBuilderMock();

        $b->shouldReceive('newInstance')->once()->andReturnSelf()
            ->shouldReceive('findByDn')->once()->with($dn)->andReturn($managedByUser);

        $model = $this->newModel(['managedby' => $dn], $b);

        $this->assertEquals($managedByUser, $model->getManagedByUser());
    }

    public function test_set_managed_by_accepts_model_instance()
    {
        $model = $this->newModel();

        $dn = 'cn=Jdoe,dc=acme,dc=org';

        $managedByUser = $this->newModel(compact('dn'));

        $model->setManagedBy($managedByUser);

        $this->assertEquals($dn, $model->getManagedBy());
    }

    public function test_case_sensitivity_for_setting_and_retrieving_attributes()
    {
        $m = $this->newModel([
            'CN' => 'John Doe',
            'givenName' => 'Doe, John',
            'memberOf' => [],
        ]);

        $this->assertEquals('John Doe', $m->getFirstAttribute('cn'));
        $this->assertEquals('John Doe', $m->getFirstAttribute('cN'));
        $this->assertEquals('Doe, John', $m->getFirstAttribute('givenname'));
        $this->assertEquals('Doe, John', $m->getFirstAttribute('GiVENnAme'));
        $this->assertEquals([], $m->getAttribute('memberof'));
        $this->assertEquals([], $m->getAttribute('mEMBEROF'));
    }

    /** @expectedException \UnexpectedValueException */
    public function test_creating_entry_without_valid_dn_throws_exception()
    {
        $b = $this->newBuilder()->in('dc=acme,dc=org');

        $m = $this->newModel([], $b);

        $m->save();
    }

    public function test_creating_model_fires_events()
    {
        $c = $this->newConnectionMock();

        $m = $this->newModel([], $this->newBuilder($c));

        $d = Adldap::getEventDispatcher();

        $firedCreating = false;
        $firedCreated = false;

        $d->listen(Creating::class, function (Creating $e) use (&$firedCreating) {
            $this->assertInstanceOf(Model::class, $e->getModel());

             $firedCreating = true;
        });

        $d->listen(Created::class, function (Created $e) use (&$firedCreated) {
            $this->assertInstanceOf(Model::class, $e->getModel());

            $firedCreated = true;
        });

        $c
            ->shouldReceive('add')->once()->andReturn(true)
            ->shouldReceive('read')->once()
            ->shouldReceive('getEntries')->once();

        $m->save([
            'dn' => 'cn=jdoe,dc=acme,dc=org',
        ]);

        $this->assertTrue($firedCreating);
        $this->assertTrue($firedCreated);
    }

    public function test_updating_model_fires_events()
    {
        $c = $this->newConnectionMock();

        $m = $this->newModel([], $this->newBuilder($c));

        $m->setRawAttributes([
            'dn' => 'cn=jdoe,dc=acme,dc=org'
        ]);

        $d = Adldap::getEventDispatcher();

        $firedUpdating = false;
        $firedUpdated = false;

        $d->listen(Updating::class, function (Updating $e) use (&$firedUpdating) {
            $this->assertInstanceOf(Model::class, $e->getModel());

            $firedUpdating = true;
        });

        $d->listen(Updated::class, function (Updated $e) use (&$firedUpdated) {
            $this->assertInstanceOf(Model::class, $e->getModel());

            $firedUpdated = true;
        });

        $c
            ->shouldReceive('modifyBatch')->once()->andReturn(true)
            ->shouldReceive('read')->once()
            ->shouldReceive('getEntries')->once();

        $m->save([
            'cn' => 'new'
        ]);

        $this->assertTrue($firedUpdating);
        $this->assertTrue($firedUpdated);
    }

    public function test_deleting_model_fires_events()
    {
        $c = $this->newConnectionMock();

        $m = $this->newModel([], $this->newBuilder($c));

        $m->setRawAttributes([
            'dn' => 'cn=jdoe,dc=acme,dc=org'
        ]);

        $d = Adldap::getEventDispatcher();

        $firedDeleting = false;
        $firedDeleted = false;

        $d->listen(Deleting::class, function (Deleting $e) use (&$firedDeleting) {
            $this->assertInstanceOf(Model::class, $e->getModel());

            $firedDeleting = true;
        });

        $d->listen(Deleted::class, function (Deleted $e) use (&$firedDeleted) {
            $this->assertInstanceOf(Model::class, $e->getModel());

            $firedDeleted = true;
        });

        $c->shouldReceive('delete')->once()->andReturn(true);

        $m->delete();

        $this->assertTrue($firedDeleting);
        $this->assertTrue($firedDeleted);
    }

    public function test_model_events_can_be_listened_for_with_wildcard()
    {
        $c = $this->newConnectionMock();

        $m = $this->newModel([], $this->newBuilder($c));

        $m->setRawAttributes([
            'dn' => 'cn=jdoe,dc=acme,dc=org'
        ]);

        $d = Adldap::getEventDispatcher();

        $firedDeleting = false;
        $firedDeleted = false;

        $d->listen('Adldap\Models\Events\*', function ($event, $payload) use (&$firedDeleting, &$firedDeleted) {
            if ($event == 'Adldap\Models\Events\Deleting') {
                $firedDeleting = true;
            } else if ($event == 'Adldap\Models\Events\Deleted') {
                $firedDeleted = true;
            }
        });

        $c->shouldReceive('delete')->once()->andReturn(true);

        $m->delete();

        $this->assertTrue($firedDeleting);
        $this->assertTrue($firedDeleted);
    }

    public function test_retrieving_guid_with_other_schema_returns_proper_value()
    {
        $m = $this->newModel([
            'entryuuid' => 'cdc718a0-8c3c-1034-8646-e30b83a2e38d',
        ]);

        $m->setSchema(new OpenLDAP());

        $this->assertEquals($m->entryuuid[0], $m->getConvertedGuid());
    }
}
