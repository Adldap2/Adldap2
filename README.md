![AdLDAP 2 Banner]
(https://github.com/adldap2/adldap2/blob/master/adldap2-banner.jpg)

[![Build Status](https://travis-ci.org/adLDAP2/adLDAP2.svg)](https://travis-ci.org/adLDAP2/adLDAP2)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/adLDAP2/adLDAP2/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/adLDAP2/adLDAP2/?branch=master)
[![Total Downloads](https://poser.pugx.org/adldap2/adldap2/downloads.svg)](https://packagist.org/packages/adldap2/adldap2)
[![Latest Stable Version](https://poser.pugx.org/adldap2/adldap2/v/stable.svg)](https://packagist.org/packages/adldap2/adldap2)
[![License](https://poser.pugx.org/adldap2/adldap2/license.svg)](https://packagist.org/packages/adldap2/adldap2)

> **Note:** This is a fork of the main abandoned adLDAP repository. You can file issues and pull-requests here and I will address / merge them.
> Looking for the original repository? [Click here](https://github.com/adLDAP/adLDAP).

Originally written by Scott Barnett and Richard Hyland. Adopted by the community.

## To Do

- Completely refresh all classes and improve the terrible scrutinizer score
- Unit / Functional Testing
- Better, and easier to understand documentation
- Create LDAP encapsulation class for easier testing and possible support for other connections

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

    "adldap2/adldap2": "4.0.*"

Run `composer update` on your project source, and you're all set!

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
