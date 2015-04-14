<?php

namespace adLDAP\classes;

use adLDAP\Exceptions\adLDAPException;
use adLDAP\Objects\Mailbox;

/**
 * Ldap Microsoft Exchange Management
 *
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
 * @category ToolsAndUtilities
 * @package adLDAP
 * @subpackage Exchange
 * @author Scott Barnett, Richard Hyland
 * @copyright (c) 2006-2014 Scott Barnett, Richard Hyland
 * @license http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html LGPLv2.1
 * @version 5.0.0
 * @link http://github.com/adldap/adLDAP
 *
 * Class adLDAPExchange
 * @package adLDAP\classes
 */
class adLDAPExchange extends adLDAPBase
{
    /**
     * Create an Exchange account.
     *
     * @param string $username The username of the user to add the Exchange account to
     * @param array $storageGroup The mailbox, Exchange Storage Group, for the user account, this must be a full CN
     * @param string $emailAddress The primary email address to add to this user
     * @param null $mailNickname The mail nick name. If mail nickname is blank, the username will be used
     * @param bool $useDefaults Indicates whether the store should use the default quota, rather than the per-mailbox quota.
     * @param null $baseDn Specify an alternative base_dn for the Exchange storage group
     * @param bool $isGUID Is the username passed a GUID or a samAccountName
     * @return bool|string
     * @throws adLDAPException
     */
    public function createMailbox($username, $storageGroup, $emailAddress, $mailNickname = NULL, $useDefaults = TRUE, $baseDn = NULL, $isGUID = false)
    {
        $mailbox = new Mailbox(array(
            'username' => $username,
            'storageGroup' => $storageGroup,
            'emailAddress' =>  $emailAddress,
            'mailNickname' => $mailNickname,
            'baseDn' => ($baseDn ? $baseDn : $this->adldap->getBaseDn()),
            'mdbUseDefaults' => $this->adldap->utilities()->boolToStr($useDefaults),
            'container' => "CN=" . implode(",CN=", $storageGroup),
        ));

        // Throw an exception if the storageGroup is not an array
        if ( ! is_array($mailbox->getAttribute('storageGroup'))) throw new adLDAPException("Attribute [storageGroup] must be an array");

        // Validate the other required field
        $mailbox->validateRequired();

        // Set the mail nickname to the username if it isn't provided
        if($mailbox->{'mailNickname'} === null) $mailbox->setAttribute('mailNickname', $mailbox->{'username'});

        // Perform the creation on the current connection
        $result = $this->adldap->user()->modify($username, $mailbox->toLdapArray(), $isGUID);

        // Return the result
        if ($result === false) return false;

        return true;
    }

    /**
     * Add an X400 address to Exchange.
     * See http://tools.ietf.org/html/rfc1685 for more information.
     * An X400 Address looks similar to this X400:c=US;a= ;p=Domain;o=Organization;s=Doe;g=John;
     *
     * @param string $username The username of the user to add the X400 to to
     * @param string $country Country
     * @param string $admd Administration Management Domain
     * @param string $pdmd Private Management Domain (often your AD domain)
     * @param string $org Organization
     * @param string $surname Surname
     * @param string $givenName Given name
     * @param bool $isGUID Is the username passed a GUID or a samAccountName
     * @return bool|string
     */
    public function addX400($username, $country, $admd, $pdmd, $org, $surname, $givenName, $isGUID = false)
    {
        $this->adldap->utilities()->validateNotNull('Username', $username);
        
        $proxyValue = 'X400:';
            
        // Find the dn of the user
        $user = $this->adldap->user()->info($username, array("cn", "proxyaddresses"), $isGUID);

        if ($user[0]["dn"] === NULL) return false;

        $userDn = $user[0]["dn"];
        
        // We do not have to demote an email address from the default so we can just add the new proxy address
        $attributes['exchange_proxyaddress'] = $proxyValue . 'c=' . $country . ';a=' . $admd . ';p=' . $pdmd . ';o=' . $org . ';s=' . $surname . ';g=' . $givenName . ';';
       
        // Translate the update to the LDAP schema                
        $add = $this->adldap->ldapSchema($attributes);
        
        if ( ! $add) return false;

        /*
         * Perform the update, take out the '@' to see any errors,
         * usually this error might occur because the address already
         * exists in the list of proxyAddresses
         */
        return $this->connection->add($userDn, $add);
    }

