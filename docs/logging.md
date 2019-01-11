# Logging

Adldap2 includes an implementation of PSR's widely supported [Logger](https://github.com/php-fig/log) interface.

By default, all of Adldap2's [events](events.md) will call the logger you have set to utilize.

> **Note**: The included logger does not display or log anything to a file. You must implement your own implementation.

## Registering a Logger

To register a logger call `Adldap::setLogger()`. The logger must implement the `Psr\Log\LoggerInterface`.

>**Note**: Be sure to set the logger prior to creating a new `Adldap` instance. This
> ensures all events throughout the lifecycle of the request use your logger.

```php
use Adldap\Adldap;

Adldap::setLogger($myLogger);

$config = ['...'];

$ad = new Adldap();

$ad->addProvider($config);
```

## Logged Information

Here is a list of events that are logged along with the information included:

| Event | Logged |
|---|---|
| `Adldap\Auth\Events\Attempting` | LDAP (ldap://192.168.1.1:389) - Operation: Adldap\Auth\Events\Attempting - Username: CN=Steve Bauman,OU=Users,DC=corp,DC=acme,DC=org - Result: Success | 
| `Adldap\Auth\Events\Binding` | LDAP (ldap://192.168.1.1:389) - Operation: Adldap\Auth\Events\Binding - Username: CN=Steve Bauman,OU=Users,DC=corp,DC=acme,DC=org - Result: Success | 
| `Adldap\Auth\Events\Bound` | LDAP (ldap://192.168.1.1:389) - Operation: Adldap\Auth\Events\Bound - Username: CN=Steve Bauman,OU=Users,DC=corp,DC=acme,DC=org - Result: Success | 
| `Adldap\Auth\Events\Passed` | LDAP (ldap://192.168.1.1:389) - Operation: Adldap\Auth\Events\Passed - Username: CN=Steve Bauman,OU=Users,DC=corp,DC=acme,DC=org - Result: Success | 
| `Adldap\Auth\Events\Failed` | LDAP (ldap://192.168.1.1:389) - Operation: Adldap\Auth\Events\Failed - Username: CN=Steve Bauman,OU=Users,DC=corp,DC=acme,DC=org - Result: Invalid Credentials |
| | |
| `Adldap\Models\Events\Saving` | | 
| `Adldap\Models\Events\Saved` | | 
| `Adldap\Models\Events\Creating` | | 
| `Adldap\Models\Events\Created` | | 
| `Adldap\Models\Events\Updating` | | 
| `Adldap\Models\Events\Updated` | | 
| `Adldap\Models\Events\Deleting` | | 
| `Adldap\Models\Events\Deleted` | | 
