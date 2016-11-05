<?php

namespace Adldap\tests\Models;

use Adldap\Models\Group;
use Adldap\Query\Builder;
use Adldap\Tests\TestCase;
use Adldap\Schemas\ActiveDirectory;

class GroupTest extends TestCase
{
    protected function newGroupModel($attributes, $builder, $schema = null)
    {
        return new Group($attributes, $builder, $schema);
    }

    public function test_get_members()
    {
        $expected = $this->newGroupModel([
            'cn' => ['test1'],
            'dn' => 'cn=test1,dc=corp,dc=org'
        ], $this->newBuilder());

        $builder = $this->mock(Builder::class);

        $builder
            ->shouldReceive('getSchema')->once()->andReturn(new ActiveDirectory())
            ->shouldReceive('newInstance')->once()->andReturn($builder)
            ->shouldReceive('findByDn')->once()->andReturn($expected);

        $group = $this->newGroupModel([
            'member' => ['cn=test1,dc=corp,dc=org'],
        ], $builder);

        $members = $group->getMembers();
        $this->assertCount(1, $members);
        $this->assertEquals($expected->getCommonName(), $members->first()->getCommonName());
        $this->assertEquals($expected->getDn(), $members->first()->getDn());
    }

    public function test_get_member_names()
    {
        $group = $this->newGroupModel([
            'member' => [
                'cn=test1,dc=corp,dc=org',
                'cn=test2,dc=corp,dc=org',
                'cn=test3,dc=corp,dc=org',
            ]
        ], $this->newBuilder());

        $this->assertEquals([
            'test1',
            'test2',
            'test3',
        ], $group->getMemberNames());
    }

    public function test_in_group()
    {
        $builder = $this->newBuilder();

        $group1 = $this->newGroupModel([], $builder)->setRawAttributes([
            'cn' => [
                'test1',
            ],
            'dn' => 'cn=test1,dc=corp,dc=org',
        ]);

        $group2 = $this->newGroupModel([], $builder)->setRawAttributes([
            'cn' => [
                'test2',
            ],
            'dn' => 'cn=test2,dc=corp,dc=org',
        ]);

        $group3 = $this->newGroupModel([], $builder)->setRawAttributes([
            'cn' => [
                'test3',
            ],
            'dn' => 'cn=test3,dc=corp,dc=org',
        ]);

        $group4 = $this->newGroupModel([], $builder)->setRawAttributes([
            'cn' => [
                'test4',
            ],
            'dn' => 'cn=test4,dc=corp,dc=org',
        ]);

        $groups = collect([$group1, $group2, $group3]);

        $group = $this->mock(Group::class, [[], $builder])->makePartial();

        $group->shouldReceive('getGroups')->once()->andReturn($groups);

        $this->assertFalse($group->inGroup('test'));
        $this->assertFalse($group->inGroup($group4));
        $this->assertFalse($group->inGroup([$group1, $group4]));
        $this->assertFalse($group->inGroup(['test', 'test1']));

        $this->assertTrue($group->inGroup('test1'));
        $this->assertTrue($group->inGroup(['test1', 'cn=test2,dc=corp,dc=org', 'cn=test3,dc=corp,dc=org']));
        $this->assertTrue($group->inGroup([$group1, $group2, 'test3']));
    }

    public function test_get_members_with_range(){

        $builder = $this->mock(Builder::class);

        $builder
            ->shouldReceive('getSchema')->zeroOrMoreTimes()->andReturn(new ActiveDirectory())
            ->shouldReceive('newInstance')->zeroOrMoreTimes()->andReturn($builder);

        $group = $this->newGroupModel([
            'member' => [
                'count'=>0
            ],
            'member;range=0-1'=>[
                'cn=test1,dc=corp,dc=org',
                'cn=test1,dc=corp,dc=org',
            ],
        ], $builder);

        $expectedMembers =[
            $this->newGroupModel([
                'cn' => ['test1'],
                'dn' => 'cn=test1,dc=corp,dc=org'
            ], $builder),
            $this->newGroupModel([
                'cn' => ['test2'],
                'dn' => 'cn=test2,dc=corp,dc=org'
            ], $builder),
            $this->newGroupModel([
                'cn' => ['test3'],
                'dn' => 'cn=test3,dc=corp,dc=org'
            ], $builder),
            $this->newGroupModel([
                'cn' => ['test4'],
                'dn' => 'cn=test4,dc=corp,dc=org'
            ], $builder)
        ]
        ;

        $expectedGroup = $this->newGroupModel([
            'member' => [
                'count'=>0
            ],
            'member;range=2-*'=>[
                'cn=test1,dc=corp,dc=org',
                'cn=test1,dc=corp,dc=org',
            ],
        ], $builder);


        $builder
            ->shouldReceive('findByDn')->once()->andReturn($expectedMembers[0])
            ->shouldReceive('findByDn')->once()->andReturn($expectedMembers[1])
            ->shouldReceive('findByDn')->once()->andReturn($expectedGroup)
            ->shouldReceive('findByDn')->once()->andReturn($expectedMembers[2])
            ->shouldReceive('findByDn')->once()->andReturn($expectedMembers[3])
        ;

        $members = $group->getMembers();

        $this->assertCount(4, $members);
        $this->assertEquals($expectedMembers[0]->getCommonName(), $members->shift()->getCommonName());
        $this->assertEquals($expectedMembers[1]->getCommonName(), $members->shift()->getCommonName());
        $this->assertEquals($expectedMembers[2]->getCommonName(), $members->shift()->getCommonName());
        $this->assertEquals($expectedMembers[3]->getCommonName(), $members->shift()->getCommonName());

    }
}
