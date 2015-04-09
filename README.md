# PHP Ldap Library for Active Directory Manipulation

[![Build Status](https://travis-ci.org/stevebauman/adldap-fork.svg)](https://travis-ci.org/stevebauman/adldap-fork)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/stevebauman/adldap-fork/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/stevebauman/adldap-fork/?branch=master)
[![Total Downloads](https://poser.pugx.org/stevebauman/adldap-fork/downloads.svg)](https://packagist.org/packages/stevebauman/adldap-fork)
[![Latest Stable Version](https://poser.pugx.org/stevebauman/adldap-fork/v/stable.svg)](https://packagist.org/packages/stevebauman/adldap-fork)
[![License](https://poser.pugx.org/stevebauman/adldap-fork/license.svg)](https://packagist.org/packages/stevebauman/adldap-fork)

> **Note:** This is a fork of the main adLDAP repository. You can file issues and pull-requests here and I will address / merge them.
> Looking for the main repository? [Main adLDAP Repository](https://github.com/adLDAP/adLDAP).

Originally written by Scott Barnett, Richard Hyland
email: scott@wiggumworld.com, adldap@richardhyland.com
https://github.com/adldap/adLDAP/

## To do

- Completely refresh all classes and improve the terrible scrutinizer score
- Unit / Functional Testing
- Better, and easier to understand documentation
- Create LDAP encapsulation class for easier testing

## About

adLDAP is a PHP class that provides LDAP authentication and integration with Active Directory.

We'd appreciate any improvements or additions to be submitted back
to benefit the entire community :)

## Requirements

adLDAP requires PHP 5 and both the LDAP (http://php.net/ldap) and SSL (http://php.net/openssl) libraries
adLDAP version 5.0.0 will require PHP 5.3+

## Installation

#### Via Composer

Add adldap-fork to your `composer.json` file:

    "stevebauman/adldap-fork": "4.0.*"

Run `composer update` on your project source, and you're all set!

#### Manual Installation

The core of adLDAP is contained in the 'lib/adLDAP' directory.  Simply copy/rename this directory inside your own
projects.

Edit the file ``lib/adldap/adLDAP.php`` and change the configuration variables near the top, specifically
those for domain controllers, base dn and account suffix, and if you want to perform anything more complex
than use authentication you'll also need to set the admin username and password variables too.

From within your code simply require the adLDAP.php file and call it like so

    use \adLDAP;
    
    require_once(dirname(__FILE__) . '/adLDAP.php');
    
    $adldap = new adLDAP();

It would be better to wrap it in a try/catch though

    use \adLDAP;
    
    try
    {
        $adldap = new adLDAP();
    }
    catch (adLDAPException $e)
    {
        echo $e;
        exit();   
    }

Then simply call commands against it e.g.

``$adldap->authenticate($username, $password);``

or 

``$adldap->group()->members($groupName);``

## Documentation

You can find our website at https://github.com/adldap/adLDAP/ or the class documentation at

https://github.com/adldap/adLDAP/wiki/adLDAP-Developer-API-Reference

## License

This library is free software; you can redistribute it and/or modify it under the terms of the 
GNU Lesser General Public License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
See the GNU Lesser General Public License for more details or LICENSE.txt distributed with
this class.
