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
        return self::ACCOUNT_EXPIRES;
    }

    /**
     * {@inheritdoc}
     */
    public function accountName()
    {
        return self::ACCOUNT_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function accountType()
    {
        return self::ACCOUNT_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function adminDisplayName()
    {
        return self::ADMIN_DISPLAY_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function anr()
    {
        return self::ANR;
    }

    /**
     * {@inheritdoc}
     */
    public function badPasswordCount()
    {
        return self::BAD_PASSWORD_COUNT;
    }

    /**
     * {@inheritdoc}
     */
    public function badPasswordTime()
    {
        return self::BAD_PASSWORD_TIME;
    }

    /**
     * {@inheritdoc}
     */
    public function commonName()
    {
        return self::COMMON_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function company()
    {
        return self::COMPANY;
    }

    /**
     * {@inheritdoc}
     */
    public function computer()
    {
        return self::COMPUTER;
    }

    /**
     * {@inheritdoc}
     */
    public function configurationNamingContext()
    {
        return self::CONFIGURATION_NAMING_CONTEXT;
    }

    /**
     * {@inheritdoc}
     */
    public function contact()
    {
        return self::CONTACT;
    }

    /**
     * {@inheritdoc}
     */
    public function country()
    {
        return self::COUNTRY;
    }

    /**
     * {@inheritdoc}
     */
    public function createdAt()
    {
        return self::CREATED_AT;
    }

    /**
     * {@inheritdoc}
     */
    public function defaultNamingContext()
    {
        return self::DEFAULT_NAMING_CONTEXT;
    }

    /**
     * {@inheritdoc}
     */
    public function department()
    {
        return self::DEPARTMENT;
    }

    /**
     * {@inheritdoc}
     */
    public function description()
    {
        return self::DESCRIPTION;
    }

    /**
     * {@inheritdoc}
     */
    public function displayName()
    {
        return self::DISPLAY_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function distinguishedName()
    {
        return self::DISTINGUISHED_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function dnsHostName()
    {
        return self::DNS_HOST_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function domainComponent()
    {
        return self::DOMAIN_COMPONENT;
    }

    /**
     * {@inheritdoc}
     */
    public function driverName()
    {
        return self::DRIVER_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function driverVersion()
    {
        return self::DRIVER_VERSION;
    }

    /**
     * {@inheritdoc}
     */
    public function email()
    {
        return self::EMAIL;
    }

    /**
     * {@inheritdoc}
     */
    public function emailNickname()
    {
        return self::EMAIL_NICKNAME;
    }

    /**
     * {@inheritdoc}
     */
    public function employeeId()
    {
        return self::EMPLOYEE_ID;
    }

    /**
     * {@inheritdoc}
     */
    public function employeeNumber()
    {
        return self::EMPLOYEE_NUMBER;
    }

    /**
     * {@inheritdoc}
     */
    public function false()
    {
        return self::FALSE;
    }

    /**
     * {@inheritdoc}
     */
    public function firstName()
    {
        return self::FIRST_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function groupType()
    {
        return self::GROUP_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function homeAddress()
    {
        return self::ADDRESS_HOME;
    }

    /**
     * {@inheritdoc}
     */
    public function homeMdb()
    {
        return self::HOME_MDB;
    }

    /**
     * {@inheritdoc}
     */
    public function info()
    {
        return self::INFO;
    }

    /**
     * {@inheritdoc}
     */
    public function initials()
    {
        return self::INITIALS;
    }

    /**
     * {@inheritdoc}
     */
    public function instanceType()
    {
        return self::INSTANCE_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function isCriticalSystemObject()
    {
        return self::IS_CRITICAL_SYSTEM_OBJECT;
    }

    /**
     * {@inheritdoc}
     */
    public function lastLogOff()
    {
        return self::LAST_LOGOFF;
    }

    /**
     * {@inheritdoc}
     */
    public function lastLogOn()
    {
        return self::LAST_LOGON;
    }

    /**
     * {@inheritdoc}
     */
    public function lastLogOnTimestamp()
    {
        return self::LAST_LOGON_TIMESTAMP;
    }

    /**
     * {@inheritdoc}
     */
    public function lastName()
    {
        return self::LAST_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function legacyExchangeDn()
    {
        return self::LEGACY_EXCHANGE_DN;
    }

    /**
     * {@inheritdoc}
     */
    public function locale()
    {
        return self::LOCALE;
    }

    /**
     * {@inheritdoc}
     */
    public function location()
    {
        return self::LOCATION;
    }

    /**
     * {@inheritdoc}
     */
    public function lockoutTime()
    {
        return self::LOCKOUT_TIME;
    }

    /**
     * {@inheritdoc}
     */
    public function manager()
    {
        return self::MANAGER;
    }

    /**
     * {@inheritdoc}
     */
    public function maxPasswordAge()
    {
        return self::MAX_PASSWORD_AGE;
    }

    /**
     * {@inheritdoc}
     */
    public function member()
    {
        return self::MEMBER;
    }

    /**
     * {@inheritdoc}
     */
    public function memberOf()
    {
        return self::MEMBER_OF;
    }

    /**
     * {@inheritdoc}
     */
    public function messageTrackingEnabled()
    {
        return self::MESSAGE_TRACKING_ENABLED;
    }

    /**
     * {@inheritdoc}
     */
    public function msExchangeServer()
    {
        return self::MS_EXCHANGE_SERVER;
    }

    /**
     * {@inheritdoc}
     */
    public function name()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategory()
    {
        return self::ANR;
    }

    /**
     * {@inheritdoc}
     */
    public function objectClass()
    {
        return self::OBJECT_CLASS;
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassPrinter()
    {
        return self::OBJECT_CLASS_PRINTER;
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryComputer()
    {
        return self::OBJECT_CATEGORY_COMPUTER;
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryContainer()
    {
        return self::OBJECT_CATEGORY_CONTAINER;
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryExchangePrivateMdb()
    {
        return self::OBJECT_CATEGORY_EXCHANGE_PRIVATE_MDB;
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryExchangeServer()
    {
        return self::OBJECT_CATEGORY_EXCHANGE_SERVER;
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryExchangeStorageGroup()
    {
        return self::OBJECT_CATEGORY_EXCHANGE_STORAGE_GROUP;
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryGroup()
    {
        return self::OBJECT_CATEGORY_GROUP;
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryOrganizationalUnit()
    {
        return self::OBJECT_CATEGORY_ORGANIZATIONAL_UNIT;
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryPerson()
    {
        return self::OBJECT_CATEGORY_PERSON;
    }

    /**
     * {@inheritdoc}
     */
    public function objectCategoryPrinter()
    {
        return self::OBJECT_CATEGORY_PRINTER;
    }

    /**
     * {@inheritdoc}
     */
    public function objectGuid()
    {
        return self::OBJECT_GUID;
    }

    /**
     * {@inheritdoc}
     */
    public function objectSid()
    {
        return self::OBJECT_SID;
    }

    /**
     * {@inheritdoc}
     */
    public function operatingSystem()
    {
        return self::OPERATING_SYSTEM;
    }

    /**
     * {@inheritdoc}
     */
    public function operatingSystemServicePack()
    {
        return self::OPERATING_SYSTEM_SERVICE_PACK;
    }

    /**
     * {@inheritdoc}
     */
    public function operatingSystemVersion()
    {
        return self::OPERATING_SYSTEM_VERSION;
    }

    /**
     * {@inheritdoc}
     */
    public function organizationalPerson()
    {
        return self::ORGANIZATIONAL_PERSON;
    }

    /**
     * {@inheritdoc}
     */
    public function organizationalUnit()
    {
        return self::ORGANIZATIONAL_UNIT_LONG;
    }

    /**
     * {@inheritdoc}
     */
    public function organizationalUnitShort()
    {
        return self::ORGANIZATIONAL_UNIT_SHORT;
    }

    /**
     * {@inheritdoc}
     */
    public function otherMailbox()
    {
        return self::OTHER_MAILBOX;
    }

    /**
     * {@inheritdoc}
     */
    public function passwordLastSet()
    {
        return self::PASSWORD_LAST_SET;
    }

    /**
     * {@inheritdoc}
     */
    public function person()
    {
        return self::PERSON;
    }

    /**
     * {@inheritdoc}
     */
    public function personalTitle()
    {
        return self::PERSONAL_TITLE;
    }

    /**
     * {@inheritdoc}
     */
    public function physicalDeliveryOfficeName()
    {
        return self::PHYSICAL_DELIVERY_OFFICE_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function portName()
    {
        return self::PORT_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function postalCode()
    {
        return self::POSTAL_CODE;
    }

    /**
     * {@inheritdoc}
     */
    public function primaryGroupId()
    {
        return self::PRIMARY_GROUP_ID;
    }

    /**
     * {@inheritdoc}
     */
    public function printerBinNames()
    {
        return self::PRINTER_BIN_NAMES;
    }

    /**
     * {@inheritdoc}
     */
    public function printerColorSupported()
    {
        return self::PRINTER_COLOR_SUPPORTED;
    }

    /**
     * {@inheritdoc}
     */
    public function printerDuplexSupported()
    {
        return self::PRINTER_DUPLEX_SUPPORTED;
    }

    /**
     * {@inheritdoc}
     */
    public function printerEndTime()
    {
        return self::PRINTER_END_TIME;
    }

    /**
     * {@inheritdoc}
     */
    public function printerMaxResolutionSupported()
    {
        return self::PRINTER_MAX_RESOLUTION_SUPPORTED;
    }

    /**
     * {@inheritdoc}
     */
    public function printerMediaSupported()
    {
        return self::PRINTER_MEDIA_SUPPORTED;
    }

    /**
     * {@inheritdoc}
     */
    public function printerMemory()
    {
        return self::PRINTER_MEMORY;
    }

    /**
     * {@inheritdoc}
     */
    public function printerName()
    {
        return self::PRINTER_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function printerOrientationSupported()
    {
        return self::PRINTER_ORIENTATION_SUPPORTED;
    }

    /**
     * {@inheritdoc}
     */
    public function printerPrintRate()
    {
        return self::PRINTER_PRINT_RATE;
    }

    /**
     * {@inheritdoc}
     */
    public function printerPrintRateUnit()
    {
        return self::PRINTER_PRINT_RATE_UNIT;
    }

    /**
     * {@inheritdoc}
     */
    public function printerShareName()
    {
        return self::PRINTER_SHARE_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function printerStaplingSupported()
    {
        return self::PRINTER_STAPLING_SUPPORTED;
    }

    /**
     * {@inheritdoc}
     */
    public function printerStartTime()
    {
        return self::PRINTER_START_TIME;
    }

    /**
     * {@inheritdoc}
     */
    public function priority()
    {
        return self::PRIORITY;
    }

    /**
     * {@inheritdoc}
     */
    public function profilePath()
    {
        return self::PROFILE_PATH;
    }

    /**
     * {@inheritdoc}
     */
    public function proxyAddresses()
    {
        return self::PROXY_ADDRESSES;
    }

    /**
     * {@inheritdoc}
     */
    public function scriptPath()
    {
        return self::SCRIPT_PATH;
    }

    /**
     * {@inheritdoc}
     */
    public function serialNumber()
    {
        return self::SERIAL_NUMBER;
    }

    /**
     * {@inheritdoc}
     */
    public function serverName()
    {
        return self::SERVER_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function showInAddressBook()
    {
        return self::SHOW_IN_ADDRESS_BOOK;
    }

    /**
     * {@inheritdoc}
     */
    public function streetAddress()
    {
        return self::STREET_ADDRESS;
    }

    /**
     * {@inheritdoc}
     */
    public function systemFlags()
    {
        return self::SYSTEM_FLAGS;
    }

    /**
     * {@inheritdoc}
     */
    public function telephone()
    {
        return self::TELEPHONE;
    }

    /**
     * {@inheritdoc}
     */
    public function thumbnail()
    {
        return self::THUMBNAIL;
    }

    /**
     * {@inheritdoc}
     */
    public function title()
    {
        return self::TITLE;
    }

    /**
     * {@inheritdoc}
     */
    public function true()
    {
        return self::TRUE;
    }

    /**
     * {@inheritdoc}
     */
    public function unicodePassword()
    {
        return self::UNICODE_PASSWORD;
    }

    /**
     * {@inheritdoc}
     */
    public function updatedAt()
    {
        return self::UPDATED_AT;
    }

    /**
     * {@inheritdoc}
     */
    public function url()
    {
        return self::URL;
    }

    /**
     * {@inheritdoc}
     */
    public function userAccountControl()
    {
        return self::USER_ACCOUNT_CONTROL;
    }

    /**
     * {@inheritdoc}
     */
    public function userPrincipalName()
    {
        return self::USER_PRINCIPAL_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function versionNumber()
    {
        return self::VERSION_NUMBER;
    }
}
