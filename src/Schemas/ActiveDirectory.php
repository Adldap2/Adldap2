<?php

namespace Adldap\Schemas;

/**
 * Class ActiveDirectory
 *
 * The active directory attribute schema for easy auto completion retrieval.
 */
class ActiveDirectory
{
    const ACCOUNT_EXPIRES = 'accountexpires';
    const ACCOUNT_NAME = 'samaccountname';
    const ACCOUNT_TYPE = 'samaccounttype';
    const ADDRESS_HOME = 'homepostaladdress';
    const ANR = 'anr';
    const BAD_PASSWORD_COUNT = 'badpwdcount';
    const BAD_PASSWORD_TIME = 'badpasswordtime';
    const COMMON_NAME = 'cn';
    const COMPANY = 'company';
    const CREATED_AT = 'whencreated';
    const DEPARTMENT = 'department';
    const DESCRIPTION = 'description';
    const DISPLAY_NAME = 'displayname';
    const DISTINGUISHED_NAME = 'dn';
    const DNS_HOST_NAME = 'dnshostname';
    const EMAIL = 'mail';
    const EMAIL_NICKNAME = 'mailnickname';
    const EMPLOYEE_ID = 'employeeid';
    const EMPLOYEE_NUMBER = 'employeenumber';
    const EMPLOYEE_TYPE = 'employeetype';
    const FALSE = 'FALSE';
    const FIRST_NAME = 'givenname';
    const GROUP_TYPE = 'grouptype';
    const HOME_MDB = 'homemdb';
    const INITIALS = 'initials';
    const INSTANCE_TYPE = 'instancetype';
    const IS_CRITICAL_SYSTEM_OBJECT = 'iscriticalsystemobject';
    const LAST_LOGOFF = 'lastlogoff';
    const LAST_LOGON = 'lastlogon';
    const LAST_LOGON_TIMESTAMP = 'lastlogontimestamp';
    const LAST_NAME = 'sn';
    const LEGACY_EXCHANGE_DN = 'legacyexchangedn';
    const LOCKOUT_TIME = 'lockouttime';
    const MEMBER = 'member';
    const MEMBER_OF = 'memberof';
    const NAME = 'name';
    const OBJECT_CLASS = 'objectclass';
    const OBJECT_SID = 'objectsid';
    const OPERATING_SYSTEM = 'operatingsystem';
    const OPERATING_SYSTEM_SERVICE_PACK = 'operatingsystemservicepack';
    const OPERATING_SYSTEM_VERSION = 'operatingsystemversion';
    const POSTAL_CODE = 'postalcode';
    const PRIMARY_GROUP_ID = 'primarygroupid';
    const PROFILE_PATH = 'profilepath';
    const PROXY_ADDRESSES = 'proxyaddresses';
    const SCRIPT_PATH = 'scriptpath';
    const SHOW_IN_ADDRESS_BOOK = 'showinaddressbook';
    const STREET = 'street';
    const SYSTEM_FLAGS = 'systemflags';
    const TELEPHONE = 'telephonenumber';
    const TITLE = 'title';
    const TRUE = 'TRUE';
    const UPDATED_AT  = 'whenchanged';
    const USER_ACCOUNT_CONTROL = 'useraccountcontrol';
    const USER_PRINCIPAL_NAME = 'userprincipalname';
    const PRINTER_NAME = 'printername';
    const PRINTER_COLOR_SUPPORTED = 'printcolor';
    const PRINTER_DUPLEX_SUPPORTED = 'printduplexsupported';
    const PRINTER_STAPLING_SUPPORTED = 'printstaplingsupported';
    const PRINTER_MEDIA_SUPPORTED = 'printmediasupported';
    const PRINTER_BIN_NAMES = 'printbinnames';
    const PRINTER_SHARE_NAME = 'printsharename';
    const PRINTER_MEMORY = 'printmemory';
    const URL = 'url';
    const LOCATION = 'location';
    const SERVER_NAME = 'servername';
}
