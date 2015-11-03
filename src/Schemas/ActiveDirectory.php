<?php

namespace Adldap\Schemas;

/**
 * Class ActiveDirectory.
 *
 * The active directory attribute schema for easy auto completion retrieval.
 */
class ActiveDirectory implements SchemaInterface
{
    const ACCOUNT_EXPIRES = 'accountexpires';
    const ACCOUNT_NAME = 'samaccountname';
    const ACCOUNT_TYPE = 'samaccounttype';
    const ADDRESS_HOME = 'homepostaladdress';
    const ADMIN_DISPLAY_NAME = 'admindisplayname';
    const ANR = 'anr';
    const BAD_PASSWORD_COUNT = 'badpwdcount';
    const BAD_PASSWORD_TIME = 'badpasswordtime';
    const COMMON_NAME = 'cn';
    const COMPANY = 'company';
    const COMPUTER = 'computer';
    const CONFIGURATION_NAMING_CONTEXT = 'configurationnamingcontext';
    const CONTACT = 'contact';
    const COUNTRY = 'c';
    const CREATED_AT = 'whencreated';
    const DEFAULT_NAMING_CONTEXT = 'defaultnamingcontext';
    const DEPARTMENT = 'department';
    const DESCRIPTION = 'description';
    const DISPLAY_NAME = 'displayname';
    const DISTINGUISHED_NAME = 'dn';
    const DNS_HOST_NAME = 'dnshostname';
    const DOMAIN_COMPONENT = 'dc';
    const DRIVER_NAME = 'drivername';
    const DRIVER_VERSION = 'driverversion';
    const EMAIL = 'mail';
    const EMAIL_NICKNAME = 'mailnickname';
    const EMPLOYEE_ID = 'employeeid';
    const EMPLOYEE_NUMBER = 'employeenumber';
    const EMPLOYEE_TYPE = 'employeetype';
    const FALSE = 'FALSE';
    const FIRST_NAME = 'givenname';
    const GROUP_TYPE = 'grouptype';
    const HOME_MDB = 'homemdb';
    const INFO = 'info';
    const INITIALS = 'initials';
    const INSTANCE_TYPE = 'instancetype';
    const IS_CRITICAL_SYSTEM_OBJECT = 'iscriticalsystemobject';
    const LAST_LOGOFF = 'lastlogoff';
    const LAST_LOGON = 'lastlogon';
    const LAST_LOGON_TIMESTAMP = 'lastlogontimestamp';
    const LAST_NAME = 'sn';
    const LEGACY_EXCHANGE_DN = 'legacyexchangedn';
    const LOCALE = 'l';
    const LOCATION = 'location';
    const LOCKOUT_TIME = 'lockouttime';
    const MANAGER = 'manager';
    const MAX_PASSWORD_AGE = 'maxpwdage';
    const MEMBER = 'member';
    const MEMBER_OF = 'memberof';
    const MESSAGE_TRACKING_ENABLED = 'messagetrackingenabled';
    const MS_EXCHANGE_SERVER = 'ms-exch-exchange-server';
    const NAME = 'name';
    const OBJECT_CATEGORY = 'objectcategory';
    const OBJECT_CATEGORY_COMPUTER = 'computer';
    const OBJECT_CATEGORY_CONTAINER = 'container';
    const OBJECT_CATEGORY_EXCHANGE_PRIVATE_MDB = 'msexchprivatemdb';
    const OBJECT_CATEGORY_EXCHANGE_SERVER = 'msExchExchangeServer';
    const OBJECT_CATEGORY_EXCHANGE_STORAGE_GROUP = 'msExchStorageGroup';
    const OBJECT_CATEGORY_GROUP = 'group';
    const OBJECT_CATEGORY_ORGANIZATIONAL_UNIT = 'organizational-unit';
    const OBJECT_CATEGORY_PERSON = 'person';
    const OBJECT_CATEGORY_PRINTER = 'print-queue';
    const OBJECT_CLASS = 'objectclass';
    const OBJECT_CLASS_PRINTER = 'printqueue';
    const OBJECT_GUID = 'objectguid';
    const OBJECT_SID = 'objectsid';
    const OPERATING_SYSTEM = 'operatingsystem';
    const OPERATING_SYSTEM_SERVICE_PACK = 'operatingsystemservicepack';
    const OPERATING_SYSTEM_VERSION = 'operatingsystemversion';
    const ORGANIZATIONAL_PERSON = 'organizationalperson';
    const ORGANIZATIONAL_UNIT_LONG = 'organizationalunit';
    const ORGANIZATIONAL_UNIT_SHORT = 'ou';
    const OTHER_MAILBOX = 'othermailbox';
    const PASSWORD_LAST_SET = 'pwdlastset';
    const PERSON = 'person';
    const PERSONAL_TITLE = 'personaltitle';
    const PHYSICAL_DELIVERY_OFFICE_NAME = 'physicaldeliveryofficename';
    const PORT_NAME = 'portname';
    const POSTAL_CODE = 'postalcode';
    const PRIMARY_GROUP_ID = 'primarygroupid';
    const PRINTER_BIN_NAMES = 'printbinnames';
    const PRINTER_COLOR_SUPPORTED = 'printcolor';
    const PRINTER_DUPLEX_SUPPORTED = 'printduplexsupported';
    const PRINTER_END_TIME = 'printendtime';
    const PRINTER_MAX_RESOLUTION_SUPPORTED = 'printmaxresolutionsupported';
    const PRINTER_MEDIA_SUPPORTED = 'printmediasupported';
    const PRINTER_MEMORY = 'printmemory';
    const PRINTER_NAME = 'printername';
    const PRINTER_ORIENTATION_SUPPORTED = 'printorientationssupported';
    const PRINTER_PRINT_RATE = 'printrate';
    const PRINTER_PRINT_RATE_UNIT = 'printrateunit';
    const PRINTER_SHARE_NAME = 'printsharename';
    const PRINTER_STAPLING_SUPPORTED = 'printstaplingsupported';
    const PRINTER_START_TIME = 'printstarttime';
    const PRIORITY = 'priority';
    const PROFILE_PATH = 'profilepath';
    const PROXY_ADDRESSES = 'proxyaddresses';
    const SCRIPT_PATH = 'scriptpath';
    const SERIAL_NUMBER = 'serialnumber';
    const SERVER_NAME = 'servername';
    const SHOW_IN_ADDRESS_BOOK = 'showinaddressbook';
    const STREET = 'street';
    const STREET_ADDRESS = 'streetaddress';
    const SYSTEM_FLAGS = 'systemflags';
    const TELEPHONE = 'telephonenumber';
    const THUMBNAIL = 'thumbnailphoto';
    const TITLE = 'title';
    const TOP = 'top';
    const TRUE = 'TRUE';
    const UNICODE_PASSWORD = 'unicodepwd';
    const UPDATED_AT = 'whenchanged';
    const URL = 'url';
    const USER = 'user';
    const USER_ACCOUNT_CONTROL = 'useraccountcontrol';
    const USER_PRINCIPAL_NAME = 'userprincipalname';
    const VERSION_NUMBER = 'versionnumber';

