# v9.0.0

## Changed

- Require PHP 7.0 - [8fa6df4](https://github.com/Adldap2/Adldap2/commit/8fa6df40fe6f76bfb0b1daf479fce1ab24afc21d)
- `Auth\Guard::bind()` no longer applies username `prefix` and `suffix`. This has been moved to the `attempt()` method - [c64705b](https://github.com/Adldap2/Adldap2/commit/c64705b393a0d42890ea0822d70936e843dcec1f)
- Binding as an administrator will now no longer use an account prefix or suffix. This prefix or suffix will need to be inserted into the username itself. [c64705b](https://github.com/Adldap2/Adldap2/commit/c64705b393a0d42890ea0822d70936e843dcec1f)
- Renamed `admin_username` and `admin_password` configuration options to `username` and `password` - [28f7d85](https://github.com/Adldap2/Adldap2/commit/28f7d85878e3bbbcbc2761a9d3a74df63b19a2c0)
- Renamed `domain_controllers` configuration option to `hosts` - [24bdc51](https://github.com/Adldap2/Adldap2/commit/24bdc51195cc7851e93d9650b824ccd829b5b75f)
- `Query\Factory::newGrammar()` is now protected for removal out of public interface, but maintain its ability to be overridden - [3f9db94](https://github.com/Adldap2/Adldap2/commit/3f9db94fc87bb36058b691ea0b1c03787190fc92)
- `Query\Factory::newQuery()` no longer accepts a distinguished name as a parameter. `setBaseDn()` must be used prior to `newQuery()` if developers would like to override the base [3f9db94](https://github.com/Adldap2/Adldap2/commit/3f9db94fc87bb36058b691ea0b1c03787190fc92)
- Renamed `Schemas\BaseSchema` to `Schemas\Schema` to follow naming convention - [b07493e](https://github.com/Adldap2/Adldap2/commit/b07493e666885c1aed4f5069f63943117f6ce504)

## Removed

- Removed prefix and suffix arguments from the `bind()` method - [c64705b](https://github.com/Adldap2/Adldap2/commit/c64705b393a0d42890ea0822d70936e843dcec1f)
- Removed `admin_account_prefix` and `admin_account_suffix` configuration options. This will need to be applied to the admin username itself by the developer - [28f7d85](https://github.com/Adldap2/Adldap2/commit/28f7d85878e3bbbcbc2761a9d3a74df63b19a2c0)
- Removed LDAP escape shim methods from Utilities class (no longer needed with PHP 7.0 requirement) - [43b4d88](https://github.com/Adldap2/Adldap2/commit/43b4d88047073b7305717f5d78ab4cb806731053)
- Removed `isSupported()`, `isSaslSupported()`, `isPagingSupported()`, and `isBatchSupported()` methods on `ConnectionInterface` - [07d470b](https://github.com/Adldap2/Adldap2/commit/07d470bef0924918079d9796932cb1f73acee1b3)
- Removed `Query\Factory::all()` method. This has been replaced by the `get()` method, as the `all()` method does not return `all()` results from an LDAP server that contains over 1000 records - [3f9db94](https://github.com/Adldap2/Adldap2/commit/3f9db94fc87bb36058b691ea0b1c03787190fc92)
- Removed `Query\Factory::setQuery()` method. This method has been replaced with a protected method `newBuilder()` for its ability to be overridden by developers. - [3f9db94](https://github.com/Adldap2/Adldap2/commit/3f9db94fc87bb36058b691ea0b1c03787190fc92)
- Removed `Query\Processor::map()` method. Use new `Schema::objectClassModelMap()` method instead - [beae260](https://github.com/Adldap2/Adldap2/commit/beae26096aabf13cf649a5261434c6a6414924dd)

## Added

- Anonymous binds are now easier by simply calling `bind()` without a username and password - [c64705b](https://github.com/Adldap2/Adldap2/commit/c64705b393a0d42890ea0822d70936e843dcec1f)
- Allow models to build their own DNs upon creation - [375d0bf](https://github.com/Adldap2/Adldap2/commit/375d0bf0b1979cd0a9abe92ad004340276aea963) - [#501](https://github.com/Adldap2/Adldap2/issues/501)
- Added `memberIdentifier()` schema method for additional support for LDAP variants - [7879d86](https://github.com/Adldap2/Adldap2/commit/7879d86f390294c4fac13b12da170abec3c0a456) - [#493](https://github.com/Adldap2/Adldap2/issues/493)
- Added `Query\Factory::setBaseDn()` method to override configured base DN for the search factory - [3f9db94](https://github.com/Adldap2/Adldap2/commit/3f9db94fc87bb36058b691ea0b1c03787190fc92)
- Added `Adldap\Models\User::getUserParamters()` and `setUserParameters()` [977b312](https://github.com/Adldap2/Adldap2/commit/977b3120f22cfc6e4b550d2e085505d2f75ade4f) - Thanks to @eltharin
- Added `Adldap\Models\Attributes\MbString` utility class for quickly detecting encoding - [977b312](https://github.com/Adldap2/Adldap2/commit/977b3120f22cfc6e4b550d2e085505d2f75ade4f), [3c588f1](https://github.com/Adldap2/Adldap2/commit/3c588f111b05f5aa0e3927c97be198d2d712cc60) - Thanks to @eltharin
- Models can now be passed directly into `Query\Factory::setDn()` and `in()` methods - [c1f9c25](https://github.com/Adldap2/Adldap2/commit/c1f9c25b251aa900c266aaa90280f8f30d4631c3)
- Added `objectClassModelMap()` into the `Schemas\Schema` for ability to insert new models - [beae260](https://github.com/Adldap2/Adldap2/commit/beae26096aabf13cf649a5261434c6a6414924dd) - [#518](https://github.com/Adldap2/Adldap2/issues/518)
- Added `Models\User::getEmployeeType()` and `setEmployeeType()` - [fdad5d0](https://github.com/Adldap2/Adldap2/commit/fdad5d0adb32552d77a4e08410a07f0a18be12e8) - [#524](https://github.com/Adldap2/Adldap2/issues/524)
- Added `Models\User::setClearLockoutTime()` for unlocking ActiveDirectory accounts - [10bde32](https://github.com/Adldap2/Adldap2/commit/10bde32bb2bea34e0a20dae17b2727db9ca70c5e) - [#558](https://github.com/Adldap2/Adldap2/issues/558)
- Added `Schemas\Schema::managedBy()` - [5ffd423](https://github.com/Adldap2/Adldap2/commit/5ffd4232cd547e97fda8ce0643a8d0d9f85d1665)
- Added `Models\User::getManagedBy()`, `getManagedByUser()` and `setManagedBy()` methods - [2c29144](https://github.com/Adldap2/Adldap2/commit/2c29144ab8ba5b54e20ea2eab672116301549e10), [64825a9](https://github.com/Adldap2/Adldap2/commit/64825a94404eb9a9201c8d58b02d5dbba0860187)
- Added `Connections\DetailedError` class for retrieving detailed error information - [6261a30](https://github.com/Adldap2/Adldap2/commit/6261a30de200ff5dbcd30f9a845d25af5eebdd0a) - [#565](https://github.com/Adldap2/Adldap2/pull/565) - Thanks to @datatim

## Fixed

- Searches now properly retain their base distinguished names when locating models by a distinguished name - [53c99c2](https://github.com/Adldap2/Adldap2/commit/53c99c253e851729f5fb2c52cd4b692764ae3e9a) - [#493](https://github.com/Adldap2/Adldap2/issues/493)
- Create a fresh query instance when constructing models so query params are reset - [e931a7e](https://github.com/Adldap2/Adldap2/commit/e931a7e89775bf4ec77fd3bcba7a6c1f82330f16)
- Properly determine if a users password has expired when using `ActiveDirectory` - [3d065fb](https://github.com/Adldap2/Adldap2/commit/3d065fb94807f4cbbd70d5af7853de13f847c0ea) - [#530](https://github.com/Adldap2/Adldap2/issues/530)
- Determine if a users thumbnail is already base64 encoded before encoding - [313290c](https://github.com/Adldap2/Adldap2/commit/313290cf617d04a4f91d10695edf82562f65a7fc), [0a0fda4](https://github.com/Adldap2/Adldap2/commit/0a0fda482f956bd28329580f2907b2b533a2548a) - Thanks to @phanos
- Fixed `users()` scope returning Contacts instead of just Users - [c5e4b5c](https://github.com/Adldap2/Adldap2/commit/c5e4b5c4007bcd2b43f2c723b145bf1e8e681e9b) - [#517](https://github.com/Adldap2/Adldap2/issues/517)
- Fixed `Schemas\Schema::objectClassUser()` method not being used when constructing new `Models\User`'s - [41954d8](https://github.com/Adldap2/Adldap2/commit/41954d8bb6d12765148e0e5fca83c0cb304d6552) - [#523](https://github.com/Adldap2/Adldap2/issues/523)

# Released

For previous version change logs, please view the [GitHub Releases](https://github.com/Adldap2/Adldap2/releases) page.
