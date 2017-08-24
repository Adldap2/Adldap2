# Unreleased

### Added

- Added configurable models via the Schema ([caf1750](https://github.com/Adldap2/Adldap2/commit/caf17505f6eac609e028cc7763da468c9c59ca6a))
- Added `isValidGuid()` method on `Utilities` ([562776a](https://github.com/Adldap2/Adldap2/commit/562776a4a0a63fcb52c9d963bc52e91bbe70c9b2))
- Added MemberOf Recursive filter to Schema ([5846270](https://github.com/Adldap2/Adldap2/commit/584627088893a221ec43a785d1aa00f5a367a50d))
- Added `notFilter()` method to query `Builder` ([#418](https://github.com/Adldap2/Adldap2/issues/418))-([8f4d969](https://github.com/Adldap2/Adldap2/commit/8f4d9698afda27d99dbf0dca0c4643f367dbd7d4)) 
- Added `homeDrive` and `homeDirectory` methods to `Schema` ([8966491](https://github.com/Adldap2/Adldap2/commit/8966491507376430409d0847160450ce595323cd),[b1fe3f9](https://github.com/Adldap2/Adldap2/commit/b1fe3f9e2bbedf0fc9113d62e707808153d7b2f2))
- Added `isValid()` method to `BatchModification` ([67db6d9](https://github.com/Adldap2/Adldap2/commit/67db6d973b6b5147e4391fac4f6be024e97e2753))
- Added `KEY_ATTRIB`, `KEY_MODTYPE` & `KEY_VALUES` constants to `BatchModification` ([1f29859](https://github.com/Adldap2/Adldap2/commit/1f2985912df61d7a11e5f196ecbcc1f460383758))
- Added `lockoutTime()`, `filterEnabled()` and `filterDisabled()` methods to `OpenLDAP` Schema ([96e0e1f](https://github.com/Adldap2/Adldap2/commit/96e0e1fd8298bcfdefb002d71abf4a4fb06b83a6))
- Added `Guid` and `Sid` attribute classes for converting GUIDs and SIDs to string & binary ([03f7074](https://github.com/Adldap2/Adldap2/commit/03f7074d56af95ad69a17f2f77ee238c708a1841))
- Added `inOu()` method to `Model` to check if a `Model` instance is inside a given OU ([fdc6b17](https://github.com/Adldap2/Adldap2/commit/fdc6b177c630993cb541a72ae4f79d4cb581e459))

### Changed

- GUIDs are converted to hex before searching if the Schema requires it ([de40105](https://github.com/Adldap2/Adldap2/commit/de401055abf1d4311f087ac1bae2ed0048fcdb75))
- GUIDs / SIDs are converted to strings only if the Schema requires it ([d89f105](https://github.com/Adldap2/Adldap2/commit/d89f105f0335fd77b48f467449aa559c4e9169af))
- An array can now be used in the `find()` method to search for multiple records ([06a25ee](https://github.com/Adldap2/Adldap2/commit/06a25ee5501aebf457c42099788a8cf3293b2e39))
- Rebinds as the administrator are now properly re-bound ([ac5e73b](https://github.com/Adldap2/Adldap2/commit/ac5e73bfee16bba83a16e73523c39935f390c4a3))
- Account prefix and suffix are now only applied if the given prefix and suffix are null ([#406](https://github.com/Adldap2/Adldap2/issues/406))-([7db8896](https://github.com/Adldap2/Adldap2/commit/7db8896ea69516a258bd07f5d8cea50683bc1da5))
- GUIDs and SIDs are now converted to their string equivalents upon serialization ([f43d4dc](https://github.com/Adldap2/Adldap2/commit/f43d4dc504b06013b549144f28f1b9d791610b38))
- Exception is always thrown when a batch modification is invalid ([b45bfef](https://github.com/Adldap2/Adldap2/commit/b45bfeff4c4e3ae9b91e6499ff0c88d675923a03))
- `Utilities::littleEndian()` static method has been removed ([57360b1](https://github.com/Adldap2/Adldap2/commit/57360b10dcf57ae013a3688cb168e637b58ba587))
- GUIDs are now used as the authentication identifier for Laravel ([9033159](https://github.com/Adldap2/Adldap2/commit/90331598169de6ec5446917fefb6334fa72e4a47))
- Removed `__destruct()` from the `ProviderInterface` ([a951f29](https://github.com/Adldap2/Adldap2/commit/a951f29ceefeb47431e2147f1251f1383cdd3bc9))
- `DistinguishedName` properties are now public ([9d18d7d](https://github.com/Adldap2/Adldap2/commit/9d18d7d96ac9f6c00770572c1c2ae4fe2f49aa06))
- Model traits are now contained in the `Concerns` namespace ([81759a5](https://github.com/Adldap2/Adldap2/commit/81759a5e3b32cf5c9c4fa9658ddf84a19f0daa9e))
- Moved attribute methods / properties into `Concerns` trait ([5be9ff4](https://github.com/Adldap2/Adldap2/commit/5be9ff439cc6f3f1cdb6d85e15c94d109747b437))

## Fixed

- Models are now serialized properly ([#430](https://github.com/Adldap2/Adldap2/issues/430))-([d207376](https://github.com/Adldap2/Adldap2/commit/d207376a004a5a83af33dc7584237fd6b5c57d6a))
