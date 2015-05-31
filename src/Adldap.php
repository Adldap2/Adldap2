<?php

namespace Adldap;

use Adldap\Exceptions\AdldapException;
use Adldap\Interfaces\ConnectionInterface;
use Adldap\Classes\AdldapSearch;
use Adldap\Classes\AdldapUtils;
use Adldap\Classes\AdldapFolders;
use Adldap\Classes\AdldapExchange;
use Adldap\Classes\AdldapComputers;
use Adldap\Classes\AdldapContacts;
use Adldap\Classes\AdldapUsers;
use Adldap\Classes\AdldapGroups;
use Adldap\Objects\Configuration;
use Adldap\Objects\Ldap\Schema as LdapSchema;
use Adldap\Objects\Schema as AdldapSchema;

/**
 * Class Adldap.
 */
class Adldap
{
    /**
     * Define the different types of account in AD.
     */
    const ADLDAP_NORMAL_ACCOUNT = 805306368;
    const ADLDAP_WORKSTATION_TRUST = 805306369;
    const ADLDAP_INTERDOMAIN_TRUST = 805306370;
    const ADLDAP_SECURITY_GLOBAL_GROUP = 268435456;
    const ADLDAP_DISTRIBUTION_GROUP = 268435457;
    const ADLDAP_SECURITY_LOCAL_GROUP = 536870912;
    const ADLDAP_DISTRIBUTION_LOCAL_GROUP = 536870913;
    const ADLDAP_FOLDER = 'OU';
    const ADLDAP_CONTAINER = 'CN';

    /**
     * The Adldap version number.
     *
     * @var string
     */
    const VERSION = '5.0.0';

    /**
     * The account suffix for your domain, can be set when the class is invoked.
     *
     * @var string
     */
    protected $accountSuffix = '';

    /**
     * The base dn for your domain.
     *
     * If this is set to null then Adldap will attempt to obtain this automatically from the rootDSE
     *
     * @var string
     */
    protected $baseDn = '';

    /**
     * The user login identifier key used in the AD schema.
     *
     * @var string
     */
    protected $userIdKey = 'sAMAccountname';

    /**
     * The attribute (index 0) and value (index 1) used to identify a person in the AD schema.
     *
     * @var array
     */
    protected $personFilter = ['category' => 'objectCategory', 'person' => 'person'];

    /**
     * Port used to talk to the domain controllers.
     *
     * @var string
     */
    protected $adPort = ConnectionInterface::PORT;

    /**
     * Array of domain controllers. Specifiy multiple controllers if you
     * would like the class to balance the LDAP queries amongst multiple servers.
     *
     * @var array
     */
    protected $domainControllers = [];

    /**
     * AD does not return the primary group. http://support.microsoft.com/?kbid=321360
     * This tweak will resolve the real primary group.
     * Setting to false will fudge "Domain Users" and is much faster. Keep in mind though that if
     * someone's primary group is NOT domain users, this is obviously going to mess up the results.
     *
     * @var bool
     */
    protected $realPrimaryGroup = true;

    /**
     * When querying group memberships, do it recursively
     * eg. User Fred is a member of Group A, which is a member of Group B, which is a member of Group C
     * user_ingroup("Fred","C") will returns true with this option turned on, false if turned off.
     *
     * @var bool
     */
    protected $recursiveGroups = true;

    /**
     * When a query returns a referral, follow it.
     *
     * @var int
     */
    protected $followReferrals = 0;

    /**
     * Holds the current ldap connection.
     *
     * @var ConnectionInterface
     */
    protected $ldapConnection;

    /**
     * Optional account with higher privileges for searching
     * This should be set to a domain admin account.
     *
     * @var string
     */
    private $adminUsername = '';

    /**
     * Account with higher privileges password.
     *
     * @var string
     */
    private $adminPassword = '';

