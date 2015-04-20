<?php

namespace Adldap;

use Adldap\Classes\AdldapSearch;
use Adldap\Exceptions\AdldapException;
use Adldap\Interfaces\ConnectionInterface;
use Adldap\Classes\AdldapUtils;
use Adldap\Classes\AdldapFolders;
use Adldap\Classes\AdldapExchange;
use Adldap\Classes\AdldapComputers;
use Adldap\Classes\AdldapContacts;
use Adldap\Classes\AdldapUsers;
use Adldap\Classes\AdldapGroups;
use Adldap\Objects\Configuration;
use Adldap\Objects\LdapEntry;
use Adldap\Objects\LdapSchema;
use Adldap\Objects\Schema;

/**
 * Class Adldap
 * @package Adldap
 */
class Adldap
{
    /**
     * Define the different types of account in AD
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
     * The default port for LDAP non-SSL connections
     */
    const ADLDAP_LDAP_PORT = '389';

    /**
     * The default port for LDAPS SSL connections
     */
    const ADLDAP_LDAPS_PORT = '636';

    /**
     * The default query fields to use when
     * performing a global search.
     *
     * @var array
     */
    public $defaultQueryFields = array(
        'cn',
        'description',
        'displayname',
        'distinguishedname',
        'samaccountname',
    );

    /**
     * The account suffix for your domain, can be set when the class is invoked
     *
     * @var string
     */
    protected $accountSuffix = "@mydomain.local";

    /**
     * The base dn for your domain
     *
     * If this is set to null then Adldap will attempt to obtain this automatically from the rootDSE
     *
     * @var string
     */
    protected $baseDn = "DC=mydomain,DC=local";

    /**
     * The user login identifier key used in the AD schema
     * 
     * @var string
     */
    protected $userIdKey = "sAMAccountname";

    /**
     * The attribute (index 0) and value (index 1) used to identify a person in the AD schema
     * 
     * @var array
     */
    protected $personFilter = array("category" => "objectCategory", "person" => "person");

    /**
     * Port used to talk to the domain controllers.
     *
     * @var string
     */
    protected $adPort = self::ADLDAP_LDAP_PORT;

    /**
     * Array of domain controllers. Specifiy multiple controllers if you
     * would like the class to balance the LDAP queries amongst multiple servers
     *
     * @var array
     */
    protected $domainControllers = array("dc01.mydomain.local");

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
     * user_ingroup("Fred","C") will returns true with this option turned on, false if turned off
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
     * Holds the current ldap connection
     *
     * @var ConnectionInterface
     */
    protected $ldapConnection;

    /**
     * The group class
     *
     * @var AdldapGroups
     */
    protected $groupClass;

    /**
     * The user class
     *
     * @var AdldapUsers
     */
    protected $userClass;

    /**
     * The folders class
     *
     * @var AdldapFolders
     */
    protected $folderClass;

    /**
     * The utils class
     *
     * @var AdldapUtils
     */
    protected $utilClass;

    /**
     * The contacts class
     *
     * @var AdldapContacts
     */
    protected $contactClass;

    /**
     * The exchange class
     *
     * @var AdldapExchange
     */
    protected $exchangeClass;

    /**
     * The computers class
     *
     * @var AdldapComputers
     */
    protected $computerClass;

    /**
     * Optional account with higher privileges for searching
     * This should be set to a domain admin account
     *
     * @var string
     */
    private $adminUsername = '';

    /**
     * Account with higher privileges password
     *
     * @var string
     */
    private $adminPassword = '';

