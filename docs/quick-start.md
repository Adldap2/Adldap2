## Quick Start

```php
// Construct new Adldap instance.
$ad = new \Adldap\Adldap();

// Create a configuration array.
$config = [
  // Your account suffix, for example: jdoe@corp.acme.org
  'account_suffix'        => '@corp.acme.org',
  
  // You can use the host name or the IP address of your controllers.
  'domain_controllers'    => ['ACME-DC01.corp.acme.org', '10.0.20.119'],
  
  // Your base DN. This is usually your account suffix.
  'base_dn'               => 'dc=corp,dc=acme,dc=org',
  
  // The account to use for querying / modifying users. This
  // does not need to be an actual admin account.
  'admin_username'        => 'admin',
  'admin_password'        => 'password',
];

// Create a new connection provider.
$provider = new \Adldap\Connections\Provider($config);

// Add the provider to Adldap.
$ad->addProvider('default', $provider);

try {
    // Connect using the providers alias name.
    $ad->connect('default');

    // Perform a query.
    $results = $provider->search()->where('cn', '=', 'John Doe')->get();
    
    // Find a user.
    $user = $provider->search()->find('jdoe');

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
