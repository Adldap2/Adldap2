<?php

namespace Adldap\Contracts\Schemas;

interface SchemaInterface
{
    /**
     * The date when the account expires. This value represents the number of 100-nanosecond
     * intervals since January 1, 1601 (UTC). A value of 0 or 0x7FFFFFFFFFFFFFFF
     * (9223372036854775807) indicates that the account never expires.
     *
     * @return string
     */
    public function accountExpires();

    /**
     * The logon name used to support clients and servers running earlier versions of the
     * operating system, such as Windows NT 4.0, Windows 95, Windows 98,
     * and LAN Manager. This attribute must be 20 characters or
     * less to support earlier clients.
     *
     * @return string
     */
    public function accountName();

    /**
     * This attribute contains information about every account type object.
     *
     * @return string
     */
    public function accountType();

    /**
     * The name to be displayed on admin screens.
     *
     * @return string
     */
    public function adminDisplayName();

    /**
     * Ambiguous name resolution attribute to be used when choosing between objects.
     *
     * @return string
     */
    public function anr();

    /**
     * The number of times the user tried to log on to the account using
     * an incorrect password. A value of 0 indicates that the
     * value is unknown.
     *
     * @return string
     */
    public function badPasswordCount();

    /**
     * The last time and date that an attempt to log on to this
     * account was made with a password that is not valid.
     *
     * @return string
     */
    public function badPasswordTime();

    /**
     * The name that represents an object.
     *
     * @return string
     */
    public function commonName();

    /**
     * The user's company name.
     *
     * @return string
     */
    public function company();

    /**
     * @return string
     */
    public function computer();

    /**
     * @return string
     */
    public function configurationNamingContext();

    /**
     * @return string
     */
    public function contact();

    /**
     * @return string
     */
    public function country();

    /**
     * @return string
     */
    public function createdAt();

    /**
     * @return string
     */
    public function defaultNamingContext();

    /**
     * @return string
     */
    public function department();

    /**
     * @return string
     */
    public function description();

    /**
     * @return string
     */
    public function displayName();

    /**
     * @return string
     */
    public function distinguishedName();

    /**
     * @return string
     */
    public function dnsHostName();

    /**
     * @return string
     */
    public function domainComponent();

    /**
     * @return string
     */
    public function driverName();

    /**
     * @return string
     */
    public function driverVersion();

    /**
     * @return string
     */
    public function email();

    /**
     * @return string
     */
    public function emailNickname();

    /**
     * @return string
     */
    public function employeeId();

    /**
     * @return string
     */
    public function employeeNumber();

    public function employeeType();

    /**
     * @return string
     */
    public function false();

    /**
     * @return string
     */
    public function firstName();

    /**
     * @return string
     */
    public function groupType();

    /**
     * @return string
     */
    public function homeAddress();

    /**
     * @return string
     */
    public function homeMdb();

    /**
     * @return string
     */
    public function info();

    /**
     * @return string
     */
    public function initials();

    /**
     * @return string
     */
    public function instanceType();

    /**
     * @return string
     */
    public function isCriticalSystemObject();

    /**
     * @return string
     */
    public function lastLogOff();

    /**
     * @return string
     */
    public function lastLogOn();

    /**
     * @return string
     */
    public function lastLogOnTimestamp();

    /**
     * @return string
     */
    public function lastName();

    /**
     * @return string
     */
    public function legacyExchangeDn();

    /**
     * @return string
     */
    public function locale();

    /**
     * @return string
     */
    public function location();

    /**
     * @return string
     */
    public function lockoutTime();

    /**
     * @return string
     */
    public function manager();

    /**
     * @return string
     */
    public function maxPasswordAge();

    /**
     * @return string
     */
    public function member();

    /**
     * @return string
     */
    public function memberOf();

    /**
     * @return string
     */
    public function messageTrackingEnabled();

    /**
     * @return string
     */
    public function msExchangeServer();

