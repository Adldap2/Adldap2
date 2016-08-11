<?php

namespace Adldap\Connections;

use Traversable;
use Adldap\Objects\DistinguishedName;
use Adldap\Exceptions\ConfigurationException;
use Adldap\Exceptions\InvalidArgumentException;
use Adldap\Contracts\Connections\ConnectionInterface;

class Configuration
{
    /**
     * The LDAP base dn.
     *
     * @var string
     */
    protected $baseDn;

    /**
     * The boolean to instruct the LDAP connection
     * whether or not to follow referrals.
     *
     * https://msdn.microsoft.com/en-us/library/ms677913(v=vs.85).aspx
     *
     * @var bool
     */
    protected $followReferrals = false;

    /**
     * The LDAP port to use when connecting to
     * the domain controllers.
     *
     * @var string
     */
    protected $port = ConnectionInterface::PORT;

    /**
     * The LDAP network timeout setting.
     *
     * @var int
     */
    protected $timeout = 5;

    /**
     * Determines whether or not to use SSL
     * with the current LDAP connection.
     *
     * @var bool
     */
    protected $useSSL = false;

    /**
     * Determines whether or not to use TLS
     * with the current LDAP connection.
     *
     * @var bool
     */
    protected $useTLS = false;

    /**
     * The domain controllers to connect to.
     *
     * @var array
     */
    protected $domainControllers = [];

    /**
     * The LDAP account suffix.
     *
     * @var string
     */
    protected $accountSuffix;

    /**
     * The LDAP account prefix.
     *
     * @var string
     */
    protected $accountPrefix;

    /**
     * The LDAP administrator username.
     *
     * @var string
     */
    private $adminUsername;

    /**
     * The LDAP administrator password.
     *
     * @var string
     */
    private $adminPassword;

    /**
     * The LDAP administrator account suffix.
     *
     * @var string
     */
    private $adminAccountSuffix;

    /**
     * Constructor.
     *
     * @param array|Traversable $options
     *
     * @throws InvalidArgumentException
     */
    public function __construct($options = [])
    {
        $this->fill($options);
    }

    /**
     * Sets the base DN property.
     *
     * @param string $dn
     */
    public function setBaseDn($dn)
    {
        // We'll construct a new Distinguished name with
        // the DN we're given so we know it's valid.
        $this->baseDn = (new DistinguishedName($dn))->get();
    }

    /**
     * Returns the Base DN string.
     *
     * @return string|null
     */
    public function getBaseDn()
    {
        return $this->baseDn;
    }

    /**
     * Sets the follow referrals option.
     *
     * @param bool $bool
     */
    public function setFollowReferrals($bool)
    {
        $this->followReferrals = (bool) $bool;
    }

    /**
     * Returns the follow referrals option.
     *
     * @return int
     */
    public function getFollowReferrals()
    {
        return $this->followReferrals;
    }

    /**
     * Sets the port option to use when connecting.
     *
     * @param string|int $port
     *
     * @throws ConfigurationException
     */
    public function setPort($port)
    {
        if (!is_numeric($port)) {
            throw new ConfigurationException('Your configured LDAP port must be an integer.');
        }

        $this->port = (string) $port;
    }

    /**
     * Returns the port option.
     *
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sets the timeout option.
     *
     * @param int $timeout
     *
     * @throws \Adldap\Exceptions\ConfigurationException
     */
    public function setTimeout($timeout)
    {
        if (!is_numeric($timeout)) {
            throw new ConfigurationException('Your configured LDAP timeout must be an integer.');
        }

        $this->timeout = $timeout;
    }

    /**
     * Returns the timeout option.
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Sets the option whether or not to use SSL when connecting.
     *
     * @param bool $bool
     *
     * @throws ConfigurationException
     */
    public function setUseSSL($bool)
    {
        $bool = (bool) $bool;

        if ($this->useTLS && $bool === true) {
            $message = 'You can only specify the use of one security protocol. TLS is already enabled.';

            throw new ConfigurationException($message);
        }

        $this->useSSL = $bool;
    }

