<?php

namespace Adldap\tests\Models;

use Adldap\Models\Group;
use Adldap\Tests\TestCase;

class GroupTest extends TestCase
{
    protected function newGroupModel($attributes, $builder, $schema = null)
    {
        return new Group($attributes, $builder, $schema);
    }
}