    /**
     * Constructor.
     *
     * Tries to bind to the AD domain over LDAP or LDAPs
     *
     * @param array $options The Adldap configuration options array
     * @param mixed $connection The connection you'd like to use
     * @param bool $autoConnect Whether or not you want to connect on construct
     * @throws AdldapException
     */
    public function __construct(array $options = array(), $connection = NULL, $autoConnect = true)
    {
        // Create a new LDAP Connection if one isn't set
        if( ! $connection) $connection = new Connections\Ldap;

        $this->setLdapConnection($connection);

        // If we dev wants to connect automatically, we'll construct the
        if($autoConnect)
        {
            $configuration = new Configuration($options);

            if ($configuration->countAttributes() > 0)
            {
                $this->setAccountSuffix($configuration->{'account_suffix'});

                $this->setBaseDn($configuration->{'base_dn'});

                $this->setDomainControllers($configuration->{"domain_controllers"});

                $this->setAdminUsername($configuration->{'admin_username'});

                $this->setAdminPassword($configuration->{'admin_password'});

                $this->setRealPrimaryGroup($configuration->{'real_primarygroup'});

                $this->setUseSSL($configuration->{'use_ssl'});

                $this->setUseTLS($configuration->{'use_tls'});

                $this->setRecursiveGroups($configuration->{'recursive_groups'});

                $this->setFollowReferrals($configuration->{'follow_referrals'});

                if($configuration->hasAttribute('ad_port'))
                {
                    $this->setPort($configuration->{'ad_port'});
                }

                if($configuration->hasAttribute('user_id_key'))
                {
                    $this->setUserIdKey($configuration->{'user_id_key'});
                }

                if($configuration->hasAttribute('person_filter'))
                {
                    $this->setPersonFilter($configuration->{'person_filter'});
                }

                $sso = $configuration->{'sso'};

                /*
                 * If we've set SSO to true, we'll make sure we check
                 * if SSO is supported, if so we'll bind it to the
                 * current LDAP connection.
                 */
                if ($sso)
                {
                    if ($this->ldapConnection->isSaslSupported()) $this->ldapConnection->useSSO();
                }
            }

            // Looks like we're all set. Let's try and connect
            $this->connect();
        }
    }

    /**
     * Destructor.
     *
     * Closes the current LDAP connection.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Get the active LDAP Connection
     *
     * @return bool|mixed
     */
    public function getLdapConnection()
    {
        if ($this->ldapConnection) return $this->ldapConnection;

        return false;
    }

    /**
     * Sets the ldapConnection property
     *
     * @param $connection
     * @return void
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
     * Get the current base DN
     *
     * @return string
     */
    public function getBaseDn()
    {
        return $this->baseDn;
    }

    /**
     * Set the current base DN
     *
     * @param $baseDn
     * @return void
     */
    public function setBaseDn($baseDn)
    {
        if ($baseDn !== NULL) $this->baseDn = $baseDn;
    }

    /**
     * Set the account suffix property
     *
     * @param string $accountSuffix
     * @return void
     */
    public function setAccountSuffix($accountSuffix)
    {
        if ($accountSuffix !== NULL) $this->accountSuffix = $accountSuffix;
    }

    /**
     * Retrieve the current account suffix
     *
     * @return string
     */
    public function getAccountSuffix()
    {
        return $this->accountSuffix;
    }

    /**
    * Set the user id key
    * 
    * @param string $userIdKey
    * @return void
    */
    public function setUserIdKey($userIdKey)
    {
          $this->userIdKey = $userIdKey;

    }

    /**
    * Get the user id key
    * 
    * @return string
    */
    public function getUserIdKey()
    {
          return $this->userIdKey;
    }

    /**
     * Sets the person search filter
     * 
     * @param array $personKey
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
     * specified domainControllers array
     *
     * @param array $domainControllers
     * @return void
     * @throws AdldapException
     */
    public function setDomainControllers(array $domainControllers = array())
    {
        if(count($domainControllers) === 0) throw new AdldapException("You must specify at least one domain controller.");

        $this->domainControllers = $domainControllers;
    }

    /**
     * Retrieve the array of domain controllers
     *
     * @return array
     */
    public function getDomainControllers()
    {
        return $this->domainControllers;
    }

    /**
     * Sets the port number your domain controller communicates over
     *
     * @param int|string $adPort
     * @throws AdldapException
     */
    public function setPort($adPort)
    {
        if( ! is_numeric($adPort))
        {
            if($adPort === NULL) $adPort = 'null';

            throw new AdldapException("The Port: $adPort is not numeric and cannot be used.");
        }

        $this->adPort = (string) $adPort;
    }