    public function accountExpires()
    {
        return self::ACCOUNT_EXPIRES;
    }

    public function accountName()
    {
        return self::ACCOUNT_NAME;
    }

    public function accountType()
    {
        return self::ACCOUNT_TYPE;
    }

    public function adminDisplayName()
    {
        return self::ADMIN_DISPLAY_NAME;
    }

    public function anr()
    {
        return self::ANR;
    }

    public function badPasswordCount()
    {
        return self::BAD_PASSWORD_COUNT;
    }

    public function badPasswordTime()
    {
        return self::BAD_PASSWORD_TIME;
    }

    public function commonName()
    {
        return self::COMMON_NAME;
    }

    public function company()
    {
        return self::COMPANY;
    }

    public function computer()
    {
        return self::COMPUTER;
    }

    public function configurationNamingContext()
    {
        return self::CONFIGURATION_NAMING_CONTEXT;
    }

    public function contact()
    {
        return self::CONTACT;
    }

    public function country()
    {
        return self::COUNTRY;
    }

    public function createdAt()
    {
        return self::CREATED_AT;
    }

    public function defaultNamingContext()
    {
        return self::DEFAULT_NAMING_CONTEXT;
    }

    public function department()
    {
        return self::DEPARTMENT;
    }

    public function description()
    {
        return self::DESCRIPTION;
    }

