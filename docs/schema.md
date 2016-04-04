# Schema

In Adldap2 `v6`, a new Schema class has been implemented. This means that if your active directory schema differs is some way for specific attributes,
you can customize them and those attribute names and it will persist throughout using Adldap2. The schema also provides a convenient way
of accessing Schema attributes. Let's get started.

Adldap2 comes with an `Adldap\Schemas\ActiveDirectory` schema by default, which implements `Adldap\Contracts\Schemas\SchemaInterface`.

You can either extend from the `ActiveDirectory` schema, or create your own and implement the `SchemaInterface`.

Please browse the [Schema Interface](/src/Contracts/Schemas/SchemaInterface.php) to view all of the schema methods.
