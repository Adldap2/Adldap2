# Object Reference

All Adldap results will return an array of objects. These objects may vary depending on the type of LDAP object.

## Entry

All objects **extend** from the base Entry object. This means that the Entry methods are available on **all** objects
listed below.

#### getAttributes()

Returns the raw LDAP attributes of the current entry.

#### getName()

Returns the entry's `name` attribute.

#### getCommonName()

Returns the entry's `commonName` attribute.

#### getAccountName()

Returns the entry's `sAMaccountname` attribute.

#### getAccountType()

Returns the entry's `sAMaccounttype` attribute.

#### getCreatedAt()

Returns the entry's `whencreated` attribute.

#### getUpdatedAt()

Returns the entry's `whenchanged` attribute.

## User

## Group

## Computer

## Printer

## Container

## ExchangeServer