    public function displayName()
    {
        return self::DISPLAY_NAME;
    }

    public function distinguishedName()
    {
        return self::DISTINGUISHED_NAME;
    }

    public function dnsHostName()
    {
        return self::DNS_HOST_NAME;
    }

    public function domainComponent()
    {
        return self::DOMAIN_COMPONENT;
    }

    public function driverName()
    {
        return self::DRIVER_NAME;
    }

    public function driverVersion()
    {
        return self::DRIVER_VERSION;
    }

    public function email()
    {
        return self::EMAIL;
    }

    public function emailNickname()
    {
        return self::EMAIL_NICKNAME;
    }

    public function employeeId()
    {
        return self::EMPLOYEE_ID;
    }

    public function employeeNumber()
    {
        return self::EMPLOYEE_NUMBER;
    }

    public function false()
    {
        return self::FALSE;
    }

    public function firstName()
    {
        return self::FIRST_NAME;
    }

    public function groupType()
    {
        return self::GROUP_TYPE;
    }

    public function homeAddress()
    {
        return self::ADDRESS_HOME;
    }

    public function homeMdb()
    {
        return self::HOME_MDB;
    }

    public function info()
    {
        return self::INFO;
    }

    public function initials()
    {
        return self::INITIALS;
    }

    public function instanceType()
    {
        return self::INSTANCE_TYPE;
    }

    public function isCriticalSystemObject()
    {
        return self::IS_CRITICAL_SYSTEM_OBJECT;
    }

    public function lastLogOff()
    {
        return self::LAST_LOGOFF;
    }

    public function lastLogOn()
    {
        return self::LAST_LOGON;
    }

    public function lastLogOnTimestamp()
    {
        return self::LAST_LOGON_TIMESTAMP;
    }

    public function lastName()
    {
        return self::LAST_NAME;
    }

    public function legacyExchangeDn()
    {
        return self::LEGACY_EXCHANGE_DN;
    }

    public function locale()
    {
        return self::LOCALE;
    }

    public function location()
    {
        return self::LOCATION;
    }

    public function lockoutTime()
    {
        return self::LOCKOUT_TIME;
    }

    public function manager()
    {
        return self::MANAGER;
    }

    public function maxPasswordAge()
    {
        return self::MAX_PASSWORD_AGE;
    }

    public function member()
    {
        return self::MEMBER;
    }

    public function memberOf()
    {
        return self::MEMBER_OF;
    }

    public function messageTrackingEnabled()
    {
        return self::MESSAGE_TRACKING_ENABLED;
    }

    public function msExchangeServer()
    {
        return self::MS_EXCHANGE_SERVER;
    }

    public function name()
    {
        return self::NAME;
    }

    public function objectCategory()
    {
        return self::ANR;
    }

    public function objectClass()
    {
        return self::OBJECT_CLASS;
    }

    public function objectClassPrinter()
    {
        return self::OBJECT_CLASS_PRINTER;
    }

    public function objectGuid()
    {
        return self::OBJECT_GUID;
    }

    public function objectSid()
    {
        return self::OBJECT_SID;
    }

    public function operatingSystem()
    {
        return self::OPERATING_SYSTEM;
    }

    public function operatingSystemServicePack()
    {
        return self::OPERATING_SYSTEM_SERVICE_PACK;
    }

    public function operatingSystemVersion()
    {
        return self::OPERATING_SYSTEM_VERSION;
    }

