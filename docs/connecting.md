# Connecting

After installation, you'll need to create a couple objects to start running operations on your active directory server.

First, we'll define our configuration array (outlined [here](configuration.md)):

```php
$config = ['...'];
```

We'll then create a new Adldap instance:

```php
$ad = new \Adldap\Adldap();
```

Then, we'll create a new Connection Provider, and pass in the configuration in the first parameter:

```php
$provider = new \Adldap\Connections\Provider($config);
```

Once we've created a provider, we can add it to an Adldap instance:

```php
$ad->addProvider('default', $provider);
```

You can pass in any string to name the provider however you see fit.
This allows you to manage and connect to multiple AD connections if necessary.

Now, we can try connecting to the provider. We want to wrap the connect method
in a try / catch block so we can catch connection failures if they happen to occur:

```php
try {
    $ad->connect('default');
    
    // Connection was successful.
    
    // We can now perform operations on the connection.
    $provider->search();

} catch (\Adldap\Exceptions\Auth\BindException $e) {
    die("Can't bind to LDAP server!");
}
```

All together:

```php
$config = ['...'];

$ad = new \Adldap\Adldap();

$provider = new \Adldap\Connections\Provider($config);

$ad->addProvider('default', $provider);

try {
    $ad->connect('default');
    
    // Connection was successful.
    
    // We can now perform operations on the connection.
    $provider->search();

} catch (\Adldap\Exceptions\Auth\BindException $e) {
    die("Can't bind to LDAP server!");
}
```

## Custom Connections

Whenever you don't supply a new provider with an object that's an instance of
`Adldap\Contracts\Connections\ConnectionInterface`, a default connection is created for you.

A connection object is a wrapper for PHP's LDAP calls. This allows you to tweak how
things are passed into these methods if needed.

To create a custom connection, you can either extend the default connection class
(`Adldap\Connections\Ldap`), or implement the `ConnectionInterface`.

For example:

```php
class CustomConnection extends \Adldap\Connections\Ldap
{
    public function connect($hostname = [], $port = '389')
    {
        // Perform an `ldap_connect()` your own way...
    }
}
```

Now that we have our own connection class, we can instantiate it and pass it to the provider:

```php
$config = ['...'];

$connection = new CustomConnection();

$provider = new \Adldap\Connections\Provider($config, $connection);
```

## Custom Schemas

Some AD installations differ and you may need to tweak what some attributes are. This is where the schema comes in.

By default, if no schema is passed into the third parameter of a provider instance, a default schema is created.

The schema must extend from an already existing schema, or implement `Adldap\Contracts\Schemas\SchemaInterface`.

Let's create a custom schema:

```php
class CustomSchema extends \Adldap\Schemas\ActiveDirectory
{
    public function email()
    {
        return 'mail';
    }
}
```

Now we'll put it all together:

```php
$config = ['...'];

$connection = new CustomConnection();

$schema = new CustomSchema();

$provider = new \Adldap\Connections\Provider($config, $connection, $schema);
```

Now our provider will utilize our custom schema and connection classes.
