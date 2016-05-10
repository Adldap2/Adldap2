## Quick Start

```php
// Create a configuration array.
$config = [
  'account_suffix'        => '@corp.acme.org',
  'domain_controllers'    => ['ACME-DC01.corp.acme.org'],
  'base_dn'               => 'dc=corp,dc=acme,dc=org',
  'admin_username'        => 'admin',
  'admin_password'        => 'password',
];

// Create a new connection provider.
$provider = new \Adldap\Connections\Provider($config);

// Construct new Adldap instance.
$ad = new \Adldap\Adldap();

// Add the provider to Adldap.
$ad->addProvider('default', $provider);

// Try connecting to the provider.
try {
    // Connect using the providers name.
    $ad->connect('default');

    // Create a new search.
    $search = $provider->search();

    // Call query methods upon the search itself.
    $results = $search->where('...')->get();

    // Or create a new query object.
    $query = $search->newQuery();
  
    // Perform a query.
    $results = $search->where('...')->get();

    // Create a new LDAP object.
    $user =  $provider->make()->user();

    // Set a model's attribute.
    $user->cn = 'John Doe';

    // Persist the changes to your AD server.
    if ($user->save()) {
        // User was saved!
    }
} catch (\Adldap\Exceptions\Auth\BindException $e) {

    // There was an issue binding / connecting to the server.

}
```