    public function organizationalPerson()
    {
        return self::ORGANIZATIONAL_PERSON;
    }

    public function organizationalUnit()
    {
        return self::ORGANIZATIONAL_UNIT_LONG;
    }

    public function organizationalUnitShort()
    {
        return self::ORGANIZATIONAL_UNIT_SHORT;
    }

    public function otherMailbox()
    {
        return self::OTHER_MAILBOX;
    }

    public function passwordLastSet()
    {
        return self::PASSWORD_LAST_SET;
    }

    public function person()
    {
        return self::PERSON;
    }

    public function personalTitle()
    {
        return self::PERSONAL_TITLE;
    }

    public function physicalDeliveryOfficeName()
    {
        return self::PHYSICAL_DELIVERY_OFFICE_NAME;
    }

    public function portName()
    {
        return self::PORT_NAME;
    }

    public function postalCode()
    {
        return self::POSTAL_CODE;
    }

    public function primaryGroupId()
    {
        return self::PRIMARY_GROUP_ID;
    }

    public function printerBinNames()
    {
        return self::PRINTER_BIN_NAMES;
    }

    public function printerColorSupported()
    {
        return self::PRINTER_COLOR_SUPPORTED;
    }

    public function printerDuplexSupported()
    {
        return self::PRINTER_DUPLEX_SUPPORTED;
    }

    public function printerEndTime()
    {
        return self::PRINTER_END_TIME;
    }

    public function printerMaxResolutionSupported()
    {
        return self::PRINTER_MAX_RESOLUTION_SUPPORTED;
    }

    public function printerMediaSupported()
    {
        return self::PRINTER_MEDIA_SUPPORTED;
    }

    public function printerMemory()
    {
        return self::PRINTER_MEMORY;
    }

    public function printerName()
    {
        return self::PRINTER_NAME;
    }

    public function printerOrientationSupported()
    {
        return self::PRINTER_ORIENTATION_SUPPORTED;
    }

    public function printerPrintRate()
    {
        return self::PRINTER_PRINT_RATE;
    }

    public function printerPrintRateUnit()
    {
        return self::PRINTER_PRINT_RATE_UNIT;
    }

    public function printerShareName()
    {
        return self::PRINTER_SHARE_NAME;
    }

    public function printerStaplingSupported()
    {
        return self::PRINTER_STAPLING_SUPPORTED;
    }

    public function printerStartTime()
    {
        return self::PRINTER_START_TIME;
    }

    public function priority()
    {
        return self::PRIORITY;
    }

    public function profilePath()
    {
        return self::PROFILE_PATH;
    }

    public function proxyAddresses()
    {
        return self::PROXY_ADDRESSES;
    }

    public function scriptPath()
    {
        return self::SCRIPT_PATH;
    }

    public function serialNumber()
    {
        return self::SERIAL_NUMBER;
    }

    public function serverName()
    {
        return self::SERVER_NAME;
    }

    public function showInAddressBook()
    {
        return self::SHOW_IN_ADDRESS_BOOK;
    }

    public function streetAddress()
    {
        return self::STREET_ADDRESS;
    }

    public function systemFlags()
    {
        return self::SYSTEM_FLAGS;
    }

    public function telephone()
    {
        return self::TELEPHONE;
    }

    public function thumbnail()
    {
        return self::THUMBNAIL;
    }

    public function title()
    {
        return self::TITLE;
    }

    public function true()
    {
        return self::TRUE;
    }

    public function unicodePassword()
    {
        return self::UNICODE_PASSWORD;
    }

    public function updatedAt()
    {
        return self::UPDATED_AT;
    }

    public function url()
    {
        return self::URL;
    }

    public function userAccountControl()
    {
        return self::USER_ACCOUNT_CONTROL;
    }

    public function userPrincipalName()
    {
        return self::USER_PRINCIPAL_NAME;
    }

    public function versionNumber()
    {
        return self::VERSION_NUMBER;
    }
}