    /**
     * Retrieve the current port number
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
     * @return void
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
     * @return void
     */
    public function setAdminPassword($adminPassword)
    {
        $this->adminPassword = $adminPassword;
    }

    /**
     * Retrieves the set set administrators username
     *
     * @return string
     */
    private function getAdminUsername()
    {
        return $this->adminUsername;
    }

    /**
     * Retrieves the set administrators password
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
     * @return void
     */
    public function setRealPrimaryGroup($realPrimaryGroup)
    {
        $this->realPrimaryGroup = $realPrimaryGroup;
    }

    /**
     * Retrieve the current real primary group setting
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
     * @return void
     */
    public function setUseSSL($useSSL)
    {
        // Make sure we set the correct SSL port if using SSL
        if($useSSL)
        {
            $this->ldapConnection->useSSL();

            $this->setPort(self::ADLDAP_LDAPS_PORT);
        }
        else
        {
            $this->setPort(self::ADLDAP_LDAP_PORT);
        }
    }

    /**
    * Retrieves the current useSSL property
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
     * @return void
     */
    public function setUseTLS($useTLS)
    {
        if($useTLS) $this->ldapConnection->useTLS();
    }

    /**
     * Retrieves the current UseTLS property
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
     * @throws AdldapException
     */
    public function setUseSSO($useSSO)
    {
        if ($useSSO === true && ! $this->ldapConnection->isSaslSupported())
        {
            throw new AdldapException('No LDAP SASL support for PHP.  See: http://www.php.net/ldap_sasl_bind');
        }

        $this->ldapConnection->useSSO();
    }

