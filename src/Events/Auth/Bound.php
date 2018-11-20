<?php

namespace Adldap\Events\Auth;

use Adldap\Connections\ConnectionInterface;

class Bound
{
    /**
     * The connection that the username and password has been bound on.
     *
     * @var ConnectionInterface
     */
    public $connection;

    /**
     * The username that was used for binding.
     *
     * @var string
     */
    public $username;

    /**
     * The password that was used for binding.
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
