![AdLDAP 2 Banner]
(https://github.com/adldap2/adldap2/blob/master/adldap2-banner.jpg)

[![Build Status](https://img.shields.io/travis/Adldap2/Adldap2.svg?style=flat-square)](https://travis-ci.org/Adldap2/Adldap2)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/adLDAP2/adLDAP2/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/adLDAP2/adLDAP2/?branch=master)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/45a86fc2-b202-4f1b-9549-679900e5807c.svg?style=flat-square)](https://insight.sensiolabs.com/projects/45a86fc2-b202-4f1b-9549-679900e5807c)
[![Total Downloads](https://img.shields.io/packagist/dt/adldap2/adldap2.svg?style=flat-square)](https://packagist.org/packages/adldap2/adldap2)
[![Latest Stable Version](https://img.shields.io/packagist/v/adldap2/adldap2.svg?style=flat-square)](https://packagist.org/packages/adldap2/adldap2)
[![License](https://img.shields.io/packagist/l/adldap2/adldap2.svg?style=flat-square)](https://packagist.org/packages/adldap2/adldap2)

Originally written by Scott Barnett and Richard Hyland. Adopted by the community.

## Description

Adldap2 is a PHP class library that provides LDAP authentication and Active Directory management tools.

## Index

- [Installation](#installation)
- [Upgrading to v5 from v4](docs/UPGRADING.md)
- [Getting Started](docs/GETTING-STARTED.md)
- Usage 
 - [Computer Functions](docs/COMPUTER-FUNCTIONS.md)
 - [Contact Functions](docs/CONTACT-FUNCTIONS.md)
 - [Exchange Functions](docs/EXCHANGE-FUNCTIONS.md)
 - [Folder Functions](docs/FOLDER-FUNCTIONS.md)
 - [Group Functions](docs/GROUP-FUNCTIONS.md)
 - [User Functions](docs/USER-FUNCTIONS.md)

## Requirements

To use adLDAP2, your sever must support:

- PHP 5.3 or greater
- PHP SSL Libraries (http://php.net/openssl)

## Installation

Adldap2 has moved to a composer based installation. If you'd like to use Adldap without an auto-loader, you'll
have to require the files inside the project `src/` directory yourself.

Insert Adldap into your `composer.json` file:

    "adldap2/adldap2": "5.0.*"
   
Run `composer update`

You're good to go!