    /**
     * Constructor.
     *
     * Tries to bind to the AD domain over LDAP or LDAPs
     *
     * @param array $options     The Adldap configuration options array
     * @param mixed $connection  The connection you'd like to use
     * @param bool  $autoConnect Whether or not you want to connect on construct
     *
     * @throws AdldapException
     */
    public function __construct(array $options = [], $connection = null, $autoConnect = true)
    {
        // Create a new LDAP Connection if one isn't set
        if (!$connection) {
            $connection = new Connections\Ldap();
        }

        $this->setLdapConnection($connection);

        /*
         * If we dev wants to connect automatically, we'll create the
         * configuration object and set the the Adldap properties.
         */
        if ($autoConnect) {
            $configuration = new Configuration($options);

            if ($configuration->countAttributes() > 0) {
                $this->setAccountSuffix($configuration->{'account_suffix'});

                $this->setBaseDn($configuration->{'base_dn'});

                $this->setDomainControllers($configuration->{'domain_controllers'});

                $this->setAdminUsername($configuration->{'admin_username'});

                $this->setAdminPassword($configuration->{'admin_password'});

                $this->setRealPrimaryGroup($configuration->{'real_primarygroup'});

                $this->setUseSSL($configuration->{'use_ssl'});

                $this->setUseTLS($configuration->{'use_tls'});

                $this->setRecursiveGroups($configuration->{'recursive_groups'});

                $this->setFollowReferrals($configuration->{'follow_referrals'});

                if ($configuration->hasAttribute('ad_port')) {
                    $this->setPort($configuration->{'ad_port'});
                }

                if ($configuration->hasAttribute('user_id_key')) {
                    $this->setUserIdKey($configuration->{'user_id_key'});
                }

                if ($configuration->hasAttribute('person_filter')) {
                    $this->setPersonFilter($configuration->{'person_filter'});
                }

                $sso = $configuration->{'sso'};

                /*
                 * If we've set SSO to true, we'll make sure we check
                 * if SSO is supported, and if so we'll bind it to the
                 * current LDAP connection.
                 */
                if ($sso) {
                    if ($this->ldapConnection->isSaslSupported()) {
                        $this->ldapConnection->useSSO();
                    }
                }
            }

            // Looks like we're all set. Let's try and connect
            $this->connect();
        }
    }

    /**
     * Destructor.
     *
     * Closes the current LDAP connection if it exists.
     */
    public function __destruct()
    {
        if ($this->ldapConnection instanceof ConnectionInterface) {
            $this->ldapConnection->close();
        }
    }

    /**
     * Get the active LDAP Connection.
     *
     * @return bool|ConnectionInterface
     */
    public function getLdapConnection()
    {
        if ($this->ldapConnection) {
            return $this->ldapConnection;
        }

        return false;
    }

    /**
     * Sets the ldapConnection property.
     *
     * @param $connection
     */
    public function setLdapConnection(ConnectionInterface $connection)
    {
        $this->ldapConnection = $connection;
    }

    /**
     * Get the bind status of the
     * current connection.
     *
     * @return bool
     */
    public function getLdapBind()
    {
        return $this->ldapConnection->isBound();
    }

    /**
     * Get the current base DN.
     *
     * @return string
     */
    public function getBaseDn()
    {
        /*
         * If the base DN is empty, we'll assume the dev
         * wants it set automatically
         */
        if (empty($this->baseDn)) {
            $this->setBaseDn($this->findBaseDn());
        }

        return $this->baseDn;
    }

    /**
     * Set the current base DN.
     *
     * @param $baseDn
     */
    public function setBaseDn($baseDn)
    {
        $this->baseDn = $baseDn;
    }

    /**
     * Set the account suffix property.
     *
     * @param string $accountSuffix
     */
    public function setAccountSuffix($accountSuffix)
    {
        if ($accountSuffix !== null) {
            $this->accountSuffix = $accountSuffix;
        }
    }

    /**
     * Retrieve the current account suffix.
     *
     * @return string
     */
    public function getAccountSuffix()
    {
        return $this->accountSuffix;
    }

    /**
     * Set the user id key.
     *
     * @param string $userIdKey
     */
    public function setUserIdKey($userIdKey)
    {
        $this->userIdKey = $userIdKey;
    }

    /**
     * Get the user id key.
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
    public function setPersonFilter($personFilter)
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
     * Set the domain controllers property with the
     * specified domainControllers array.
     *
     * @param array $domainControllers
     *
     * @throws AdldapException
     */
    public function setDomainControllers(array $domainControllers = [])
    {
        if (count($domainControllers) === 0) {
            throw new AdldapException('You must specify at least one domain controller.');
        }

        $this->domainControllers = $domainControllers;
    }

