# Schemas (OpenLDAP, FreeIPA etc.)

Adldap2 implements different "Schemas" for each LDAP distribution type.

A *Schema* in Adldap2, is simply an interface that maps methods with your LDAP attribute names.

This means that if you have a different LDAP server than Active Directory, you must use a different schema.

Here are all of the schemas built in to Adldap2:

```php
Adldap\Schemas\FreeIPA;
Adldap\Schemas\OpenLDAP;
Adldap\Schemas\ActiveDirectory;
```

## Creating Your Own Schema

Adldap2 comes with an `Adldap\Schemas\BaseSchema` schema by default, which implements `Adldap\Schemas\SchemaInterface`.

You can either extend from the `BaseSchema` schema, or create your own and implement the `SchemaInterface`.

Please browse the [Schema Interface](/src/Schemas/SchemaInterface.php) to view all of the schema methods.

Your Schema:

```php
namespace App\Schemas;

use Adldap\Schemas\BaseSchema;

class MySchema extends BaseSchema
{
    /**
     * {@inheritdoc}
     */
    public function objectCategory()
    {
        return 'objectcategory';
    }
}
```

Injecting your custom schema:

```php
// Your configuration array.
$config = ['...'];

// New up your custom schema.
$mySchema = new \App\Schema\MySchema();

// Create a new connection provider, and inject your schema.
$provider = new \Adldap\Connections\Provider($config, $connection = null, $mySchema);

// Add the provider to your Adldap instance.
$adldap->addProvider($provider, $name = 'default');

// Connect to your provider.
$adldap->connect('default');
```