    /**
     * @return string
     */
    public function name();

    /**
     * @return string
     */
    public function objectCategory();

    /**
     * @return string
     */
    public function objectCategoryComputer();

    /**
     * @return string
     */
    public function objectCategoryContainer();

    /**
     * @return string
     */
    public function objectCategoryExchangePrivateMdb();

    /**
     * @return string
     */
    public function objectCategoryExchangeServer();

    /**
     * @return string
     */
    public function objectCategoryExchangeStorageGroup();

    /**
     * @return string
     */
    public function objectCategoryGroup();

    /**
     * @return string
     */
    public function objectCategoryOrganizationalUnit();

    /**
     * @return string
     */
    public function objectCategoryPerson();

    /**
     * @return string
     */
    public function objectCategoryPrinter();

    /**
     * @return string
     */
    public function objectClass();

    /**
     * @return string
     */
    public function objectClassPrinter();

    /**
     * @return string
     */
    public function objectGuid();

    /**
     * @return string
     */
    public function objectSid();

    /**
     * @return string
     */
    public function operatingSystem();

    /**
     * @return string
     */
    public function operatingSystemServicePack();

    /**
     * @return string
     */
    public function operatingSystemVersion();

    /**
     * @return string
     */
    public function organizationalPerson();

    /**
     * @return string
     */
    public function organizationalUnit();

    /**
     * @return string
     */
    public function organizationalUnitShort();

    /**
     * @return string
     */
    public function otherMailbox();

    /**
     * @return string
     */
    public function passwordLastSet();

    /**
     * @return string
     */
    public function person();

    /**
     * @return string
     */
    public function personalTitle();

    /**
     * @return string
     */
    public function physicalDeliveryOfficeName();

    /**
     * @return string
     */
    public function portName();

    /**
     * @return string
     */
    public function postalCode();

    /**
     * @return string
     */
    public function primaryGroupId();

    /**
     * @return string
     */
    public function printerBinNames();

    /**
     * @return string
     */
    public function printerColorSupported();

    /**
     * @return string
     */
    public function printerDuplexSupported();

    /**
     * @return string
     */
    public function printerEndTime();

    /**
     * @return string
     */
    public function printerMaxResolutionSupported();

    /**
     * @return string
     */
    public function printerMediaSupported();

    /**
     * @return string
     */
    public function printerMemory();

    /**
     * @return string
     */
    public function printerName();

    /**
     * @return string
     */
    public function printerOrientationSupported();

    /**
     * @return string
     */
    public function printerPrintRate();

    /**
     * @return string
     */
    public function printerPrintRateUnit();

    /**
     * @return string
     */
    public function printerShareName();

    /**
     * @return string
     */
    public function printerStaplingSupported();

    /**
     * @return string
     */
    public function printerStartTime();

    /**
     * @return string
     */
    public function priority();

    /**
     * @return string
     */
    public function profilePath();

    /**
     * @return string
     */
    public function proxyAddresses();

    /**
     * @return string
     */
    public function scriptPath();

    /**
     * @return string
     */
    public function serialNumber();

    /**
     * @return string
     */
    public function serverName();

    /**
     * @return string
     */
    public function showInAddressBook();

    public function street();

    /**
     * @return string
     */
    public function streetAddress();

    /**
     * @return string
     */
    public function systemFlags();

    /**
     * @return string
     */
    public function telephone();

    /**
     * @return string
     */
    public function thumbnail();

    /**
     * @return string
     */
    public function title();

    public function top();

    /**
     * @return string
     */
    public function true();

    /**
     * @return string
     */
    public function unicodePassword();

    /**
     * @return string
     */
    public function updatedAt();

    /**
     * @return string
     */
    public function url();

    /**
     * @return string
     */
    public function user();

    /**
     * @return string
     */
    public function userAccountControl();

    /**
     * @return string
     */
    public function userPrincipalName();

    /**
     * @return string
     */
    public function versionNumber();
}
