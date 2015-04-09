<?php

namespace adLDAP\Interfaces;

/**
 * The Connection interface used for making
 * connections. Implementing this interface
 * on connection classes helps unit and functional
 * testing classes that require a connection.
 *
 * Interface ConnectionInterface
 * @package adLDAP\Interfaces
 */
interface ConnectionInterface
{
    /**
     * Returns true / false if the
     * current connection is supported
     * on the current PHP install.
     *
     * @return bool
     */
    public function isSupported();

    /**
     * Returns true / false if the
     * current connection instance is using
     * SSL.
     *
     * @return bool
     */
    public function isUsingSSL();

    /**
     * @return bool
     */
    public function isUsingTLS();

    /**
     * Returns true / false if the current
     * connection instance is using single
     * sign on
     *
     * @return bool
     */
    public function isUsingSSO();

    /**
     * Get the current connection.
     *
     * @return mixed
     */
    public function getConnection();

    /**
     * Retrieve the entries from a search result.
     *
     * @param $searchResult
     * @return mixed
     */
    public function getEntries($searchResult);

    /**
     * Retrieve the last error on the current
     * connection.
     *
     * @return mixed
     */
    public function getLastError();

    /**
     * Sets an option on the current connection.
     *
     * @param int $option
     * @param mixed $value
     * @return mixed
     */
    public function setOption($option, $value);

    /**
     * Connects to the specified hostname using the
     * specified port.
     *
     * @param $hostname
     * @param int $port
     * @return mixed
     */
    public function connect($hostname, $port = 389);

    /**
     * Starts a connection using TLS.
     *
     * @return mixed
     */
    public function startTLS();

    /**
     * Binds to the current connection using
     * the specified username and password. If sasl
     * is true, the current connection is bound using
     * SASL.
     *
     * @param string $username
     * @param string $password
     * @param bool $sasl
     * @return mixed
     */
    public function bind($username, $password, $sasl = false);

    /**
     * Closes the current connection.
     *
     * @return mixed
     */
    public function close();

    /**
     * @param string $dn
     * @param string $filter
     * @param array $fields
     * @return mixed
     */
    public function search($dn, $filter, array $fields);

    /**
     * @param $dn
     * @param $filter
     * @param array $fields
     * @return mixed
     */
    public function read($dn, $filter, array $fields);
}