    /**
     * Retrieve the array of domain controllers.
     *
     * @return array
     */
    public function getDomainControllers()
    {
        return $this->domainControllers;
    }

    /**
     * Sets the port number your domain controller communicates over.
     *
     * @param int|string $adPort
     *
     * @throws AdldapException
     */
    public function setPort($adPort)
    {
        if (!is_numeric($adPort)) {
            if ($adPort === null) {
                $adPort = 'null';
            }

            throw new AdldapException("The Port: $adPort is not numeric and cannot be used.");
        }

        $this->adPort = (string) $adPort;
    }

    /**
     * Retrieve the current port number.
     *
     * @return int|string
     */
    public function getPort()
    {
        return $this->adPort;
    }

    /**
     * Sets the adminUsername property.
     * Set the username of an account with higher privileges.
     *
     * @param string $adminUsername
     */
    public function setAdminUsername($adminUsername)
    {
        $this->adminUsername = $adminUsername;
    }

    /**
     * Sets the adminPassword property
     * Set the password of an account with higher privileges.
     *
     * @param string $adminPassword
     */
    public function setAdminPassword($adminPassword)
    {
        $this->adminPassword = $adminPassword;
    }

    /**
     * Retrieves the set set administrators username.
     *
     * @return string
     */
    private function getAdminUsername()
    {
        return $this->adminUsername;
    }

    /**
     * Retrieves the set administrators password.
     *
     * @return string
     */
    private function getAdminPassword()
    {
        return $this->adminPassword;
    }

    /**
     * Set the realPrimaryGroup property.
     * Set whether to detect the true primary group.
     *
     * @param bool $realPrimaryGroup
     */
    public function setRealPrimaryGroup($realPrimaryGroup)
    {
        $this->realPrimaryGroup = $realPrimaryGroup;
    }

    /**
     * Retrieve the current real primary group setting.
     *
     * @return bool
     */
    public function getRealPrimaryGroup()
    {
        return $this->realPrimaryGroup;
    }

    /**
     * Set whether to use SSL on the
     * current ldap connection.
     *
     * @param bool $useSSL
     */
    public function setUseSSL($useSSL)
    {
        // Make sure we set the correct SSL port if using SSL
        if ($useSSL) {
            $this->ldapConnection->useSSL();

            $this->setPort(ConnectionInterface::PORT_SSL);
        } else {
            $this->setPort(ConnectionInterface::PORT);
        }
    }

    /**
     * Retrieves the current useSSL property.
     *
     * @return bool
     */
    public function getUseSSL()
    {
        return $this->ldapConnection->isUsingSSL();
    }

    /**
     * Sets the useTLS property
     * Set whether to use TLS.
     *
     * @param bool $useTLS
     */
    public function setUseTLS($useTLS)
    {
        if ($useTLS) {
            $this->ldapConnection->useTLS();
        }
    }

    /**
     * Retrieves the current UseTLS property.
     *
     * @return bool
     */
    public function getUseTLS()
    {
        return $this->ldapConnection->isUsingTLS();
    }

    /**
     * Sets the useSSO property.
     * Set whether to use SSO.
     * Requires ldap_sasl_bind support. Be sure --with-ldap-sasl is used when configuring PHP otherwise this function will be undefined.
     *
     * @param bool $useSSO
     *
     * @throws AdldapException
     */
    public function setUseSSO($useSSO)
    {
        if ($useSSO === true && !$this->ldapConnection->isSaslSupported()) {
            throw new AdldapException('No LDAP SASL support for PHP.  See: http://www.php.net/ldap_sasl_bind');
        }

        $this->ldapConnection->useSSO();
    }

    /**
     * Retrieves the current useSSO property.
     *
     * @return bool
     */
    public function getUseSSO()
    {
        return $this->ldapConnection->isUsingSSO();
    }

    /**
     * Sets the recursiveGroups property.
     * Set whether to lookup recursive groups.
     *
     * @param bool $recursiveGroups
     */
    public function setRecursiveGroups($recursiveGroups)
    {
        if ($recursiveGroups) {
            $this->recursiveGroups = true;
        } else {
            $this->recursiveGroups = false;
        }
    }

