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
        $query = $this->newBuilder();

        $schema = $this->mock(SchemaInterface::class);
        $schema->shouldReceive('entryModel')->once()->andReturn(Entry::class);

        $query->setSchema($schema);

        $this->assertInstanceOf(Entry::class, (new Processor($query))->newModel());
    }
}
