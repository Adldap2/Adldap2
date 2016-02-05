<?php

namespace Adldap\Connections;

use Adldap\Exceptions\ConfigurationException;
use Adldap\Exceptions\InvalidArgumentException;
use Adldap\Objects\DistinguishedName;
use Adldap\Schemas\ActiveDirectory;
use Traversable;

class Configuration
{
    /**
     * The LDAP base dn.
     *
     * @var string
     */
    protected $baseDn;

    /**
     * The integer to instruct the LDAP connection
     * whether or not to follow referrals.
     *
     * https://msdn.microsoft.com/en-us/library/ms677913(v=vs.85).aspx
     *
     * @var bool
     */
    protected $followReferrals = false;

    /**
     * The user ID key to use for performing searches upon.
     *
     * @var string
     */
    protected $userIdKey = ActiveDirectory::ACCOUNT_NAME;

    /**
     * The attribute (index 0) and value (index 1) used to identify a person in the AD schema.
     *
     * @var array
     */
    protected $personFilter = [
        'category' => ActiveDirectory::OBJECT_CATEGORY,
        'person'   => ActiveDirectory::PERSON,
    ];

    /**
     * The LDAP port to use when connecting to
     * the domain controllers.
     *
     * @var string
     */
    protected $port = ConnectionInterface::PORT;

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
     * Determines whether or not to use SSO
     * with the current LDAP connection.
     *
     * @var bool
     */
    protected $useSSO = false;

    /**
     * The domain controllers to connect to.
     *
     * @var array
     */
    protected $domainControllers = [];

    /**
     * The domain controller selected to connect to.
     *
     * @var string
     */
    protected $domainControllerSelected;

    /**
     * The LDAP account suffix.
     *
     * @var string
     */
    protected $accountSuffix;

    /**
     * The LDAP admin account suffix.
     *
     * @var string
     */
    protected $adminAccountSuffix;

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
     * Constructor.
     *
     * @param array|Traversable $options
     *
     * @throws InvalidArgumentException
     */
    public function __construct($options = [])
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
     * Sets the base DN property.
     *
     * @param string $dn
     */
    public function setBaseDn($dn)
    {
        // We'll construct a new Distinguished name with
        // the DN we're given so we know it's valid.
        if (!$dn instanceof DistinguishedName) {
            $dn = new DistinguishedName($dn);
        }

        $this->baseDn = $dn->get();
    }

    /**
     * Returns the Base DN string.
     *
     * @return string
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
     * Sets the user ID key option.
     *
     * @param $key
     */
    public function setUserIdKey($key)
    {
        $this->userIdKey = $key;
    }

    /**
     * Returns the user ID key option.
     *
     * @return string
     */
    public function getUserIdKey()
    {
        return $this->userIdKey;
    }

    /**
     * Sets the person search filter.
     *
     * @param array $personFilter
     */
    public function setPersonFilter(array $personFilter)
    {
        $this->personFilter = $personFilter;
    }

    /**
     * Get the person search filter.
     * An optional parameter may be used to specify the desired part.
     * Without a parameter, returns an imploded string of the form "category=person".
     *
     * @param string $key
     *
     * @return string
     */
    public function getPersonFilter($key = null)
    {
        if ($key == 'category') {
            return $this->personFilter['category'];
        }

        if ($key == 'person') {
            return $this->personFilter['person'];
        }

        return implode('=', $this->personFilter);
    }

    /**
     * Sets the port option to use when connecting.
     *
     * @param $port
     */
    public function setPort($port)
    {
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
     * Sets the option whether or not to use SSL when connecting.
     *
     * @param $bool
     *
     * @throws ConfigurationException
     */
    public function setUseSSL($bool)
    {
        $bool = (bool) $bool;

        if ($this->useTLS && $bool === true) {
            $message = 'You can only specify the use of one security protocol. Use TLS is true.';

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
     * @param $bool
     *
     * @throws ConfigurationException
     */
    public function setUseTLS($bool)
    {
        $bool = (bool) $bool;

        if ($this->useSSL && $bool === true) {
            $message = 'You can only specify the use of one security protocol. Use SSL is true.';

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
     * Sets the option whether or not to use SSO when connecting.
     *
     * @param $bool
     */
    public function setUseSSO($bool)
    {
        $this->useSSO = (bool) $bool;
    }

    /**
     * Returns the use SSO option.
     *
     * @return bool
     */
    public function getUseSSO()
    {
        return $this->useSSO;
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
            $message = 'You must specify at least one domain controller.';

            throw new ConfigurationException($message);
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
     * Sets the account suffix option.
     *
     * @param string $suffix
     */
    public function setAccountSuffix($suffix)
    {
        $this->accountSuffix = (string) $suffix;
    }

    /**
     * Returns the account suffix option.
     *
     * @return string
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
     * @return string
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
     * @return string
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
     * Sets the domain controller selected random.
     *
     * @param string $domainController
     */
    public function setDomainControllerSelected($domainController)
    {
        $this->domainControllerSelected = (string) $domainController;
    }

    /**
     * Returns the domain controller selected to etablish the connection.
     *
     * @return string
     */
    public function getDomainControllerSelected()
    {
        return $this->domainControllerSelected;
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
        $key = str_replace('_', '', strtolower($key));

        return $key;
    }
}
