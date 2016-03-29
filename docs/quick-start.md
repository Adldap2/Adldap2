## Quick Start & and Testing an LDAP connection

If you need to test something with access to an LDAP server, the generous folks at [Georgia Tech](http://drupal.gatech.edu/handbook/public-ldap-server) have you covered.

Use the following configuration:

```php
$config = [
  'account_suffix'        => '@corp.acme.org,
  'domain_controllers'    => ['ACME-DC01.corp.acme.org'],
  'base_dn'               => 'dc=corp,dc=acme,dc=org',
  'admin_username'        => 'admin',
  'admin_password'        => 'password',
];

// Create a new connection provider.
$provider = new \Adldap\Connections\Provider($config);

$ad = new \Adldap\Adldap();

// Add the provider to Adldap.
$ad->addProvider('default', $provider);

// Try connecting to the provider.
try {
    $ad->connect('default');

    // Create a new search.
    $search = $provider->search();

    // Call query methods upon the search itself.
    $results = $search->where('...')->get();

    // Or create a new query object.
    $query = $search->newQuery();

    $results = $search->where('...')->get();

    // Create a new LDAP object.
    $user =  $provider->make()->user();

    $user->cn = 'John Doe';

    if ($user->save()) {
        // User was saved!
    }
} catch (\Adldap\Exceptions\Auth\BindException $e) {

    // There was an issue binding to the server.

} catch (\Adldap\Exceptions\ConnectionException $e) {

    // There was an issue connecting to the server.

}
```

However while useful for basic testing, the queryable data only includes user data, so if you're looking for testing with any other information
or functionality such as modification, you'll have to use your own server.
