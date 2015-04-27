![AdLDAP 2 Banner]
(https://github.com/adldap2/adldap2/blob/master/adldap2-banner.jpg)

[![Build Status](https://img.shields.io/travis/Adldap2/Adldap2.svg?style=flat-square)](https://travis-ci.org/Adldap2/Adldap2)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/adLDAP2/adLDAP2/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/adLDAP2/adLDAP2/?branch=master)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/45a86fc2-b202-4f1b-9549-679900e5807c.svg?style=flat-square)](https://insight.sensiolabs.com/projects/45a86fc2-b202-4f1b-9549-679900e5807c)
[![Total Downloads](https://img.shields.io/packagist/dt/adldap2/adldap2.svg?style=flat-square)](https://packagist.org/packages/adldap2/adldap2)
[![Latest Stable Version](https://img.shields.io/packagist/v/adldap2/adldap2.svg?style=flat-square)](https://packagist.org/packages/adldap2/adldap2)
[![License](https://img.shields.io/packagist/l/adldap2/adldap2.svg?style=flat-square)](https://packagist.org/packages/adldap2/adldap2)

> Originally written by Scott Barnett and Richard Hyland. Adopted by the community.

## Description

> **ATTENTION**: Version v5.0.0 is in heavy development, however it is close to completion. Use 'dev-master' at you're own risk.

Adldap2 is a tested PHP class library that provides LDAP authentication and Active Directory management tools.

## Index

> **Note:** Documentation is incomplete as Adldap is currently in the upgrade process to version `5.0.0`. They will be fully featured and complete in the coming weeks.

- [Installation](#installation)
- [Testing With A Public AD Server](#need-to-test-an-ldap-connection)
- [Upgrading to v5 from v4](docs/UPGRADING.md)
- [Getting Started](docs/GETTING-STARTED.md)
- Usage 
 - [Search Functions](docs/SEARCH-FUNCTIONS.md)
 - [Computer Functions](docs/COMPUTER-FUNCTIONS.md)
 - [Contact Functions](docs/CONTACT-FUNCTIONS.md)
 - [Exchange Functions](docs/EXCHANGE-FUNCTIONS.md)
 - [Folder Functions](docs/FOLDER-FUNCTIONS.md)
 - [Group Functions](docs/GROUP-FUNCTIONS.md)
 - [User Functions](docs/USER-FUNCTIONS.md)

## Requirements

To use Adldap2, your sever must support:

- PHP 5.4 or greater
- PHP LDAP Extension


## Optional Requirements

If your AD server requires SSL, your server must support the following libraries:

- PHP SSL Libraries (http://php.net/openssl)

## Installation

Adldap2 has moved to a composer based installation. If you'd like to use Adldap without an auto-loader, you'll
have to require the files inside the project `src/` directory yourself.

Insert Adldap into your `composer.json` file:

    "adldap2/adldap2": "5.0.*"
   
Run `composer update`

You're good to go!

## Need to test an LDAP connection?

If you need to test something with access to an LDAP server, the generous folks at [Georgia Tech](http://drupal.gatech.edu/handbook/public-ldap-server) have you covered.

Use the following configuration:

    $config = array(
        'account_suffix' => "@gatech.edu",
    
        'domain_controllers' => array("whitepages.gatech.edu"),
    
        'base_dn' => 'dc=whitepages,dc=gatech,dc=edu',
    
        'admin_username' => '',
    
        'admin_password' => '',
    );
    
    $ad = new Adldap($config);
    
However while useful for basic testing, the queryable data only includes user data, so if you're looking for testing with any other information
or functionality such as modification, you'll have to use you're own server.
