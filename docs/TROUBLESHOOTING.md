# Troubleshooting

If you encounter any issues that aren't solved by looking at this troubleshooting guide, please [create an issue](https://github.com/Adldap2/Adldap2/issues/new).

### I can't connect!

If you can't connect, please double check your configuration. The most notable configuration options to check are:

###### Your Admin Credentials

While this is probably obvious, check your admin account on the server to make sure it isn't locked or that you're using the
correct password.

###### Admin Account Suffix

Your configured `admin_account_suffix` needs to be correct to be able to connect successfully.

Your account suffix will **usually** be your base DN, for example:

If my `base_dn` is `dc=corp,dc=acme,dc=org`, then my `admin_account_suffix` is `@corp.acme.org`.

###### SSL & TLS

If you've enabled SSL or TLS (not both), ensure the port you've entered is correct.

### I don't receive any results for anything!

Please verify that your `base_dn` in your configuration array is correct. All search results are based off this DN.

To find your `base_dn` using `Adldap`, call `$manager->getRootDse();`, for example:

```php
$config = ['...'];

$ad = new Adldap($config);

$manager = $ad->connect();

var_dump($manager->getRootDse());
```

Your base DN will be located inside the root DSE's `rootdomainnamingcontext` attribute.
