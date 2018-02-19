<h1 align="center">Adldap2</h1>

<p align="center">
<a href="https://travis-ci.org/Adldap2/Adldap2"><img src="https://img.shields.io/travis/Adldap2/Adldap2.svg?style=flat-square"/></a>
<a href="https://scrutinizer-ci.com/g/Adldap2/Adldap2/?branch=master"><img src="https://img.shields.io/scrutinizer/g/adLDAP2/adLDAP2/master.svg?style=flat-square"/></a>
<a href="https://packagist.org/packages/adldap2/adldap2"><img src="https://img.shields.io/packagist/dt/adldap2/adldap2.svg?style=flat-square"/></a>
<a href="https://packagist.org/packages/adldap2/adldap2"><img src="https://img.shields.io/packagist/v/adldap2/adldap2.svg?style=flat-square"/></a>
<a href="https://packagist.org/packages/adldap2/adldap2"><img src="https://img.shields.io/packagist/l/adldap2/adldap2.svg?style=flat-square"/></a>
</p>

<p align="center">
Working with LDAP doesn't need to be hard.
</p>

<p align="center">
Adldap2 is a PHP package that provides LDAP authentication and directory management tools using the Active Record pattern.
</p>

## Index

 - [Quick Start](docs/quick-start.md)
 - [Configuration](docs/configuration.md)
 - [Connecting](docs/connecting.md)
 - [Authenticating](docs/authenticating.md)
 - [Query Builder (Searching)](docs/query-builder.md)
 - [Models](docs/models/model.md)
    - [Computer](docs/models/computer.md)
    - [Contact](docs/models/contact.md)
    - [Container](docs/models/container.md)
    - [Entry](docs/models/entry.md)
    - [Group](docs/models/group.md)
    - [Organizational Unit](docs/models/ou.md)
    - [Printer](docs/models/printer.md)
    - [RootDse](docs/models/root-dse.md)
    - [User](docs/models/user.md)
 - [Working with Distinguished Names](docs/distinguished-names.md)
 - [Schema](docs/schema.md)
 - [Troubleshooting](docs/troubleshooting.md)

## Installation

### Requirements

To use Adldap2, your server must support:

- PHP 5.5.9 or greater
- PHP LDAP Extension
- An LDAP Server

> **Note**: OpenLDAP support is experimental, success may vary.

### Optional Requirements

> **Note: Adldap makes use of `ldap_modify_batch()` for executing modifications to LDAP records**. Your server
must be on **PHP >= 5.5.10 || >= 5.6.0** to make modifications.

If your AD server requires SSL, your server must support the following libraries:

- PHP SSL Libraries (http://php.net/openssl)

### Installing

Adldap2 utilizes composer for installation.

Run the following command in the root directory of your project:

```
composer require adldap2/adldap2
```

> **Note**: If you're upgrading from an earlier release, please take a look
> at the [release notes](https://github.com/Adldap2/Adldap2/releases).

## Implementations

- [Laravel](https://github.com/Adldap2/Adldap2-Laravel)
- [Kohana](https://github.com/Adldap2/Adldap2-Kohana)

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
