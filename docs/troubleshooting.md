# Troubleshooting

#### Creating and Setting a Users Password

To set a users password when you've created a new one, you need to enable their account, **then** set their password.

For example:

```php
// Construct a new user instance.
$user = $provider->make()->user();

// Set the user profile details.
$user->setAccountName('jdoe');
$user->setFirstName('John');
$user->setLastName('Doe');
$user->setCompany('ACME');
$user->setEmail('jdoe@acme.com');

// Save the new user.
if ($user->save()) {
    // Enable the new user (using user account control).
    $user->setUserAccountControl(512);

    // Set new user password
    $user->setPassword('Password123');

    // Save the user.
    if($user->save()) {
        // The password was saved successfully.
    }
}
```

#### Retrieving All Records Inside a Group

To retrieve all records inside a particular group (including nested groups), use the `rawFilter()` method:

```php
// The `memberof:1.2.840.113556.1.4.1941:` string indicates
// that we want all nested group records as well.
$filter = '(memberof:1.2.840.113556.1.4.1941:=CN=MyGroup,DC=example,DC=com)';

$users = $provider->search()->rawFilter($filter)->get();
```