    /**
     * Retrieves the current useSSO property
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
     * @return void
     */
    public function setRecursiveGroups($recursiveGroups)
    {
        if($recursiveGroups)
        {
            $this->recursiveGroups = true;
        } else
        {
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
     * Retrieve the group class with the current LDAP connection.
     * This will set the groupClass property with a new group class
     * instance if it has not been set.
     *
     * @return AdldapGroups
     */
    public function group()
    {
        if ( ! $this->groupClass)
        {
            $this->groupClass = new AdldapGroups($this);
        }

        return $this->groupClass;
    }

    /**
     * Retrieve the current user class with the current LDAP connection.
     * This will set the userClass property with a new user class instance
     * if it has not been set.
     *
     * @return AdldapUsers
     */
    public function user()
    {
        if ( ! $this->userClass)
        {
            $this->userClass = new AdldapUsers($this);
        }

        return $this->userClass;
    }

    /**
     * Retrieve the current folder class with the current LDAP connection.
     * This will set the folderClass property with a new folder class instance
     * if it has not been set.
     *
     * @return AdldapFolders
     */
    public function folder()
    {
        if ( ! $this->folderClass)
        {
            $this->folderClass = new AdldapFolders($this);
        }

        return $this->folderClass;
    }

    /**
     * Retrieves the current utility class with the current LDAP connection.
     * This will set the utilClass property with a new utility class instance
     * if it has not been set.
     *
     * @return AdldapUtils
     */
    public function utilities()
    {
        if ( ! $this->utilClass)
        {
            $this->utilClass = new AdldapUtils($this);
        }

        return $this->utilClass;
    }

    /**
     * Retrieves the current contact class with the current LDAP connection.
     * This will set the contactClass property with a new contacts class instance
     * if it has not been set.
     *
     * @return AdldapContacts
     */
    public function contact()
    {
        if ( ! $this->contactClass)
        {
            $this->contactClass = new AdldapContacts($this);
        }

        return $this->contactClass;
    }

    /**
     * Get the exchange class interface
     *
     * @return AdldapExchange
     */
    public function exchange()
    {
        if ( ! $this->exchangeClass)
        {
            $this->exchangeClass = new AdldapExchange($this);
        }

        return $this->exchangeClass;
    }

    /**
     * Get the computers class interface
     *
     * @return AdldapComputers
     */
    public function computer()
    {
        if ( ! $this->computerClass)
        {
            $this->computerClass = new AdldapComputers($this);
        }

        return $this->computerClass;
    }

    /**
     * Returns a new Adldap Search object.
     *
     * @return AdldapSearch
     */
    public function search()
    {
        return new AdldapSearch($this);
    }

    /**
     * Connects and Binds to the Domain Controller
     *
     * @return bool
     * @throws AdldapException
     */
    public function connect()
    {
        // Connect to the AD/LDAP server as the username/password
        $domainController = $this->randomController();

        $adminUsername = $this->getAdminUsername();
        $adminPassword = $this->getAdminPassword();

        $useSSL = $this->getUseSSL();
        $useTLS = $this->getUseTLS();
        $useSSO = $this->getUseSSO();

        $port = $this->getPort();

        if ($useSSL) $this->ldapConnection->useSSL();

        $this->ldapConnection->connect($domainController, $port);

        $this->ldapConnection->setOption(LDAP_OPT_PROTOCOL_VERSION, 3);
        $this->ldapConnection->setOption(LDAP_OPT_REFERRALS, $this->followReferrals);

        if ($useTLS) $this->ldapConnection->startTLS();

        // Bind as a domain admin if they've set it up
        if ($adminUsername !== NULL && $adminPassword !== NULL)
        {
            $this->bindUsingCredentials($adminUsername, $adminPassword);
        }

        $remoteUser = $this->getRemoteUserInput();
        $kerberosAuth = $this->getKerberosAuthInput();

        if ($useSSO && $remoteUser && ! $adminUsername && $kerberosAuth)
        {
            return $this->bindUsingKerberos($kerberosAuth);
        }

        if ( ! $this->getBaseDn()) $this->setBaseDn($this->findBaseDn());

        return true;
    }

    /**
     * Closes the LDAP connection if a current connection exists.
     *
     * @return bool
     */
    public function close()
    {
        if($this->ldapConnection) $this->ldapConnection->close();
    }

    /**
     * Authenticates a user using the specified credentials
     *
     * @param string $username The users AD username
     * @param string $password The users AD password
     * @param bool $preventRebind
     * @return bool
     * @throws AdldapException
     */
    public function authenticate($username, $password, $preventRebind = false)
    {
        $this->utilities()->validateNotNullOrEmpty('Username', $username);
        $this->utilities()->validateNotNullOrEmpty('Password', $password);

        $remoteUser = $this->getRemoteUserInput();
        $kerberos = $this->getKerberosAuthInput();

        // Allow binding over SSO for Kerberos
        if ($this->getUseSSO() && $remoteUser && $remoteUser == $username && $this->getAdminUsername() === NULL && $kerberos)
        {
            return $this->bindUsingKerberos($kerberos);
        }

        // Bind as the user
        $ret = true;

        try {
            $bound = $this->bindUsingCredentials($username, $password);
        }
        catch (AdldapException $e) {
            $ret = false;
        }

        if($preventRebind)
        {
            return $ret;
        } else
        {
            $adminUsername = $this->getAdminUsername();
            $adminPassword = $this->getAdminPassword();

            if($adminUsername !== NULL && $adminPassword !== NULL)
            {
                $bound = $this->bindUsingCredentials($adminUsername, $adminPassword);

                if ( ! $bound)
                {
                    // This should never happen in theory
                    throw new AdldapException('Rebind to Active Directory failed. AD said: ' . $this->ldapConnection->getLastError());
                }
            }
        }

        return $ret;
    }

    /**
     * Returns objectClass in an array
     *
     * @param string $distinguishedName The full DN of a contact
     * @return array|bool
     */
    public function getObjectClass($distinguishedName)
    {
        $this->utilities()->validateNotNull('Distinguished Name [dn]', $distinguishedName);

        $this->utilities()->validateLdapIsBound();

        $filter = "distinguishedName=" . $this->utilities()->ldapSlashes($distinguishedName);

        $results = $this->ldapConnection->search($this->getBaseDn(), $filter, array("objectclass"));

        $entries = $this->ldapConnection->getEntries($results);

        $objects = array();

        for ($i = 0; $i < $entries[0]["objectclass"]["count"]; $i++)
        {
            array_push($objects, $entries[0]["objectclass"][$i]);
        }

        return $objects;
    }

    /**
     * Find the Base DN of your domain controller
     *
     * @return mixed
     */
    public function findBaseDn()
    {
        $namingContext = $this->getRootDse(array('defaultnamingcontext'));

        return $namingContext[0]['defaultnamingcontext'][0];
    }

    /**
     * Get the RootDSE properties from a domain controller
     *
     * @param array $attributes The attributes you wish to query e.g. defaultnamingcontext
     * @return array|bool
     */
    public function getRootDse($attributes = array("*", "+"))
    {
        $this->utilities()->validateLdapIsBound();

        $filter = 'objectClass=*';

        $results = $this->ldapConnection->read(NULL, $filter, $attributes);

        return $this->ldapConnection->getEntries($results);
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
        if($this->ldapConnection) return $this->ldapConnection->getLastError();
    }

    /**
     * Returns an LDAP compatible schema array for modifications.
     *
     * @param array $attributes Attributes to be queried
     * @return array|bool
     * @depreciated Depreciated as of 5.0 in favor of ldapSchema function
     */
    public function adldap_schema(array $attributes)
    {
        return $this->ldapSchema($attributes);
    }

    /**
     * Returns an LDAP compatible schema array for modifications.
     *
     * @param array $attributes Attributes to be queried
     * @return array|bool
     */
    public function ldapSchema(array $attributes)
    {
        // Check every attribute to see if it contains 8bit characters and then UTF8 encode them
        array_walk($attributes, array($this->utilities(), 'encode8bit'));

        $schema = new Schema($attributes);

        $ldapSchema = new LdapSchema($schema);

        if ($ldapSchema->countAttributes() === 0) return false;

        // Return a filtered array to remove NULL attributes
        return array_filter($ldapSchema->getAttributes(), function($attribute)
        {
            // Only return the attribute if it is not null
            if ($attribute[0] !== null) return $attribute;
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
     * Select a random domain controller from your domain controller array.
     *
     * @return string
     */
    protected function randomController()
    {
        return $this->domainControllers[array_rand($this->domainControllers)];
    }

    /**
     * Binds to the current connection using kerberos
     *
     * @param $kerberosCredentials
     * @returns bool
     * @throws AdldapException
     */
    private function bindUsingKerberos($kerberosCredentials)
    {
        putenv("KRB5CCNAME=" . $kerberosCredentials);

        $bound = $this->ldapConnection->bind(NULL, NULL, true);

        if ( ! $bound)
        {
            $message = 'Rebind to Active Directory failed. AD said: ' . $this->ldapConnection->getLastError();

            throw new AdldapException($message);
        }

        return true;
    }

    /**
     * Binds to the current connection using administrator credentials
     *
     * @param string $username
     * @param string $password
     * @returns bool
     * @throws AdldapException
     */
    private function bindUsingCredentials($username, $password)
    {
        // Allow binding with null credentials
        if(empty($username))
        {
            $username = NULL;
        } else
        {
            $username .= $this->getAccountSuffix();
        }

        if(empty($password)) $password = NULL;

        $bindings = $this->ldapConnection->bind($username, $password);

        if ( ! $bindings)
        {
            $error = $this->ldapConnection->getLastError();

            if ($this->ldapConnection->isUsingSSL() && ! $this->ldapConnection->isUsingTLS())
            {
                // If you have problems troubleshooting, remove the @ character from the ldapBind command above to get the actual error message
                $message = 'Bind to Active Directory failed. Either the LDAPs connection failed or the login credentials are incorrect. AD said: ' . $error;
            }
            else
            {
                $message = 'Bind to Active Directory failed. Check the login credentials and/or server details. AD said: ' . $error;
            }

            throw new AdldapException($message);
        }

        return true;
    }
}
