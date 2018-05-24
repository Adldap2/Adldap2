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

---

## Index

- [Introduction](docs/introduction.md)
- [Installation](docs/installation.md)
- [Setup](docs/setup.md)
- [Searching](docs/searching.md)
- [Creating / Updating](docs/models/model.md)
  - [Computer](docs/models/computer.md)
  - [Contact](docs/models/contact.md)
  - [Container](docs/models/container.md)
  - [Entry](docs/models/entry.md)
  - [Group](docs/models/group.md)
  - [Organizational Unit](docs/models/ou.md)
  - [Printer](docs/models/printer.md)
  - [RootDse](docs/models/root-dse.md)
  - [User](docs/models/user.md)

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
