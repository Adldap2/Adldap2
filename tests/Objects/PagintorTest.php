<?php

namespace Adldap\Tests\Objects;

use Adldap\Objects\Paginator;
use Adldap\Tests\FunctionalTestCase;

class PagintorTest extends FunctionalTestCase
{
    public function testPaginatorConstruct()
    {
        $paginator = new Paginator(array(), 50, 0, 0);

        $this->assertEquals(0, $paginator->getPages());
        $this->assertEquals(0, $paginator->count());
        $this->assertEquals(50, $paginator->getPerPage());
    }

    public function testPaginatorPages()
    {
        $data = array(
            array(
                'name' => 'John',
            ),
            array(
                'name' => 'Suzy',
            )
        );

        $paginator = new Paginator($data, 1, 0, 2);

        $this->assertEquals($data, $paginator->getAttributes());

        // Tests that only the first entry is shown
        foreach($paginator as $entry)
        {
            $this->assertEquals(array('name' => 'John'), $entry);
        }

        $paginator2 = new Paginator($data, 1, 1, 2);

        foreach($paginator2 as $entry)
        {
            $this->assertEquals(array('name' => 'Suzy'), $entry);
        }
    }
}