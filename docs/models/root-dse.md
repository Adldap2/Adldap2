# The RootDse Model

## Getting the Root DSE

To get the Root DSE of your AD server, call the `getRootDse()` method off a new search:

```php
$rootDse = $provider->search()->getRootDse();
```
