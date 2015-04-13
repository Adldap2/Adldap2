<?php

namespace adLDAP;

use adLDAP\Exceptions\adLDAPException;
use adLDAP\Interfaces\ConnectionInterface;
use adLDAP\Objects\Configuration;
use adLDAP\Objects\LdapSchema;
use adLDAP\Objects\Schema;

/**
 * PHP LDAP CLASS FOR MANIPULATING ACTIVE DIRECTORY
 * Version 5.0.0
 *
 * PHP Version 5 with SSL and LDAP support
 *
 * Written by Scott Barnett, Richard Hyland
 *   email: scott@wiggumworld.com, adldap@richardhyland.com
 *   http://github.com/adldap/adLDAP
 *
 * Copyright (c) 2006-2014 Scott Barnett, Richard Hyland
 *
 * We'd appreciate any improvements or additions to be submitted back
 * to benefit the entire community :)
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * Main adLDAP class
 *
 * Can be initialised using $adldap = new adLDAP();
 *
 * Something to keep in mind is that Active Directory is a permissions
 * based directory. If you bind as a domain user, you can't fetch as
 * much information on other users as you could as a domain admin.
 *
 * Before asking questions, please read the Documentation at
 * http://adldap.sourceforge.net/wiki/doku.php?id=api
 *
 * Class adLDAP
 * @package adLDAP
 * @author Scott Barnett, Richard Hyland
 * @copyright (c) 2006-2014 Scott Barnett, Richard Hyland
 */
