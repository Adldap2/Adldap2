# The Computer Model

## Getting the operating system, version, & service pack
 
To get the computers operating system, call the `getOperatingSystem()` method:

```php
$computer = $provider->search()->computers()->find('ACME-EXCHANGE');

// Returns 'Windows Server 2003'
echo $computer->getOperatingSystem();
```

To get the computers operating system version, call the `getOperatingSystemVersion()` method:

```php
// Returns '5.2 (3790)';
echo $computer->getOperatingSystemVersion();
```

To get the computers operating system service pack, call the `getOperatingSystemServicePack()` method:

```php
// Returns 'Service Pack 1';
echo $computer->getOperatingSystemServicePack();
```

## Getting the DNS host name

To get the computers DNS host name, call the `getDnsHostName()` method:

```php
$computer = $provider->search()->computers()->find('ACME-DESKTOP001');

// Returns 'ACME-DESKTOP001.corp.acme.org'
$computer->getDnsHostName();
```

## Getting Log off / Log on times

```php
$computer->getLastLogOff();

$computer->getLastLogon();

$computer->getLastLogonTimestamp();
```
