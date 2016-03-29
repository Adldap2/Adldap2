# Adldap2

[![Build Status](https://img.shields.io/travis/Adldap2/Adldap2/v5.2.11.svg?style=flat-square)](https://travis-ci.org/Adldap2/Adldap2)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/adLDAP2/adLDAP2/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/adLDAP2/adLDAP2/?branch=master)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/45a86fc2-b202-4f1b-9549-679900e5807c.svg?style=flat-square)](https://insight.sensiolabs.com/projects/45a86fc2-b202-4f1b-9549-679900e5807c)
[![Total Downloads](https://img.shields.io/packagist/dt/adldap2/adldap2.svg?style=flat-square)](https://packagist.org/packages/adldap2/adldap2)
[![Latest Stable Version](https://img.shields.io/packagist/v/adldap2/adldap2.svg?style=flat-square)](https://packagist.org/packages/adldap2/adldap2)
[![License](https://img.shields.io/packagist/l/adldap2/adldap2.svg?style=flat-square)](https://packagist.org/packages/adldap2/adldap2)

> Originally written by Scott Barnett and Richard Hyland. Adopted by the community.

[![Join the chat at https://gitter.im/Adldap2](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/Adldap2?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

## Description

Working with Active Directory doesn't need to be hard. Adldap2 is a tested PHP package that provides LDAP
authentication and Active Directory management tools using the Active Record pattern.

## Index

- [Installation](#installation)
- [Testing With A Public AD Server](#need-to-test-an-ldap-connection)
- Usage
 - [Configuration](https://github.com/adldap2/adldap2/tree/v5.2/docs/CONFIGURATION.md)
 - [Getting Started](https://github.com/adldap2/adldap2/tree/v5.2/docs/GETTING-STARTED.md)
 - [Searching](https://github.com/adldap2/adldap2/tree/v5.2/docs/SEARCH.md)
 - [Models](https://github.com/adldap2/adldap2/tree/v5.2/docs/models/INDEX.md)
 - [Working with Distinguished Names](https://github.com/adldap2/adldap2/tree/v5.2/docs/DISTINGUISHED-NAMES.md)
 - [API Documentation](http://adldap2.github.io/api/v5.2.0)
- Classes
  - [Users](https://github.com/adldap2/adldap2/tree/v5.2/docs/classes/USERS.md)
  - [Groups](https://github.com/adldap2/adldap2/tree/v5.2/docs/classes/GROUPS.md)
  - [Containers](https://github.com/adldap2/adldap2/tree/v5.2/docs/classes/CONTAINERS.md)
  - [Computers](https://github.com/adldap2/adldap2/tree/v5.2/docs/classes/COMPUTERS.md)
  - [Printers](https://github.com/adldap2/adldap2/tree/v5.2/docs/classes/PRINTERS.md)
  - [Organizational Units (OUs)](https://github.com/adldap2/adldap2/tree/v5.2/docs/classes/ORGANIZATIONAL-UNITS.md)
  - [Exchange Servers](https://github.com/adldap2/adldap2/tree/v5.2/docs/classes/EXCHANGE.md)
- [Implementations](#implementations)

## Requirements

To use Adldap2, your sever must support:

- PHP 5.4.26 or greater
- PHP LDAP Extension

If your project does not use composer and you would like to use the search functionality you must include at minimum [Doctrine Collections](https://github.com/doctrine/collections)


## Optional Requirements

> **Note: Adldap makes use of `ldap_modify_batch()` for processing modifications to models**. Your server
must be on **PHP >= 5.4.26 || >= 5.5.10 || >= 5.6.0** to make modifications.

If your AD server requires SSL, your server must support the following libraries:

- PHP SSL Libraries (http://php.net/openssl)

## Installation

Adldap2 has moved to a composer based installation. If you'd like to use Adldap without an auto-loader, you'll
have to require the files inside the project `src/` directory yourself.

Insert Adldap into your `composer.json` file:

    "adldap2/adldap2": "5.2.*"
   
Run `composer update`

You're good to go!

## Implementations

- [Laravel](https://github.com/Adldap2/Adldap2-Laravel)
- [Kohana](https://github.com/Adldap2/Adldap2-Kohana)
- [Yii2 Wrapper](https://github.com/edvler/yii2-adldap-module)

## Versioning

Adldap2 is versioned under the [Semantic Versioning](http://semver.org/) guidelines as much as possible.

Releases will be numbered with the following format:

`<major>.<minor>.<patch>`

And constructed with the following guidelines:

* Breaking backward compatibility bumps the major and resets the minor and patch.
* New additions without breaking backward compatibility bumps the minor and resets the patch.
* Bug fixes and misc changes bumps the patch.

Minor versions are not maintained individually, and you're encouraged to upgrade through to the next minor version.

Major versions are maintained individually through separate branches.
