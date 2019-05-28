<?php

namespace Adldap\Tests\Log;

use Adldap\Models\User;
use Adldap\Tests\TestCase;
use Adldap\Log\EventLogger;
use Psr\Log\LoggerInterface;
use Adldap\Auth\Events\Failed;
use Adldap\Auth\Events\Binding;
use Adldap\Query\Events\Search;
use Adldap\Models\Events\Creating;
use Adldap\Connections\ConnectionInterface;

class EventLoggerTest extends TestCase
{
    public function test_auth_events_are_logged()
    {
        $connection = $this->mock(ConnectionInterface::class);

        $connection
            ->shouldReceive('getHost')->once()->andReturn('ldap://192.168.1.1')
            ->shouldReceive('getName')->once()->andReturn('domain-a');

        $logger = $this->mock(LoggerInterface::class);

        $log = 'LDAP (ldap://192.168.1.1)'
            .' - Connection: domain-a'
            .' - Operation: Binding'
            .' - Username: jdoe@acme.org';

        $logger->shouldReceive('info')->once()->with($log);

        $eventLogger = new EventLogger($logger);

        $this->assertNull($eventLogger->log(new Binding($connection, 'jdoe@acme.org', 'super-secret')));
    }

    public function test_failed_auth_event_reports_result()
    {
        $connection = $this->mock(ConnectionInterface::class);

        $connection
            ->shouldReceive('getHost')->once()->andReturn('ldap://192.168.1.1')
            ->shouldReceive('getName')->once()->andReturn('domain-a')
            ->shouldReceive('getLastError')->once()->andReturn('Invalid Credentials');

        $logger = $this->mock(LoggerInterface::class);

        $log = 'LDAP (ldap://192.168.1.1)'
            .' - Connection: domain-a'
            .' - Operation: Failed'
            .' - Username: jdoe@acme.org'
            .' - Reason: Invalid Credentials';

        $logger->shouldReceive('warning')->once()->with($log);

        $eventLogger = new EventLogger($logger);

        $this->assertNull($eventLogger->log(new Failed($connection, 'jdoe@acme.org', 'super-secret')));
    }

    public function test_model_events_are_logged()
    {
        $connection = $this->mock(ConnectionInterface::class);

        $connection
            ->shouldReceive('getHost')->once()->andReturn('ldap://192.168.1.1')
            ->shouldReceive('getName')->once()->andReturn('domain-a');

        $logger = $this->mock(LoggerInterface::class);

        $dn = 'cn=John Doe,dc=corp,dc=acme,dc=org';

        $log = 'LDAP (ldap://192.168.1.1)'
            .' - Connection: domain-a'
            .' - Operation: Creating'
            ." - On: Adldap\Models\User"
            ." - Distinguished Name: $dn";

        $logger->shouldReceive('info')->once()->with($log);

        $query = $this->newBuilder($connection);

        $user = new User(['dn' => $dn], $query);

        $eventLogger = new EventLogger($logger);

        $this->assertNull($eventLogger->log(new Creating($user)));
    }

    public function test_query_events_are_logged()
    {
        $connection = $this->mock(ConnectionInterface::class);

        $connection
            ->shouldReceive('getHost')->once()->andReturn('ldap://192.168.1.1')
            ->shouldReceive('getName')->once()->andReturn('domain-a');

        $logger = $this->mock(LoggerInterface::class);

        $log = 'LDAP (ldap://192.168.1.1)'
            .' - Connection: domain-a'
            .' - Operation: Search'
            .' - Base DN: '
            .' - Filter: (objectclass=*)'
            .' - Selected: (*)'
            .' - Time Elapsed: 10';

        $logger->shouldReceive('info')->once()->with($log);

        $eventLogger = new EventLogger($logger);

        $query = $this->newBuilder($connection);

        $this->assertNull($eventLogger->log(new Search($query, 10)));
    }
}
