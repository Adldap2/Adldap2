# Raw Ldapconnection

## Introduction

If you want to connect to the ldap server without models (old fashion way), and want to get back the data in a raw format
You can easily do that. If you call the `getConnection()` function of the `Adldap`, then you get the connetion class of the selected (or default) provider. The default connection class is  called `Ldap` (`src/Connections/Ldap.php`).

If you look inside there are a loads of ldap functions, what contains the original php functions.

So how can you run that methods?

Here it is:

```php

$rawconneter = Adldap::getConnection();
$result = $rawconnecter->search($basedn, "cn=appletree", $returningfields);

$result = $rawconnecter->add($dn, $entry);

$result = $rawconnecter->delete($dn);

// .. etc

```