class adLDAP
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
     * The account suffix for your domain, can be set when the class is invoked
     *
     * @var string
     */
    protected $accountSuffix = "@mydomain.local";

    /**
     * The base dn for your domain
     *
     * If this is set to null then adLDAP will attempt to obtain this automatically from the rootDSE
     *
     * @var string
     */
    protected $baseDn = "DC=mydomain,DC=local"; 

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
     * someone's primary group is NOT domain users, this is obviously going to mess up the results
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
     * @var \adLDAP\classes\adLDAPGroups
     */
    protected $groupClass;

    /**
     * The user class
     *
     * @var \adLDAP\classes\adLDAPUsers
     */
    protected $userClass;

    /**
     * The folders class
     *
     * @var \adLDAP\classes\adLDAPFolders
     */
    protected $folderClass;

    /**
     * The utils class
     *
     * @var \adLDAP\classes\adLDAPUtils
     */
    protected $utilClass;

    /**
     * The contacts class
     *
     * @var \adLDAP\classes\adLDAPContacts
     */
    protected $contactClass;

    /**
     * The exchange class
     *
     * @var \adLDAP\classes\adLDAPExchange
     */
    protected $exchangeClass;

    /**
     * The computers class
     *
     * @var \adLDAP\classes\adLDAPComputers
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
     * @param array $options The adLDAP configuration options array
     * @param mixed $connection The connection you'd like to use
     * @throws adLDAPException
     */
    public function __construct(array $options = array(), $connection = NULL)
    {
        // Create a new LDAP Connection if one isn't set
        if( ! $connection) $connection = new Connections\LDAP;

        $this->setLdapConnection($connection);

        // Check if LDAP is supported
        if ($this->ldapConnection->isSupported() === false)
        {
            throw new adLDAPException('No LDAP support for PHP.  See: http://www.php.net/ldap');
        }

        $configuration = new Configuration($options);

        // You can specifically overide any of the default configuration options setup above
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

            $this->setPort($configuration->{'ad_port'});

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
     * Retrieve the group class with the current LDAP connection.
     * This will set the groupClass property with a new group class
     * instance if it has not been set.
     *
     * @return \adLDAP\classes\adLDAPGroups
     */
    public function group()
    {
        if ( ! $this->groupClass)
        {
            $this->groupClass = new \adLDAP\classes\adLDAPGroups($this);
        }

        return $this->groupClass;
    }

    /**
     * Retrieve the current user class with the current LDAP connection.
     * This will set the userClass property with a new user class instance
     * if it has not been set.
     *
     * @return \adLDAP\classes\adLDAPUsers
     */
    public function user()
    {
        if ( ! $this->userClass)
        {
            $this->userClass = new \adLDAP\classes\adLDAPUsers($this);
        }

        return $this->userClass;
    }

    /**
     * Retrieve the current folder class with the current LDAP connection.
     * This will set the folderClass property with a new folder class instance
     * if it has not been set.
     *
     * @return \adLDAP\classes\adLDAPFolders
     */
    public function folder()
    {
        if ( ! $this->folderClass)
        {
            $this->folderClass = new \adLDAP\classes\adLDAPFolders($this);
        }

        return $this->folderClass;
    }

    /**
     * Retrieves the current utility class with the current LDAP connection.
     * This will set the utilClass property with a new utility class instance
     * if it has not been set.
     *
     * @return \adLDAP\classes\adLDAPUtils
     */
    public function utilities()
    {
        if ( ! $this->utilClass)
        {
            $this->utilClass = new \adLDAP\classes\adLDAPUtils($this);
        }

        return $this->utilClass;
    }

    /**
     * Retrieves the current contact class with the current LDAP connection.
     * This will set the contactClass property with a new contacts class instance
     * if it has not been set.
     *
     * @return \adLDAP\classes\adLDAPContacts
     */
    public function contact()
    {
        if ( ! $this->contactClass)
        {
            $this->contactClass = new \adLDAP\classes\adLDAPContacts($this);
        }

        return $this->contactClass;
    }

    /**
     * Get the exchange class interface
     *
     * @return \adLDAP\classes\adLDAPExchange
     */
    public function exchange()
    {
        if ( ! $this->exchangeClass)
        {
            $this->exchangeClass = new \adLDAP\classes\adLDAPExchange($this);
        }

        return $this->exchangeClass;
    }

    /**
     * Get the computers class interface
     *
     * @return \adLDAP\classes\adLDAPComputers
     */
    public function computer()
    {
        if ( ! $this->computerClass)
        {
            $this->computerClass = new \adLDAP\classes\adLDAPComputers($this);
        }

        return $this->computerClass;
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
     * Set the domain controllers property with the
     * specified domainControllers array
     *
     * @param array $domainControllers
     * @return void
     * @throws adLDAPException
     */
    public function setDomainControllers(array $domainControllers = array())
    {
        if(count($domainControllers) === 0) throw new adLDAPException("You must specify at least one domain controller.");

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
     * @throws adLDAPException
     */
    public function setPort($adPort)
    {
        if( ! is_numeric($adPort)) throw new adLDAPException("The Port: $adPort is not numeric and cannot be used.");

        $this->adPort = $adPort;
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
     * @throws adLDAPException
     */
    public function setUseSSO($useSSO)
    {
        if ($useSSO === true && ! $this->ldapConnection->isSaslSupported())
        {
            throw new adLDAPException('No LDAP SASL support for PHP.  See: http://www.php.net/ldap_sasl_bind');
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
     * Connects and Binds to the Domain Controller
     *
     * @return bool
     * @throws adLDAPException
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
     * @throws adLDAPException
     */
    public function authenticate($username, $password, $preventRebind = false)
    {
        $remoteUser = $this->getRemoteUserInput();
        $kerberos = $this->getKerberosAuthInput();

        // Allow binding over SSO for Kerberos
        if ($this->getUseSSO() && $remoteUser && $remoteUser == $username && $this->getAdminUsername() === NULL && $kerberos)
        {
            return $this->bindUsingKerberos($kerberos);
        }
        
        // Bind as the user
        $ret = true;

        $bound = $this->bindUsingCredentials($username, $password);

        if ( ! $bound) $ret = false;

        if($preventRebind)
        {
            return $ret;
        } else
        {
            $adminUsername = $this->getAdminUsername();
            $adminPassword = $this->getAdminPassword();

            if($adminUsername && $adminPassword)
            {

                if ( ! $bound)
                {
                    // This should never happen in theory
                    throw new adLDAPException('Rebind to Active Directory failed. AD said: ' . $this->ldapConnection->getLastError());
                }
            }
        }

        return $ret;
    }

    /**
     * Return a list of all found objects (except computer) in AD
     * $search has to match either cn, displayname, samaccountname or sn
     *
     * @param bool $includeDescription Return a description,cn, displayname and distinguishedname of the user
     * @param string $search Search parameter
     * @param bool $sorted Sort the user accounts
     * @return array|bool
     */
    public function search($includeDescription = false, $search = "*", $sorted = true)
    {
        if ( ! $this->getLdapBind()) return false;

        // Perform the search and grab all their details
        $filter = "(&(!(objectClass=computer))(|(anr=" . $search . ")))";

        $fields = array("cn","description","displayname","distinguishedname","samaccountname");

        $results = $this->ldapConnection->search($this->getBaseDn(), $filter, $fields);

        $entries = $this->ldapConnection->getEntries($results);

        $objectArray = array();

        for ($i = 0; $i < $entries["count"]; $i++)
        {
            if ($includeDescription && strlen($entries[$i]["description"][0]) > 0)
            {
                $objectArray[$entries[$i]["samaccountname"][0]] = array(
                    $entries[$i]["cn"][0],
                    $entries[$i]["description"][0],
                    $entries[$i]["displayname"][0],
                    $entries[$i]["distinguishedname"][0]
                );
            } elseif ($includeDescription)
            {
                // description is set to displayname if no description is present
                $objectArray[$entries[$i]["samaccountname"][0]] = array(
                    $entries[$i]["cn"][0],
                    $entries[$i]["displayname"][0],
                    $entries[$i]["displayname"][0],
                    $entries[$i]["distinguishedname"][0]
                );
            } else
            {
                array_push($objectArray, $entries[$i]["samaccountname"][0]);
            }
        }

        if ($sorted) asort($objectArray);

        return $objectArray;
    }

    /**
     * Returns objectClass in an array
     *
     * @param string $distinguishedName The full DN of a contact
     * @return array|bool
     */
    public function getObjectClass($distinguishedName)
    {
        if ($distinguishedName === NULL) return false;

        if ( ! $this->getLdapBind()) return false;

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
        if ( ! $this->getLdapBind()) return (false);

        $filter = 'objectClass=*';

        $results = $this->ldapConnection->read(NULL, $filter, $attributes);

        $entries = $this->ldapConnection->getEntries($results);

        return $entries;
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
        return $this->ldapConnection->getLastError();
    }

    /**
     * Returns an LDAP compatible schema array for modifications.
     *
     * @param array $attributes Attributes to be queried
     * @return array|bool
     */
    public function adldap_schema(array $attributes)
    {
        // Check every attribute to see if it contains 8bit characters and then UTF8 encode them
        array_walk($attributes, array($this->utilities(), 'encode8bit'));

        $schema = new Schema($attributes);

        $ldapSchema = new LdapSchema();

        // Set all the LDAP attributes
        $ldapSchema->setAttribute('l', $schema->getAttribute('address_city'));

        $ldapSchema->setAttribute('postalCode', $schema->getAttribute('address_code'));

        $ldapSchema->setAttribute('c', $schema->getAttribute('address_country'));

        $ldapSchema->setAttribute('postOfficeBox', $schema->getAttribute('address_pobox'));

        $ldapSchema->setAttribute('st', $schema->getAttribute('address_state'));

        $ldapSchema->setAttribute('streetAddress', $schema->getAttribute('address_street'));

        $ldapSchema->setAttribute('company', $schema->getAttribute('company'));

        $ldapSchema->setAttribute('pwdLastSet', $schema->getAttribute('change_password'));

        $ldapSchema->setAttribute('department', $schema->getAttribute('department'));

        $ldapSchema->setAttribute('description', $schema->getAttribute('description'));

        $ldapSchema->setAttribute('displayName', $schema->getAttribute('display_name'));

        $ldapSchema->setAttribute('mail', $schema->getAttribute('email'));

        $ldapSchema->setAttribute('accountExpires', $schema->getAttribute('expires'));

        $ldapSchema->setAttribute('givenName', $schema->getAttribute('firstname'));

        $ldapSchema->setAttribute('homeDirectory', $schema->getAttribute('home_directory'));

        $ldapSchema->setAttribute('homeDrive', $schema->getAttribute('home_drive'));

        $ldapSchema->setAttribute('initials', $schema->getAttribute('initials'));

        $ldapSchema->setAttribute('userPrincipalName', $schema->getAttribute('logon_name'));

        $ldapSchema->setAttribute('manager', $schema->getAttribute('manager'));

        $ldapSchema->setAttribute('physicalDeliveryOfficeName', $schema->getAttribute('office'));

        $ldapSchema->setAttribute('unicodePwd', $schema->getAttribute('password'));

        $ldapSchema->setAttribute('profilepath', $schema->getAttribute('profile_path'));

        $ldapSchema->setAttribute('scriptPath', $schema->getAttribute('script_path'));

        $ldapSchema->setAttribute('sn', $schema->getAttribute('surname'));

        $ldapSchema->setAttribute('title', $schema->getAttribute('title'));

        $ldapSchema->setAttribute('telephoneNumber', $schema->getAttribute('telephone'));

        $ldapSchema->setAttribute('mobile', $schema->getAttribute('mobile'));

        $ldapSchema->setAttribute('pager', $schema->getAttribute('pager'));

        $ldapSchema->setAttribute('ipphone', $schema->getAttribute('ipphone'));

        $ldapSchema->setAttribute('wWWHomePage', $schema->getAttribute('web_page'));

        $ldapSchema->setAttribute('facsimileTelephoneNumber', $schema->getAttribute('fax'));

        $ldapSchema->setAttribute('userAccountControl', $schema->getAttribute('enabled'));

        $ldapSchema->setAttribute('homephone', $schema->getAttribute('homephone'));

        // Distribution List specific schema
        $ldapSchema->setAttribute('dlMemSubmitPerms', $schema->getAttribute('group_sendpermission'));

        $ldapSchema->setAttribute('dlMemRejectPerms', $schema->getAttribute('group_rejectpermission'));

        // Exchange Schema
        $ldapSchema->setAttribute('homeMDB', $schema->getAttribute('exchange_homemdb'));

        $ldapSchema->setAttribute('mailNickname', $schema->getAttribute('exchange_mailnickname'));

        $ldapSchema->setAttribute('proxyAddresses', $schema->getAttribute('exchange_proxyaddress'));

        $ldapSchema->setAttribute('mDBUseDefaults', $schema->getAttribute('exchange_usedefaults'));

        $ldapSchema->setAttribute('msExchPoliciesExcluded', $schema->getAttribute('exchange_policyexclude'));

        $ldapSchema->setAttribute('msExchPoliciesIncluded', $schema->getAttribute('exchange_policyinclude'));

        $ldapSchema->setAttribute('showInAddressBook', $schema->getAttribute('exchange_addressbook'));

        $ldapSchema->setAttribute('altRecipient', $schema->getAttribute('exchange_altrecipient'));

        $ldapSchema->setAttribute('deliverAndRedirect', $schema->getAttribute('exchange_deliverandredirect'));

        // This schema is designed for contacts
        $ldapSchema->setAttribute('msExchHideFromAddressLists', $schema->getAttribute('exchange_hidefromlists'));

        $ldapSchema->setAttribute('targetAddress', $schema->getAttribute('contact_email'));

        $ldapAttributes = $ldapSchema->getAttributes();

        if (count($ldapAttributes) === 0) return false;

        // Return a filtered array to remove NULL attributes
        return array_filter($ldapAttributes);
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
     * Select a random domain controller from your domain controller array
     *
     * @return string
     */
    protected function randomController()
    {
        return $this->domainControllers[array_rand($this->domainControllers)];
    }

    /**
     * Test basic connectivity to the domain controller
     *
     * @param string $host
     * @return bool
     */
    protected function pingController($host)
    {
        $port = $this->getPort();

        fsockopen($host, $port, $errno, $errstr, 10);

        if ($errno > 0) return false;

        return true;
    }

    /**
     * Binds to the current connection using kerberos
     *
     * @param $kerberosCredentials
     * @returns bool
     * @throws adLDAPException
     */
    private function bindUsingKerberos($kerberosCredentials)
    {
        putenv("KRB5CCNAME=" . $kerberosCredentials);

        $bound = $this->ldapConnection->bind(NULL, NULL, true);

        if ( ! $bound)
        {
            $message = 'Rebind to Active Directory failed. AD said: ' . $this->ldapConnection->getLastError();

            throw new adLDAPException($message);
        }

        return true;
    }

    /**
     * Binds to the current connection using administrator credentials
     *
     * @param string $username
     * @param string $password
     * @returns bool
     * @throws adLDAPException
     */
    private function bindUsingCredentials($username, $password)
    {
        if ($username === NULL || $password === NULL) return false;

        if (empty($username) || empty($password)) return false;

        $bindings = $this->ldapConnection->bind($username . $this->getAccountSuffix(), $password);

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

            throw new adLDAPException($message);
        }

        return true;
    }
}
