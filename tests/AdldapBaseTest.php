<?php

namespace Adldap\Tests;

class AdldapBaseTest extends FunctionalTestCase
{
    protected function newAdldapMock()
    {
        return $this->mock('Adldap\Adldap');
    }

    public function testBaseConstruct()
    {
        $ad = $this->mock('Adldap\Adldap')->makePartial();

        $base = $this->mock('Adldap\Classes\AbstractAdldapBase');

        $base->shouldReceive('getAdldap')->once()->andReturn($ad);

        $this->assertEquals(get_class($ad), get_class($base->getAdldap()));
    }
}
