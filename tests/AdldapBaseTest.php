<?php

namespace Adldap\Tests;

use Adldap\Classes\AdldapBase;

class AdldapBaseTest extends FunctionalTestCase
{
    protected function newAdldapMock()
    {
        return $this->mock('Adldap\Adldap');
    }

    public function testBaseConstruct()
    {
        $ad = $this->mock('Adldap\Adldap')->makePartial();

        $search = new AdldapBase($ad);

        $this->assertEquals(get_class($ad), get_class($search->getAdldap()));
    }
}