    /**
     * @param string $username The username of the user to add the Exchange account to
     * @param string $emailAddress The email address to add to this user
     * @param bool $default Make this email address the default address, this is a bit more intensive as we have to demote any existing default addresses
     * @param bool $isGUID Is the username passed a GUID or a samAccountName
     * @return bool|string
     */
    public function addAddress($username, $emailAddress, $default = FALSE, $isGUID = false)
    {
        $this->adldap->utilities()->validateNotNull('Username', $username);
        $this->adldap->utilities()->validateNotNull('Email Address', $emailAddress);
        
        $proxyValue = 'smtp:';

        if ($default === true) $proxyValue = 'SMTP:';
              
        // Find the dn of the user
        $user = $this->adldap->user()->info($username, array("cn","proxyaddresses"), $isGUID);

        if ($user[0]["dn"] === NULL) return false;

        $userDn = $user[0]["dn"];
        
        // We need to scan existing proxy addresses and demote the default one
        if (is_array($user[0]["proxyaddresses"]) && $default === true)
        {
            $modAddresses = array();

            for ($i = 0; $i < $user[0]['proxyaddresses']['count']; $i++)
            {
                if (strpos($user[0]['proxyaddresses'][$i], 'SMTP:') !== false)
                {
                    $user[0]['proxyaddresses'][$i] = str_replace('SMTP:', 'smtp:', $user[0]['proxyaddresses'][$i]);
                }

                if ($user[0]['proxyaddresses'][$i] != '')
                {
                    $modAddresses['proxyAddresses'][$i] = $user[0]['proxyaddresses'][$i];
                }
            }

            $modAddresses['proxyAddresses'][($user[0]['proxyaddresses']['count']-1)] = 'SMTP:' . $emailAddress;

            $result = $this->connection->modReplace($userDn, $modAddresses);
        } else
        {
            // We do not have to demote an email address from the default so we can just add the new proxy address
            $attributes['exchange_proxyaddress'] = $proxyValue . $emailAddress;
            
            // Translate the update to the LDAP schema                
            $add = $this->adldap->ldapSchema($attributes);
            
            if ( ! $add) return false;

            /*
             * Perform the update, take out the '@' to see any errors,
             * usually this error might occur because the address already
             * exists in the list of proxyAddresses
             */
            $result = $this->connection->modAdd($userDn, $add);
        }

        return $result;
    }

    /**
     * Remove an address to Exchange.
     * If you remove a default address the account will no longer have a default,
     * we recommend changing the default address first.
     *
     * @param string $username The username of the user to add the Exchange account to
     * @param string $emailAddress The email address to add to this user
     * @param bool $isGUID Is the username passed a GUID or a samAccountName
     * @return bool|string
     */
    public function deleteAddress($username, $emailAddress, $isGUID=false)
    {
        if ($username === NULL) return "Missing compulsory field [username]";

        if ($emailAddress === NULL) return "Missing compulsory fields [emailAddress]";
        
        // Find the dn of the user
        $user = $this->adldap->user()->info($username, array("cn","proxyaddresses"), $isGUID);

        if ($user[0]["dn"] === NULL) return false;

        $userDn = $user[0]["dn"];
        
        if (is_array($user[0]["proxyaddresses"]))
        {
            $mod = array();

            for ($i = 0; $i < $user[0]['proxyaddresses']['count']; $i++)
            {
                if (strpos($user[0]['proxyaddresses'][$i], 'SMTP:') !== false && $user[0]['proxyaddresses'][$i] == 'SMTP:' . $emailAddress)
                {
                    $mod['proxyAddresses'][0] = 'SMTP:' . $emailAddress;
                }
                elseif (strpos($user[0]['proxyaddresses'][$i], 'smtp:') !== false && $user[0]['proxyaddresses'][$i] == 'smtp:' . $emailAddress)
                {
                    $mod['proxyAddresses'][0] = 'smtp:' . $emailAddress;
                }
            }

            $result = $this->connection->modDelete($userDn, $mod);

            if ($result == false) return false;

            return true;
        }

        return false;
    }

    /**
     * Change the default address.
     *
     * @param string $username The username of the user to add the Exchange account to
     * @param string $emailAddress The email address to make default
     * @param bool $isGUID Is the username passed a GUID or a samAccountName
     * @return bool|string
     */
    public function primaryAddress($username, $emailAddress, $isGUID = false)
    {
        if ($username === NULL) return "Missing compulsory field [username]";

        if ($emailAddress === NULL) return "Missing compulsory fields [emailAddress]";
        
        // Find the dn of the user
        $user = $this->adldap->user()->info($username, array("cn","proxyaddresses"), $isGUID);

        if ($user[0]["dn"] === NULL) return false;

        $userDn = $user[0]["dn"];
        
        if (is_array($user[0]["proxyaddresses"]))
        {
            $modAddresses = array();

            for ($i = 0; $i < $user[0]['proxyaddresses']['count']; $i++)
            {
                if (strpos($user[0]['proxyaddresses'][$i], 'SMTP:') !== false)
                {
                    $user[0]['proxyaddresses'][$i] = str_replace('SMTP:', 'smtp:', $user[0]['proxyaddresses'][$i]);
                }

                if ($user[0]['proxyaddresses'][$i] == 'smtp:' . $emailAddress)
                {
                    $user[0]['proxyaddresses'][$i] = str_replace('smtp:', 'SMTP:', $user[0]['proxyaddresses'][$i]);
                }

                if ($user[0]['proxyaddresses'][$i] != '')
                {
                    $modAddresses['proxyAddresses'][$i] = $user[0]['proxyaddresses'][$i];
                }
            }
            
            $result = $this->connection->modReplace($userDn, $modAddresses);

            if ($result == false) return false;

            return true;
        }

        return false;
    }
    