    /**
     * Returns the use SSL option.
     *
     * @return bool
     */
    public function getUseSSL()
    {
        return $this->useSSL;
    }

    /**
     * Sets the option whether or not to use TLS when connecting.
     *
     * @param bool $bool
     *
     * @throws ConfigurationException
     */
    public function setUseTLS($bool)
    {
        $bool = (bool) $bool;

        if ($this->useSSL && $bool === true) {
            $message = 'You can only specify the use of one security protocol. SSL is already enabled.';

            throw new ConfigurationException($message);
        }

        $this->useTLS = $bool;
    }

    /**
     * Returns the use TLS option.
     *
     * @return bool
     */
    public function getUseTLS()
    {
        return $this->useTLS;
    }

    /**
     * Sets the domain controllers option.
     *
     * @param array $hosts
     *
     * @throws ConfigurationException
     */
    public function setDomainControllers(array $hosts)
    {
        if (count($hosts) === 0) {
            throw new ConfigurationException('You must specify at least one domain controller.');
        }

        $this->domainControllers = $hosts;
    }

    /**
     * Returns the domain controllers option.
     *
     * @return array
     */
    public function getDomainControllers()
    {
        return $this->domainControllers;
    }

    /**
     * Sets the account prefix option.
     *
     * @param string $suffix
     */
    public function setAccountPrefix($suffix)
    {
        $this->accountPrefix = (string) $suffix;
    }

    /**
     * Sets the account suffix option.
     *
     * @param string $suffix
     */
    public function setAccountSuffix($suffix)
    {
        $this->accountSuffix = (string) $suffix;
    }

    /**
     * Returns the account prefix option.
     *
     * @return string|null
     */
    public function getAccountPrefix()
    {
        return $this->accountPrefix;
    }

    /**
     * Returns the account suffix option.
     *
     * @return string|null
     */
    public function getAccountSuffix()
    {
        return $this->accountSuffix;
    }

    /**
     * Sets the administrators username option.
     *
     * @param string $username
     */
    public function setAdminUsername($username)
    {
        $this->adminUsername = (string) $username;
    }

    /**
     * Returns the administrator username option.
     *
     * @return string|null
     */
    public function getAdminUsername()
    {
        return $this->adminUsername;
    }

    /**
     * Sets the administrators password option.
     *
     * @param string $password
     */
    public function setAdminPassword($password)
    {
        $this->adminPassword = (string) $password;
    }

    /**
     * Returns the administrators password option.
     *
     * @return string|null
     */
    public function getAdminPassword()
    {
        return $this->adminPassword;
    }

    /**
     * Sets the administrators account suffix option.
     *
     * @param $suffix
     */
    public function setAdminAccountSuffix($suffix)
    {
        $this->adminAccountSuffix = (string) $suffix;
    }

    /**
     * Returns the administrators account suffix option.
     *
     * @return string|null
     */
    public function getAdminAccountSuffix()
    {
        return $this->adminAccountSuffix;
    }

    /**
     * Returns the administrators credentials.
     *
     * @return array
     */
    public function getAdminCredentials()
    {
        return [
            $this->adminUsername,
            $this->adminPassword,
            $this->adminAccountSuffix,
        ];
    }

    /**
     * Fills each configuration option with the supplied array.
     *
     * @param array|Traversable $options
     */
    protected function fill($options = [])
    {
        if (!is_array($options) && !$options instanceof Traversable) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s expects an array or Traversable argument; received "%s"',
                    __METHOD__,
                    (is_object($options) ? get_class($options) : gettype($options))
                )
            );
        }

        if (array_key_exists('use_ssl', $options)) {
            if (!array_key_exists('port', $options) && $options['use_ssl'] === true) {
                $options['port'] = ConnectionInterface::PORT_SSL;
            }
        }

        foreach ($options as $key => $value) {
            $method = 'set'.$this->normalizeKey($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * Normalize array key.
     *
     * @param string $key
     *
     * @return string
     */
    protected function normalizeKey($key)
    {
        return str_replace('_', '', strtolower($key));
    }
}
