<?php

namespace Adldap\Tests;

use Adldap\Adldap;

class AdldapTest extends UnitTestCase
{
    public function testConstruct()
    {
        $config = $this->mock('Adldap\Connections\Configuration');

        $config->shouldReceive('getUseSSL')->once()->andReturn(false);
        $config->shouldReceive('getUseTLS')->once()->andReturn(false);
        $config->shouldReceive('getUseSSO')->once()->andReturn(false);
        $config->shouldReceive('getDomainControllers')->once()->andReturn([]);
        $config->shouldReceive('getPort')->once()->andReturn(389);
        $config->shouldReceive('getFollowReferrals')->once()->andReturn(true);
        $config->shouldReceive('getAdminUsername')->once()->andReturn('admin');
        $config->shouldReceive('getAdminPassword')->once()->andReturn('password');
        $config->shouldReceive('getAccountSuffix')->once()->andReturn('@corp');

        $ad = new Adldap($config);

        $this->assertInstanceOf('Adldap\Connections\Configuration', $ad->getConfiguration());
    }
}
