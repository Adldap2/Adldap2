<?php

namespace Adldap\Tests\Models;

use Adldap\Models\Entry;
use Adldap\Tests\UnitTestCase;

class EntryTest extends UnitTestCase
{
    protected function newConnectionMock()
    {
        return $this->mock('Adldap\Connections\Ldap');
    }

    public function testConstruct()
    {
        $attributes = [
            'cn' => 'Common Name',
            'samaccountname' => 'Account Name',
        ];

        $entry = new Entry($attributes, $this->newConnectionMock());

        $this->assertEquals($attributes, $entry->getAttributes());
    }

    public function testSetRawAttributes()
    {
        $attributes = [
            'cn' => ['Common Name'],
            'samaccountname' => ['Account Name'],
        ];

        $entry = new Entry([], $this->newConnectionMock());

        $entry->setRawAttributes($attributes);

        $this->assertTrue($entry->exists);
        $this->assertEquals($attributes, $entry->getAttributes());
    }
}
