<?php

namespace Adldap\Auth\Events;

use Adldap\Connections\ConnectionInterface;

abstract class Event
{
    /**
     * The connection that the username and password is being bound on.
     *
     * @var ConnectionInterface
     */
    public $connection;

    /**
     * The username that is being used for binding.
     *
     * @var string
     */
    public $username;

    /**
     * The password that is being used for binding.
     *
     * @var string
     */
    public $password;

    /**
     * Constructor.
     *
     * @param ConnectionInterface $connection
     * @param string              $username
     * @param string              $password
     */
    public function __construct(ConnectionInterface $connection, $username, $password)
    {
        $this->connection = $connection;
        $this->username = $username;
        $this->password = $password;
    }
}
