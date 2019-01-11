<?php

namespace Adldap\Log;

use Psr\Log\LoggerInterface;
use Adldap\Auth\Events\Failed;
use Adldap\Auth\Events\Event as AuthEvent;
use Adldap\Models\Events\Event as ModelEvent;

class EventLogger
{
    /**
     * The logger instance.
     *
     * @var LoggerInterface|null
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * Logs an authentication event.
     *
     * @param AuthEvent $event
     *
     * @return void
     */
    public function auth(AuthEvent $event)
    {
        if (isset($this->logger)) {
            $type = is_a($event, Failed::class) ? 'warning' : 'info';

            $operation = get_class($event);

            $message = "LDAP ({$event->connection->getHost()}) - Operation: {$operation} - Username: {$event->username} - Result: {$event->connection->getLastError()}";

            $this->logger->$type($message);
        }
    }

    /**
     * Logs a model event.
     *
     * @param ModelEvent $event
     *
     * @return void
     */
    public function model(ModelEvent $event)
    {
        if (isset($this->logger)) {
            $operation = get_class($event);

            $on = get_class($event->model);

            $connection = $event->model->getQuery()->getConnection();

            $message = "LDAP ({$connection->getHost()}) - Operation: {$operation} - On: {$on} - Distinguished Name: {$event->model->getDn()}";

            $this->logger->info($message);
        }
    }
}