    /**
    * Mail enable a contact
    * Allows email to be sent to them through Exchange
    * 
    * @param string $distinguishedName The contact to mail enable
    * @param string $emailAddress The email address to allow emails to be sent through
    * @param string $mailNickname The mailnickname for the contact in Exchange.  If NULL this will be set to the display name
    * @return bool
    */
    public function contactMailEnable($distinguishedName, $emailAddress, $mailNickname = NULL)
    {
        if ($distinguishedName === NULL) return "Missing compulsory field [distinguishedName]";

        if ($emailAddress === NULL) return "Missing compulsory field [emailAddress]";
        
        if ($mailNickname !== NULL)
        {
            // Find the dn of the user
            $user = $this->adldap->contact()->info($distinguishedName, array("cn","displayname"));

            if ($user[0]["displayname"] === NULL) return false;

            $mailNickname = $user[0]['displayname'][0];
        }
        
        $attributes = array("email"=>$emailAddress,"contact_email"=>"SMTP:" . $emailAddress,"exchange_proxyaddress"=>"SMTP:" . $emailAddress,"exchange_mailnickname" => $mailNickname);
         
        // Translate the update to the LDAP schema                
        $mod = $this->adldap->ldapSchema($attributes);
        
        // Check to see if this is an enabled status update
        if ( ! $mod) return false;
        
        // Do the update
        $result = $this->connection->modify($distinguishedName, $mod);

        if ($result == false) return false;
 
        return true;
    }

    /**
     * Returns a list of Exchange Servers in the ConfigurationNamingContext of the domain
     *
     * @param array $attributes An array of the AD attributes you wish to return
     * @return array|bool
     */
    public function servers($attributes = array('cn','distinguishedname','serialnumber'))
    {
        if ( ! $this->adldap->getLdapBind()) return false;
        
        $configurationNamingContext = $this->adldap->getRootDse(array('configurationnamingcontext'));

        $filter = '(&(objectCategory=msExchExchangeServer))';

        $results = $this->connection->search($configurationNamingContext[0]['configurationnamingcontext'][0], $filter, $attributes);

        $entries = $this->connection->getEntries($results);

        return $entries;
    }

    /**
     * Returns a list of Storage Groups in Exchange for a given mail server
     *
     * @param string $exchangeServer The full DN of an Exchange server.  You can use exchange_servers() to find the DN for your server
     * @param array $attributes An array of the AD attributes you wish to return
     * @param null $recursive If enabled this will automatically query the databases within a storage group
     * @return array|bool|string
     */
    public function storageGroups($exchangeServer, $attributes = array('cn','distinguishedname'), $recursive = NULL)
    {
        if ( ! $this->adldap->getLdapBind()) return false;

        if ($exchangeServer === NULL) return "Missing compulsory field [exchangeServer]";

        if ($recursive === NULL) $recursive = $this->adldap->getRecursiveGroups();

        $filter = '(&(objectCategory=msExchStorageGroup))';

        $results = $this->connection->search($exchangeServer, $filter, $attributes);

        $entries = $this->connection->getEntries($results);

        if ($recursive === true)
        {
            for ($i = 0; $i < $entries['count']; $i++)
            {
                $entries[$i]['msexchprivatemdb'] = $this->storageDatabases($entries[$i]['distinguishedname'][0]);       
            }
        }

        return $entries;
    }

    /**
     * Returns a list of Databases within any given storage group in Exchange for a given mail server
     *
     * @param string $storageGroup The full DN of an Storage Group.  You can use exchange_storage_groups() to find the DN
     * @param array $attributes An array of the AD attributes you wish to return
     * @return array|bool|string
     */
    public function storageDatabases($storageGroup, $attributes = array('cn','distinguishedname','displayname'))
    {
        if ( ! $this->adldap->getLdapBind()) return false;

        if ($storageGroup === NULL) return "Missing compulsory field [storageGroup]";
        
        $filter = '(&(objectCategory=msExchPrivateMDB))';

        $results = $this->connection->search($storageGroup, $filter, $attributes);

        $entries = $this->connection->getEntries($results);

        return $entries;
    }
}