    /**
     * Retrieves the current recursiveGroups property.
     *
     * @return bool
     */
    public function getRecursiveGroups()
    {
        return $this->recursiveGroups;
    }

    /**
     * Sets the followReferrals property.
     *
     * @param int $referrals
     */
    public function setFollowReferrals($referrals)
    {
        $this->followReferrals = $referrals;
    }

    /**
     * Returns a new AdldapGroups instance.
     *
     * @return AdldapGroups
     */
    public function group()
    {
        return new AdldapGroups($this);
    }

    /**
     * Returns a new AdldapUsers instance.
     *
     * @return AdldapUsers
     */
    public function user()
    {
        return new AdldapUsers($this);
    }

    /**
     * Returns a new AdldapFolders instance.
     *
     * @return AdldapFolders
     */
    public function folder()
    {
        return new AdldapFolders($this);
    }

    /**
     * Returns a new AdldapUtils instance.
     *
     * @return AdldapUtils
     */
    public function utilities()
    {
        return new AdldapUtils($this);
    }

    /**
     * Returns a new AdldapContacts instance.
     *
     * @return AdldapContacts
     */
    public function contact()
    {
        return new AdldapContacts($this);
    }

    /**
     * Returns a new AdldapExchange instance.
     *
     * @return AdldapExchange
     */
    public function exchange()
    {
        return new AdldapExchange($this);
    }

    /**
     * Returns a new AdldapComputers instance.
     *
     * @return AdldapComputers
     */
    public function computer()
    {
        return new AdldapComputers($this);
    }

    /**
     * Returns a new AdldapSearch instance.
     *
     * @return AdldapSearch
     */
    public function search()
    {
        return new AdldapSearch($this);
    }

    /**
     * Connects and Binds to the Domain Controller.
     *
     * @return bool
     *
     * @throws AdldapException
     */
    public function connect()
    {
        // Select a random domain controller
        $domainController = $this->domainControllers[array_rand($this->domainControllers)];

        // Get the LDAP port
        $port = $this->getPort();

        // Create the LDAP connection
        $this->ldapConnection->connect($domainController, $port);

        // Set the LDAP options
        $this->ldapConnection->setOption(LDAP_OPT_PROTOCOL_VERSION, 3);
        $this->ldapConnection->setOption(LDAP_OPT_REFERRALS, $this->followReferrals);

        // Authenticate to the server
        return $this->authenticate($this->getAdminUsername(), $this->getAdminPassword(), true);
    }

    /**
     * Authenticates a user using the specified credentials.
     *
     * @param string $username      The users AD username
     * @param string $password      The users AD password
     * @param bool   $preventRebind
     *
     * @return bool
     *
     * @throws AdldapException
     */
    public function authenticate($username, $password, $preventRebind = false)
    {
        $auth = false;

        try {
            if ($this->getUseSSO()) {
                // If SSO is enabled, we'll try binding over kerberos
                $remoteUser = $this->getRemoteUserInput();
                $kerberos = $this->getKerberosAuthInput();

                /*
                 * If the remote user input equals the username we're
                 * trying to authenticate, we'll perform the bind
                 */
                if ($remoteUser == $username) {
                    $auth = $this->bindUsingKerberos($kerberos);
                }
            } else {
                // Looks like SSO isn't enabled, we'll bind regularly instead
                $auth = $this->bindUsingCredentials($username, $password);
            }
        } catch (AdldapException $e) {
            if ($preventRebind === true) {
                /*
                 * Binding failed and we're not allowed
                 * to rebind, we'll return false
                 */
                return $auth;
            }
        }

        // If we're allowed to rebind, we'll rebind as administrator
        if ($preventRebind === false) {
            $adminUsername = $this->getAdminUsername();
            $adminPassword = $this->getAdminPassword();

            $this->bindUsingCredentials($adminUsername, $adminPassword);

            if (!$this->ldapConnection->isBound()) {
                throw new AdldapException('Rebind to Active Directory failed. AD said: '.$this->ldapConnection->getLastError());
            }
        }

        return $auth;
    }

    /**
     * Returns objectClass in an array.
     *
     * @param string $distinguishedName The full DN of a contact
     *
     * @return array|bool
     */
    public function getObjectClass($distinguishedName)
    {
        $this->utilities()->validateNotNull('Distinguished Name [dn]', $distinguishedName);

        $result = $this->search()
            ->select('objectClass')
            ->where('distinguishedName', '=', $distinguishedName)
            ->first();

        if (is_array($result) && array_key_exists('objectclass', $result)) {
            return $result['objectclass'];
        }

        return false;
    }

