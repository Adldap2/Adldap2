# The RootDse Model

## Getting the Root DSE

To get the Root DSE of your AD server, call the `getRootDse()` method off a new search:

```php
$rootDse = $provider->search()->getRootDse();
```

## Getting the schema naming context

To get the Root DSE schema naming context, call the `getSchemaNamingContext()`:

```php
$rootDse = $provider->search()->getRootDse();

$context = $rootDse->getSchemaNamingContext();

// Returns 'cn=Schema,cn=Configuration,dc=corp,dc=acme,dc=org'
echo $context;
```
