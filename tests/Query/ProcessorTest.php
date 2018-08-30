<?php

namespace Adldap\Tests\Query;

use Adldap\Models\Entry;
use Adldap\Tests\TestCase;
use Adldap\Query\Processor;
use Adldap\Schemas\SchemaInterface;

class ProcessorTest extends TestCase
{
    public function test_default_schema_entry_model_is_used_when_default_models_are_created()
    {
        $b = $this->newBuilder();

        $s = $this->mock(SchemaInterface::class);

        $s->shouldReceive('objectClass')->once()->andReturn('objectClass')
            ->shouldReceive('entryModel')->once()->andReturn(Entry::class);

        $b->setSchema($s);

        $p = new Processor($b);

        $this->assertInstanceOf(Entry::class, $p->newModel());
    }
}
