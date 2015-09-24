## Exchange Server Model

The Computer model will be returned when an LDAP entry contains an object category: `ms-exch-exchange-server`.

##### Getting the exchange servers `serialnumber` attribute:

```php
$es->getSerialNumber();
```
 
##### Getting the exchange servers `versionnumber` attribute:

```php
$es->getVersionNumber();
```

##### Getting the exchange servers `admindisplayname` attribute:

```php
$es->getAdminDisplayName();
```
    
##### Getting the exchange servers `messagetrackingenabled` attribute: 

```php    
$es->getMessageTrackingEnabled();
```
