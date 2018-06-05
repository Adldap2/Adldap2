# Introduction

## What is Adldap2?

Adldap2 is a PHP LDAP package that allows you to:

1. Manage multiple LDAP connections at once
2. Perform authentication
3. Search your LDAP directory with a fluent query builder
4. Create / Update / Delete LDAP entities with ease
5. And more

## History of Adldap2

Adldap2 was originally created as a fork of the original LDAP library [adLDAP](https://github.com/adldap/adLDAP) due to bugs, and it being completely abandoned.

Adldap2 contains absolutely no similarities to the original repository, and was built to be as easily accessible as possible, with great documentation, and easily understandable syntax.

Much of the API was constructed with Ruby's ActiveRecord and Laravel's Eloquent in mind, and to be an answer to the question:

> _Why can't we use LDAP like we use a database?_

## Why should you use Adldap2?

Working with LDAP in PHP can be a messy and confusing endeavor, especially when using multiple connections, creating and managing entities, performing moves, resetting passwords, and performing ACL modifications to user accounts.

Wrapper classes for LDAP are usually always created in PHP applications.

Adldap2 allows you to easily manage the above problems without reinventing the wheel for every project.

## Getting Started

Ready to get started? Head over to the [installation guide](installation.md) and we'll get you up and running.