    /**
     * Finds the Base DN of your domain controller.
     *
     * @return string|bool
     */
    public function findBaseDn()
    {
        $namingContext = $this->getRootDse(['defaultnamingcontext']);

        if (is_array($namingContext) && array_key_exists('defaultnamingcontext', $namingContext)) {
            return $namingContext['defaultnamingcontext'];
        }

        return false;
    }

    /**
     * Get the RootDSE properties from a domain controller.
     *
     * @param array $attributes The attributes you wish to query e.g. defaultnamingcontext
     *
     * @return array|bool
     */
    public function getRootDse($attributes = ['*', '+'])
    {
        return $this->search()
            ->setDn(null)
            ->read(true)
            ->select($attributes)
            ->where('objectClass', '*')
            ->first();
    }

    /**
     * Get last error from Active Directory.
     *
     * This function gets the last message from Active Directory
     * This may indeed be a 'Success' message but if you get an unknown error
     * it might be worth calling this function to see what errors were raised
     *
     * @return string
     */
    public function getLastError()
    {
        if ($this->ldapConnection instanceof ConnectionInterface) {
            return $this->ldapConnection->getLastError();
        }

        return false;
    }

    /**
     * Returns an LDAP compatible schema array for modifications.
     *
     * @param array $attributes Attributes to be queried
     *
     * @return array|bool
     */
    public function ldapSchema(array $attributes)
    {
        // Check every attribute to see if it contains 8bit characters and then UTF8 encode them
        array_walk($attributes, [$this->utilities(), 'encode8bit']);

        $adldapSchema = new AdldapSchema($attributes);

        $ldapSchema = new LdapSchema($adldapSchema);

        if ($ldapSchema->countAttributes() === 0) {
            return false;
        }

        // Return a filtered array to remove NULL attributes
        return array_filter($ldapSchema->getAttributes(), function ($attribute) {
            // Only return the attribute if it is not null
            if ($attribute[0] !== null) {
                return $attribute;
            }
        });
    }

    /**
     * Returns the filtered REMOTE_USER server variable.
     *
     * @return mixed
     */
    public function getRemoteUserInput()
    {
        return filter_input(INPUT_SERVER, 'REMOTE_USER');
    }

    /**
     * Returns the filtered KRB5CCNAME server variable.
     *
     * @return mixed
     */
    public function getKerberosAuthInput()
    {
        return filter_input(INPUT_SERVER, 'KRB5CCNAME');
    }

    /**
     * Binds to the current connection using kerberos.
     *
     * @param $kerberosCredentials
     * @returns bool
     *
     * @throws AdldapException
     */
    private function bindUsingKerberos($kerberosCredentials)
    {
        putenv('KRB5CCNAME='.$kerberosCredentials);

        $bound = $this->ldapConnection->bind(null, null, true);

        if (!$bound) {
            $message = 'Bind to Active Directory failed. AD said: '.$this->ldapConnection->getLastError();

            throw new AdldapException($message);
        }

        return true;
    }

    /**
     * Binds to the current connection using the
     * inserted credentials.
     *
     * @param string $username
     * @param string $password
     * @returns bool
     *
     * @throws AdldapException
     */
    private function bindUsingCredentials($username, $password)
    {
        // Allow binding with null credentials
        if (empty($username)) {
            $username = null;
        } else {
            $username .= $this->getAccountSuffix();
        }

        if (empty($password)) {
            $password = null;
        }

        $this->ldapConnection->bind($username, $password);

        if (!$this->ldapConnection->isBound()) {
            $error = $this->ldapConnection->getLastError();

            if ($this->ldapConnection->isUsingSSL() && !$this->ldapConnection->isUsingTLS()) {
                $message = 'Bind to Active Directory failed. Either the LDAPs connection failed or the login credentials are incorrect. AD said: '.$error;
            } else {
                $message = 'Bind to Active Directory failed. Check the login credentials and/or server details. AD said: '.$error;
            }

            throw new AdldapException($message);
        }

        return true;
    }
}
