![AdLDAP 2 Banner]
(https://github.com/adldap2/adldap2/blob/master/adldap2-banner.jpg)

[![Build Status](https://img.shields.io/travis/Adldap2/Adldap2.svg?style=flat-square)](https://travis-ci.org/Adldap2/Adldap2)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/adLDAP2/adLDAP2/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/adLDAP2/adLDAP2/?branch=master)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/45a86fc2-b202-4f1b-9549-679900e5807c.svg?style=flat-square)](https://insight.sensiolabs.com/projects/45a86fc2-b202-4f1b-9549-679900e5807c)
[![Total Downloads](https://img.shields.io/packagist/dt/adldap2/adldap2.svg?style=flat-square)](https://packagist.org/packages/adldap2/adldap2)
[![Latest Stable Version](https://img.shields.io/packagist/v/adldap2/adldap2.svg?style=flat-square)](https://packagist.org/packages/adldap2/adldap2)
[![License](https://img.shields.io/packagist/l/adldap2/adldap2.svg?style=flat-square)](https://packagist.org/packages/adldap2/adldap2)

Originally written by Scott Barnett and Richard Hyland. Adopted by the community.

## To Do

- Improve / tweak refactored classes
- Unit / Functional Testing
- Better, and easier to understand documentation

#### Primary Goal

- Keep existing API for easy upgrades for existing users

## Description

adLDAP2 is a PHP class library that provides LDAP authentication and Active Directory management tools.

## Requirements

To use adLDAP2, your sever must support:

- PHP 5.3 or greater
- PHP SSL Libraries (http://php.net/openssl)

## Installation

Add adLDAP2 to your `composer.json` file:

    "adldap2/adldap2": "5.0.*"

Run `composer update` on your project source, and you're all set!

## Documentation

You can find our website at https://github.com/adldap/adLDAP/ or the class documentation at

https://github.com/adldap/adLDAP/wiki/adLDAP-Developer-API-Reference

## Upgrading to 5.0.0

Breaking changes:

- Requires have been removed from all classes, and if using without an auto-loader (such as composer) you must require all
necessary files yourself
- `adLDAP\adLDAPException` now has a new namespace: `Adldap\Exceptions\AdldapException`
- `$adldap->user()->modify()` now throws an `AdldapException` when the username parameter is null
- Inserting null/empty values into the username and/or password inside the `authenticate($username, $password)` function will now
result in an `AdldapException`, instead of returning false
- Inserting null into the group name parameter inside the method `$adldap->group()->info($groupName)` now throws an Adldap exception
instead of returning false
- Inserting null into the username parameter inside the method `$adldap->user()->info($username)` now throws an Adldap exception
instead of returning false
- If LDAP is not bound when running query methods (such as `$adldap->search()`) then an `AdldapException` will be thrown instead
of previously returning false.


The arguments for finding a user has been changed from:

    $adldap->user()->find($includeDescription = false, $searchField = false, $searchFilter = false, $sorted = true)

To:

    $adldap->user()->find($includeDescription = false, $searchArray = array(), $sorted = true))
    
This allows you to search for multiple parameters when looking for a user. [Thanks To](https://github.com/adldap/adLDAP/pull/17)

All namespaces and files have been cased correctly to adhere to PSR-4 standards. For example:

Namespace: `namespace adLDAP\adLDAP` has changed to `namespace Adldap\Adldap`

File: `require('adLDAP/adLDAP.php')`has changed to `require('Adldap/Adldap.php')`
