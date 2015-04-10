<?php

namespace adLDAP;

use adLDAP\Interfaces\ConnectionInterface;

require_once(dirname(__FILE__) . '/Interfaces/ConnectionInterface.php');
require_once(dirname(__FILE__) . '/Connections/LDAP.php');
require_once(dirname(__FILE__) . '/collections/adLDAPCollection.php');
require_once(dirname(__FILE__) . '/classes/adLDAPGroups.php');
require_once(dirname(__FILE__) . '/classes/adLDAPUsers.php');
require_once(dirname(__FILE__) . '/classes/adLDAPFolders.php');
require_once(dirname(__FILE__) . '/classes/adLDAPUtils.php');
require_once(dirname(__FILE__) . '/classes/adLDAPContacts.php');
require_once(dirname(__FILE__) . '/classes/adLDAPExchange.php');
require_once(dirname(__FILE__) . '/classes/adLDAPComputers.php');

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
     * @var mixed
     */
    protected $ldapConnection;

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
        $this->baseDn = $baseDn;
    }

    /**
     * The group class
     *
     * @var \adLDAP\classes\adLDAPGroups
     */
    protected $groupClass;

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
     * The user class
     *
     * @var \adLDAP\classes\adLDAPUsers
     */
    protected $userClass;

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
     * The folders class
     *
     * @var \adLDAP\classes\adLDAPFolders
     */
    protected $folderClass;

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
     * The utils class
     *
     * @var \adLDAP\classes\adLDAPUtils
     */
    protected $utilClass;

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
     * The contacts class
     *
     * @var \adLDAP\classes\adLDAPContacts
     */
    protected $contactClass;

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
     * The exchange class
     *
     * @var \adLDAP\classes\adLDAPExchange
     */
    protected $exchangeClass;

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
     * The computers class
     *
     * @var \adLDAP\classes\adLDAPComputers
     */
    protected $computerClass;

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
        $this->accountSuffix = $accountSuffix;
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
     */
    public function setDomainControllers(array $domainControllers)
    {
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
     */
    public function setPort($adPort)
    {
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
        $this->recursiveGroups = $recursiveGroups;
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
     * Constructor.
     *
     * Tries to bind to the AD domain over LDAP or LDAPs
     *
     * @param array $options The adLDAP configuration options array
     * @param mixed $connection The connection you'd like to use
     * @throws adLDAPException
     */
    function __construct(array $options = array(), $connection = NULL)
    {
        // Create a new LDAP Connection if one isn't set
        if( ! $connection) $connection = new Connections\LDAP;

        $this->setLdapConnection($connection);

        // Check if LDAP is supported
        if ($this->ldapConnection->isSupported() === false)
        {
            throw new adLDAPException('No LDAP support for PHP.  See: http://www.php.net/ldap');
        }

        // You can specifically overide any of the default configuration options setup above
        if (count($options) > 0)
        {
            if (array_key_exists("account_suffix", $options)) $this->setAccountSuffix($options["account_suffix"]);

            if (array_key_exists("base_dn" ,$options)) $this->setBaseDn($options["base_dn"]);

            if (array_key_exists("domain_controllers", $options))
            {
                if ( ! is_array($options["domain_controllers"]))
                {
                    throw new adLDAPException('[domain_controllers] option must be an array');
                }

                $this->setDomainControllers($options["domain_controllers"]);
            }

            if (array_key_exists("admin_username", $options)) $this->setAdminUsername($options["admin_username"]);

            if (array_key_exists("admin_password", $options)) $this->setAdminPassword($options["admin_password"]);

            if (array_key_exists("real_primarygroup", $options)) $this->setRealPrimaryGroup($options["real_primarygroup"]);

            if (array_key_exists("use_ssl", $options)) $this->setUseSSL($options["use_ssl"]);

            if (array_key_exists("use_tls", $options)) $this->setUseTLS($options["use_tls"]);

            if (array_key_exists("recursive_groups", $options)) $this->setRecursiveGroups($options["recursive_groups"]);

            if (array_key_exists("follow_referrals", $options)) $this->followReferrals = $options["follow_referrals"];

            if (array_key_exists("ad_port", $options)) $this->setPort($options["ad_port"]);

            if (array_key_exists("sso", $options))
            {
                if($options['sso'])
                {
                    /*
                     * If we've set SSO to true, we'll make sure we check
                     * if SSO is supported, if so we'll bind it to the
                     * current LDAP connection.
                     */
                    if ($this->ldapConnection->isSaslSupported()) $this->ldapConnection->useSSO();
                }
            }
        }

        // Looks like we're all set. Let's try and connect
        return $this->connect();
    }

    /**
     * Destructor.
     *
     * Closes the current LDAP connection.
     *
     * @return void
     */
    function __destruct()
    {
        $this->close();
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

        if ($useSSL)
        {
            $this->ldapConnection->useSSL()->connect($domainController, $port);
        } else
        {
            $this->ldapConnection->connect($domainController, $port);
        }

        $this->ldapConnection->setOption(LDAP_OPT_PROTOCOL_VERSION, 3);
        $this->ldapConnection->setOption(LDAP_OPT_REFERRALS, $this->followReferrals);
        
        if ($useTLS) $this->ldapConnection->startTLS();
               
        // Bind as a domain admin if they've set it up
        if ($adminUsername !== NULL && $adminPassword !== NULL)
        {
            $bindings = $this->ldapConnection->bind($adminUsername . $this->getAccountSuffix(), $adminPassword);

            if ( ! $bindings)
            {
                $error = $this->ldapConnection->getLastError();

                if ($useSSL && ! $useTLS)
                {
                    // If you have problems troubleshooting, remove the @ character from the ldapldapBind command above to get the actual error message
                    $message = 'Bind to Active Directory failed. Either the LDAPs connection failed or the login credentials are incorrect. AD said: ' . $error;
                }
                else
                {
                    $message = 'Bind to Active Directory failed. Check the login credentials and/or server details. AD said: ' . $error;
                }

                throw new adLDAPException($message);
            }
        }

        if ($useSSO && $_SERVER['REMOTE_USER'] && ! $adminUsername && $_SERVER['KRB5CCNAME'])
        {
            putenv("KRB5CCNAME=" . $_SERVER['KRB5CCNAME']);

            if ( ! $this->ldapConnection->bind(NULL, NULL, true))
            {
                $message = 'Rebind to Active Directory failed. AD said: ' . $this->ldapConnection->getLastError();

                throw new adLDAPException($message);
            } 
            else
            {
                return true;
            }
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
        return $this->ldapConnection->close();
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
        // Prevent null binding
        if ($username === NULL || $password === NULL) return false;

        if (empty($username) || empty($password)) return false;
        
        // Allow binding over SSO for Kerberos
        if ($this->getUseSSO() && $_SERVER['REMOTE_USER'] && $_SERVER['REMOTE_USER'] == $username && $this->getAdminUsername() === NULL && $_SERVER['KRB5CCNAME'])
        {
            putenv("KRB5CCNAME=" . $_SERVER['KRB5CCNAME']);

            if ( ! $this->ldapConnection->bind(NULL, NULL, true))
            {
                throw new adLDAPException('Rebind to Active Directory failed. AD said: ' . $this->ldapConnection->getLastError());
            }
            else
            {
                return true;
            }
        }
        
        // Bind as the user
        $ret = true;

        $bindings = $this->ldapConnection->bind($username . $this->getAccountSuffix(), $password);

        if ( ! $bindings) $ret = false;
        
        // Once we've checked their details, kick back into admin mode if we have it
        if ($this->getAdminPassword() !== NULL && ! $preventRebind)
        {

            $bindings = $this->ldapConnection->bind($this->getAdminUsername() . $this->getAccountSuffix(), $this->getAdminPassword());

            if ( ! $bindings)
            {
                // This should never happen in theory
                throw new adLDAPException('Rebind to Active Directory failed. AD said: ' . $this->ldapConnection->getLastError());
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
        //$filter = "(|(cn=" . $search . ")(displayname=" . $search . ")(samaccountname=" . $search . "))";
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

        $fields = array("objectclass");

        $results = $this->ldapConnection->search($this->getBaseDn(), $filter, $fields);

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
     * Schema
     *
     * @param array $attributes Attributes to be queried
     * @return array|bool
     */
    public function adldap_schema($attributes)
    {
        // LDAP doesn't like NULL attributes, only set them if they have values
        // If you wish to remove an attribute you should set it to a space
        // TO DO: Adapt user_modify to use ldap_mod_delete to remove a NULL attribute
        $mod = array();
        
        // Check every attribute to see if it contains 8bit characters and then UTF8 encode them
        array_walk($attributes, array($this, 'encode8bit'));

        if (isset($attributes["address_city"])) $mod["l"][0] = $attributes["address_city"];

        if (isset($attributes["address_code"])) $mod["postalCode"][0] = $attributes["address_code"];

        //if ($attributes["address_country"]){ $mod["countryCode"][0]=$attributes["address_country"]; } // use country codes?
        if (isset($attributes["address_country"])) $mod["c"][0] = $attributes["address_country"];

        if (isset($attributes["address_pobox"])) $mod["postOfficeBox"][0] = $attributes["address_pobox"];

        if (isset($attributes["address_state"])) $mod["st"][0] = $attributes["address_state"];

        if (isset($attributes["address_street"])) $mod["streetAddress"][0] = $attributes["address_street"];

        if (isset($attributes["company"])) $mod["company"][0] = $attributes["company"];

        if (isset($attributes["change_password"])) $mod["pwdLastSet"][0] = 0;

        if (isset($attributes["department"])) $mod["department"][0] = $attributes["department"];

        if (isset($attributes["description"])) $mod["description"][0] = $attributes["description"];

        if (isset($attributes["display_name"])) $mod["displayName"][0] = $attributes["display_name"];

        if (isset($attributes["email"])) $mod["mail"][0] = $attributes["email"];

        if (isset($attributes["expires"])) $mod["accountExpires"][0] = $attributes["expires"]; //unix epoch format?

        if (isset($attributes["firstname"])) $mod["givenName"][0] = $attributes["firstname"];

        if (isset($attributes["home_directory"])) $mod["homeDirectory"][0] = $attributes["home_directory"];

        if (isset($attributes["home_drive"])) $mod["homeDrive"][0] = $attributes["home_drive"];

        if (isset($attributes["initials"])) $mod["initials"][0] = $attributes["initials"];

        if (isset($attributes["logon_name"])) $mod["userPrincipalName"][0] = $attributes["logon_name"];

        if (isset($attributes["manager"])) $mod["manager"][0] = $attributes["manager"]; //UNTESTED ***Use DistinguishedName***

        if (isset($attributes["office"])) $mod["physicalDeliveryOfficeName"][0] = $attributes["office"];

        if (isset($attributes["password"])) $mod["unicodePwd"][0] = $this->user()->encodePassword($attributes["password"]);

        if (isset($attributes["profile_path"])) $mod["profilepath"][0] = $attributes["profile_path"];

        if (isset($attributes["script_path"])) $mod["scriptPath"][0] = $attributes["script_path"];

        if (isset($attributes["surname"])) $mod["sn"][0] = $attributes["surname"];

        if (isset($attributes["title"])) $mod["title"][0] = $attributes["title"];

        if (isset($attributes["telephone"])) $mod["telephoneNumber"][0] = $attributes["telephone"];

        if (isset($attributes["mobile"])) $mod["mobile"][0] = $attributes["mobile"];

        if (isset($attributes["pager"])) $mod["pager"][0] = $attributes["pager"];

        if (isset($attributes["ipphone"])) $mod["ipphone"][0] = $attributes["ipphone"];

        if (isset($attributes["web_page"])) $mod["wWWHomePage"][0] = $attributes["web_page"];

        if (isset($attributes["fax"])) $mod["facsimileTelephoneNumber"][0] = $attributes["fax"];

        if (isset($attributes["enabled"])) $mod["userAccountControl"][0] = $attributes["enabled"];

        if (isset($attributes["homephone"])) $mod["homephone"][0] = $attributes["homephone"];

        // Distribution List specific schema
        if (isset($attributes["group_sendpermission"])) $mod["dlMemSubmitPerms"][0] = $attributes["group_sendpermission"];

        if (isset($attributes["group_rejectpermission"])) $mod["dlMemRejectPerms"][0] = $attributes["group_rejectpermission"];

        // Exchange Schema
        if (isset($attributes["exchange_homemdb"])) $mod["homeMDB"][0] = $attributes["exchange_homemdb"];

        if (isset($attributes["exchange_mailnickname"])) $mod["mailNickname"][0] = $attributes["exchange_mailnickname"];

        if (isset($attributes["exchange_proxyaddress"])) $mod["proxyAddresses"][0] = $attributes["exchange_proxyaddress"];

        if (isset($attributes["exchange_usedefaults"])) $mod["mDBUseDefaults"][0] = $attributes["exchange_usedefaults"];

        if (isset($attributes["exchange_policyexclude"])) $mod["msExchPoliciesExcluded"][0] = $attributes["exchange_policyexclude"];

        if (isset($attributes["exchange_policyinclude"])) $mod["msExchPoliciesIncluded"][0] = $attributes["exchange_policyinclude"];

        if (isset($attributes["exchange_addressbook"])) $mod["showInAddressBook"][0] = $attributes["exchange_addressbook"];

        if (isset($attributes["exchange_altrecipient"])) $mod["altRecipient"][0] = $attributes["exchange_altrecipient"];

        if (isset($attributes["exchange_deliverandredirect"])) $mod["deliverAndRedirect"][0] = $attributes["exchange_deliverandredirect"];

        // This schema is designed for contacts
        if (isset($attributes["exchange_hidefromlists"])) $mod["msExchHideFromAddressLists"][0] = $attributes["exchange_hidefromlists"];

        if (isset($attributes["contact_email"])) $mod["targetAddress"][0] = $attributes["contact_email"];

        if (count($mod) == 0) return (false);

        return ($mod);
    }

    /**
     * Convert 8bit characters e.g. accented characters to UTF8 encoded characters
     *
     * @depreciated Not finished from original development?
     * @param $item
     * @param $key
     * @return void
     */
    protected function encode8Bit(&$item, $key)
    {
        $encode = false;

        if (is_string($item))
        {
            for ($i = 0; $i < strlen($item); $i++)
            {
                if (ord($item[$i]) >> 7) $encode = true;
            }
        }

        if ($encode === true && $key != 'password')
        {
            $item = utf8_encode($item);   
        }
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

}

/**
* adLDAP Exception Handler
* 
* Exceptions of this type are thrown on bind failure or when SSL is required but not configured
* Example:
* try {
*   $adldap = new adLDAP();
* }
* catch (adLDAPException $e) {
*   echo $e;
*   exit();
* }
*/
class adLDAPException extends \Exception {}

?>
