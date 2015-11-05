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

    /**
     * {@inheritdoc}
     */
    public function accountExpires()
    {
        return static::ACCOUNT_EXPIRES;
    }

    /**
     * {@inheritdoc}
     */
    public function accountName()
    {
        return static::ACCOUNT_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function accountType()
    {
        return static::ACCOUNT_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function adminDisplayName()
    {
        return static::ADMIN_DISPLAY_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function anr()
    {
        return static::ANR;
    }

    /**
     * {@inheritdoc}
     */
    public function badPasswordCount()
    {
        return static::BAD_PASSWORD_COUNT;
    }

    /**
     * {@inheritdoc}
     */
    public function badPasswordTime()
    {
        return static::BAD_PASSWORD_TIME;
    }

    /**
     * {@inheritdoc}
     */
    public function commonName()
    {
        return static::COMMON_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function company()
    {
        return static::COMPANY;
    }

    /**
     * {@inheritdoc}
     */
    public function computer()
    {
        return static::COMPUTER;
    }

    /**
     * {@inheritdoc}
     */
    public function configurationNamingContext()
    {
        return static::CONFIGURATION_NAMING_CONTEXT;
    }

    /**
     * {@inheritdoc}
     */
    public function contact()
    {
        return static::CONTACT;
    }

    /**
     * {@inheritdoc}
     */
    public function country()
    {
        return static::COUNTRY;
    }

    /**
     * {@inheritdoc}
     */
    public function createdAt()
    {
        return static::CREATED_AT;
    }

    /**
     * {@inheritdoc}
     */
    public function defaultNamingContext()
    {
        return static::DEFAULT_NAMING_CONTEXT;
    }

    /**
     * {@inheritdoc}
     */
    public function department()
    {
        return static::DEPARTMENT;
    }

    /**
     * {@inheritdoc}
     */
    public function description()
    {
        return static::DESCRIPTION;
    }

    /**
     * {@inheritdoc}
     */
    public function displayName()
    {
        return static::DISPLAY_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function distinguishedName()
    {
        return static::DISTINGUISHED_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function dnsHostName()
    {
        return static::DNS_HOST_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function domainComponent()
    {
        return static::DOMAIN_COMPONENT;
    }

    /**
     * {@inheritdoc}
     */
    public function driverName()
    {
        return static::DRIVER_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function driverVersion()
    {
        return static::DRIVER_VERSION;
    }

    /**
     * {@inheritdoc}
     */
    public function email()
    {
        return static::EMAIL;
    }

    /**
     * {@inheritdoc}
     */
    public function emailNickname()
    {
        return static::EMAIL_NICKNAME;
    }

    /**
     * {@inheritdoc}
     */
    public function employeeId()
    {
        return static::EMPLOYEE_ID;
    }

    /**
     * {@inheritdoc}
     */
    public function employeeNumber()
    {
        return static::EMPLOYEE_NUMBER;
    }

    /**
     * {@inheritdoc}
     */
    public function false()
    {
        return static::FALSE;
    }

    /**
     * {@inheritdoc}
     */
    public function firstName()
    {
        return static::FIRST_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function groupType()
    {
        return static::GROUP_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function homeAddress()
    {
        return static::ADDRESS_HOME;
    }

    /**
     * {@inheritdoc}
     */
    public function homeMdb()
    {
        return static::HOME_MDB;
    }

    /**
     * {@inheritdoc}
     */
    public function info()
    {
        return static::INFO;
    }

    /**
     * {@inheritdoc}
     */
    public function initials()
    {
        return static::INITIALS;
    }

    /**
     * {@inheritdoc}
     */
    public function instanceType()
    {
        return static::INSTANCE_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function isCriticalSystemObject()
    {
        return static::IS_CRITICAL_SYSTEM_OBJECT;
    }

    /**
     * {@inheritdoc}
     */
    public function lastLogOff()
    {
        return static::LAST_LOGOFF;
    }

    /**
     * {@inheritdoc}
     */
    public function lastLogOn()
    {
        return static::LAST_LOGON;
    }

    /**
     * {@inheritdoc}
     */
    public function lastLogOnTimestamp()
    {
        return static::LAST_LOGON_TIMESTAMP;
    }

    /**
     * {@inheritdoc}
     */
    public function lastName()
    {
        return static::LAST_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function legacyExchangeDn()
    {
        return static::LEGACY_EXCHANGE_DN;
    }

    /**
     * {@inheritdoc}
     */
    public function locale()
    {
        return static::LOCALE;
    }

    /**
     * {@inheritdoc}
     */
    public function location()
    {
        return static::LOCATION;
    }

    /**
     * {@inheritdoc}
     */
    public function lockoutTime()
    {
        return static::LOCKOUT_TIME;
    }

    /**
     * {@inheritdoc}
     */
    public function manager()
    {
        return static::MANAGER;
    }

    /**
     * {@inheritdoc}
     */
    public function maxPasswordAge()
    {
        return static::MAX_PASSWORD_AGE;
    }

    /**
     * {@inheritdoc}
     */
    public function member()
    {
        return static::MEMBER;
    }

    /**
     * {@inheritdoc}
     */
    public function memberOf()
    {
        return static::MEMBER_OF;
    }

    /**
     * {@inheritdoc}
     */
    public function messageTrackingEnabled()
    {
        return static::MESSAGE_TRACKING_ENABLED;
    }

    /**
     * {@inheritdoc}
     */
    public function msExchangeServer()
    {
        return static::MS_EXCHANGE_SERVER;
    }

    /**
     * {@inheritdoc}
     */
    public function name()
    {
        return static::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategory()
    {
        return static::OBJECT_CATEGORY;
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryComputer()
    {
        return static::OBJECT_CATEGORY_COMPUTER;
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryContainer()
    {
        return static::OBJECT_CATEGORY_CONTAINER;
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryExchangePrivateMdb()
    {
        return static::OBJECT_CATEGORY_EXCHANGE_PRIVATE_MDB;
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryExchangeServer()
    {
        return static::OBJECT_CATEGORY_EXCHANGE_SERVER;
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryExchangeStorageGroup()
    {
        return static::OBJECT_CATEGORY_EXCHANGE_STORAGE_GROUP;
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryGroup()
    {
        return static::OBJECT_CATEGORY_GROUP;
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryOrganizationalUnit()
    {
        return static::OBJECT_CATEGORY_ORGANIZATIONAL_UNIT;
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryPerson()
    {
        return static::OBJECT_CATEGORY_PERSON;
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryPrinter()
    {
        return static::OBJECT_CATEGORY_PRINTER;
    }

    /**
     * {@inheritdoc}
     */
    public function objectClass()
    {
        return static::OBJECT_CLASS;
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassPrinter()
    {
        return static::OBJECT_CLASS_PRINTER;
    }

    /**
     * {@inheritdoc}
     */
    public function objectGuid()
    {
        return static::OBJECT_GUID;
    }

    /**
     * {@inheritdoc}
     */
    public function objectSid()
    {
        return static::OBJECT_SID;
    }

    /**
     * {@inheritdoc}
     */
    public function operatingSystem()
    {
        return static::OPERATING_SYSTEM;
    }

    /**
     * {@inheritdoc}
     */
    public function operatingSystemServicePack()
    {
        return static::OPERATING_SYSTEM_SERVICE_PACK;
    }

    /**
     * {@inheritdoc}
     */
    public function operatingSystemVersion()
    {
        return static::OPERATING_SYSTEM_VERSION;
    }

    /**
     * {@inheritdoc}
     */
    public function organizationalPerson()
    {
        return static::ORGANIZATIONAL_PERSON;
    }

    /**
     * {@inheritdoc}
     */
    public function organizationalUnit()
    {
        return static::ORGANIZATIONAL_UNIT_LONG;
    }

    /**
     * {@inheritdoc}
     */
    public function organizationalUnitShort()
    {
        return static::ORGANIZATIONAL_UNIT_SHORT;
    }

    /**
     * {@inheritdoc}
     */
    public function otherMailbox()
    {
        return static::OTHER_MAILBOX;
    }

    /**
     * {@inheritdoc}
     */
    public function passwordLastSet()
    {
        return static::PASSWORD_LAST_SET;
    }

    /**
     * {@inheritdoc}
     */
    public function person()
    {
        return static::PERSON;
    }

    /**
     * {@inheritdoc}
     */
    public function personalTitle()
    {
        return static::PERSONAL_TITLE;
    }

    /**
     * {@inheritdoc}
     */
    public function physicalDeliveryOfficeName()
    {
        return static::PHYSICAL_DELIVERY_OFFICE_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function portName()
    {
        return static::PORT_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function postalCode()
    {
        return static::POSTAL_CODE;
    }

    /**
     * {@inheritdoc}
     */
    public function primaryGroupId()
    {
        return static::PRIMARY_GROUP_ID;
    }

    /**
     * {@inheritdoc}
     */
    public function printerBinNames()
    {
        return static::PRINTER_BIN_NAMES;
    }

    /**
     * {@inheritdoc}
     */
    public function printerColorSupported()
    {
        return static::PRINTER_COLOR_SUPPORTED;
    }

    /**
     * {@inheritdoc}
     */
    public function printerDuplexSupported()
    {
        return static::PRINTER_DUPLEX_SUPPORTED;
    }

    /**
     * {@inheritdoc}
     */
    public function printerEndTime()
    {
        return static::PRINTER_END_TIME;
    }

    /**
     * {@inheritdoc}
     */
    public function printerMaxResolutionSupported()
    {
        return static::PRINTER_MAX_RESOLUTION_SUPPORTED;
    }

    /**
     * {@inheritdoc}
     */
    public function printerMediaSupported()
    {
        return static::PRINTER_MEDIA_SUPPORTED;
    }

    /**
     * {@inheritdoc}
     */
    public function printerMemory()
    {
        return static::PRINTER_MEMORY;
    }

    /**
     * {@inheritdoc}
     */
    public function printerName()
    {
        return static::PRINTER_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function printerOrientationSupported()
    {
        return static::PRINTER_ORIENTATION_SUPPORTED;
    }

    /**
     * {@inheritdoc}
     */
    public function printerPrintRate()
    {
        return static::PRINTER_PRINT_RATE;
    }

    /**
     * {@inheritdoc}
     */
    public function printerPrintRateUnit()
    {
        return static::PRINTER_PRINT_RATE_UNIT;
    }

    /**
     * {@inheritdoc}
     */
    public function printerShareName()
    {
        return static::PRINTER_SHARE_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function printerStaplingSupported()
    {
        return static::PRINTER_STAPLING_SUPPORTED;
    }

    /**
     * {@inheritdoc}
     */
    public function printerStartTime()
    {
        return static::PRINTER_START_TIME;
    }

    /**
     * {@inheritdoc}
     */
    public function priority()
    {
        return static::PRIORITY;
    }

    /**
     * {@inheritdoc}
     */
    public function profilePath()
    {
        return static::PROFILE_PATH;
    }

    /**
     * {@inheritdoc}
     */
    public function proxyAddresses()
    {
        return static::PROXY_ADDRESSES;
    }

    /**
     * {@inheritdoc}
     */
    public function scriptPath()
    {
        return static::SCRIPT_PATH;
    }

    /**
     * {@inheritdoc}
     */
    public function serialNumber()
    {
        return static::SERIAL_NUMBER;
    }

    /**
     * {@inheritdoc}
     */
    public function serverName()
    {
        return static::SERVER_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function showInAddressBook()
    {
        return static::SHOW_IN_ADDRESS_BOOK;
    }

    /**
     * {@inheritdoc}
     */
    public function streetAddress()
    {
        return static::STREET_ADDRESS;
    }

    /**
     * {@inheritdoc}
     */
    public function systemFlags()
    {
        return static::SYSTEM_FLAGS;
    }

    /**
     * {@inheritdoc}
     */
    public function telephone()
    {
        return static::TELEPHONE;
    }

    /**
     * {@inheritdoc}
     */
    public function thumbnail()
    {
        return static::THUMBNAIL;
    }

    /**
     * {@inheritdoc}
     */
    public function title()
    {
        return static::TITLE;
    }

    /**
     * {@inheritdoc}
     */
    public function top()
    {
        return static::TOP;
    }

    /**
     * {@inheritdoc}
     */
    public function true()
    {
        return static::TRUE;
    }

    /**
     * {@inheritdoc}
     */
    public function unicodePassword()
    {
        return static::UNICODE_PASSWORD;
    }

    /**
     * {@inheritdoc}
     */
    public function updatedAt()
    {
        return static::UPDATED_AT;
    }

    /**
     * {@inheritdoc}
     */
    public function url()
    {
        return static::URL;
    }

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        return static::USER;
    }

    /**
     * {@inheritdoc}
     */
    public function userAccountControl()
    {
        return static::USER_ACCOUNT_CONTROL;
    }

    /**
     * {@inheritdoc}
     */
    public function userPrincipalName()
    {
        return static::USER_PRINCIPAL_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function versionNumber()
    {
        return static::VERSION_NUMBER;
    }
}
