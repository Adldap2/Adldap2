## Quick Start

```php
// Construct new Adldap instance.
$ad = new \Adldap\Adldap();

// Create a configuration array.
$config = [
  'account_suffix'        => '@corp.acme.org',
  'domain_controllers'    => ['ACME-DC01.corp.acme.org', 'ACME-DC02.corp.acme.org'],
  'base_dn'               => 'dc=corp,dc=acme,dc=org',
  'admin_username'        => 'admin',
  'admin_password'        => 'password',
];

// Create a new connection provider.
$provider = new \Adldap\Connections\Provider($config);

// Add the provider to Adldap.
$ad->addProvider('default', $provider);

// Try connecting to the provider.
try {
    // Connect using the providers alias name.
    $ad->connect('default');

    // Perform a query.
    $results = $provider->where('cn', '=', 'John Doe')->get();

    // Create a new LDAP entry. You can pass in attributes into the make methods.
    $user =  $provider->make()->user([
        'cn'          => 'John Doe',
        'title'       => 'Accountant',
        'description' => 'User Account',
    ]);

    // Set a model's attribute.
    $user->cn = 'John Doe';

    // Save the changes to your LDAP server.
    if ($user->save()) {
        // User was saved!
    }
} catch (\Adldap\Exceptions\Auth\BindException $e) {

    // There was an issue binding / connecting to the server.

}
```
