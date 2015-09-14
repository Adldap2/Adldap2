<?php

namespace Adldap\Schemas;

interface SchemaInterface
{
    public function accountExpires();

    public function accountName();

    public function accountType();

    public function homeAddress();

    public function adminDisplayName();

    public function anr();

    public function badPasswordCount();

    public function badPasswordTime();

    public function commonName();

    public function company();

    public function computer();

    public function configurationNamingContext();

    public function contact();

    public function country();

    public function createdAt();

    public function defaultNamingContext();

    public function department();

    public function description();

    public function displayName();

    public function distinguishedName();

    public function dnsHostName();

    public function domainComponent();

    public function driverName();

    public function driverVersion();

    public function email();

    public function emailNickname();

    public function employeeId();

    public function employeeNumber();

    public function false();

    public function groupType();

    public function homeMdb();

    public function initials();

    public function instanceType();

    public function isCriticalSystemObject();

    public function lastLogOff();

    public function lastLogOn();

    public function lastLogOnTimestamp();

    public function lastName();

    public function legacyExchangeDn();

    public function locale();

    public function location();

    public function lockoutTime();

    public function maxPasswordAge();

    public function member();

    public function memberOf();

    public function messageTrackingEnabled();

    public function msExchangeServer();

    public function name();

    public function objectCategory();

    public function objectClass();

    public function objectClassPrinter();

    public function objectGuid();

    public function objectSid();

    public function operatingSystem();

    public function operatingSystemServicePack();

    public function operatingSystemVersion();

    public function organizationalPerson();

    public function organizationalUnit();

    public function organizationalUnitShort();

    public function passwordLastSet();

    public function person();

    public function physicalDeliveryOfficeName();

    public function portName();

    public function postalCode();

    public function primaryGroupId();

    public function printerBinNames();

    public function printerColorSupported();

    public function printerDuplexSupported();

    public function printerEndTime();

    public function printerMaxResolutionSupported();

    public function printerMediaSupported();

    public function printerMemory();

    public function printerName();

    public function printerOrientationSupported();

    public function printerPrintRate();
}
