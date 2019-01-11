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
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
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
        $type = is_a($event, Failed::class) ? 'warning' : 'info';

        $operation = get_class($event);

        $message = "({$event->connection->getHost()}) - Operation: {$operation} - Username: {$event->username} - Result: {$event->connection->getLastError()}";

        $this->logger->$type($message);
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
        $operation = get_class($event);

        $on = get_class($event->model);

        $connection = $event->model->getQuery()->getConnection();

        $message = "({$connection->getHost()}) - Operation: {$operation} - On: {$on} - Distinguished Name: {$event->model->getDn()}";

        $this->logger->info($message);
    }
}
