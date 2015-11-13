<?php

namespace Adldap\Contracts\Schemas;

interface SchemaInterface
{
    /**
     * The date when the account expires. This value represents the number of 100-nanosecond
     * intervals since January 1, 1601 (UTC). A value of 0 or 0x7FFFFFFFFFFFFFFF
     * (9223372036854775807) indicates that the account never expires.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675098(v=vs.85).aspx
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
     * @link https://msdn.microsoft.com/en-us/library/ms679635(v=vs.85).aspx
     *
     * @return string
     */
    public function accountName();

    /**
     * This attribute contains information about every account type object.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679637(v=vs.85).aspx
     *
     * @return string
     */
    public function accountType();

    /**
     * The name to be displayed on admin screens.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675214(v=vs.85).aspx
     *
     * @return string
     */
    public function adminDisplayName();

    /**
     * Ambiguous name resolution attribute to be used when choosing between objects.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675223(v=vs.85).aspx
     *
     * @return string
     */
    public function anr();

    /**
     * The number of times the user tried to log on to the account using
     * an incorrect password. A value of 0 indicates that the
     * value is unknown.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675244(v=vs.85).aspx
     *
     * @return string
     */
    public function badPasswordCount();

    /**
     * The last time and date that an attempt to log on to this
     * account was made with a password that is not valid.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675243(v=vs.85).aspx
     *
     * @return string
     */
    public function badPasswordTime();

    /**
     * The name that represents an object.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675449(v=vs.85).aspx
     *
     * @return string
     */
    public function commonName();

    /**
     * The user's company name.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675457(v=vs.85).aspx
     *
     * @return string
     */
    public function company();

    /**
     * The object class computer string.
     *
     * Used when constructing new Computer models.
     *
     * @return string
     */
    public function computer();

    /**
     * DN enterprise configuration naming context.
     *
     * @link https://support.microsoft.com/en-us/kb/219005
     *
     * @return string
     */
    public function configurationNamingContext();

    /**
     * The object class contact string.
     *
     * Used when constructing new User models.
     *
     * @return string
     */
    public function contact();

    /**
     * The entry's country attribute.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675432(v=vs.85).aspx
     *
     * @return string
     */
    public function country();

    /**
     * The entry's created at attribute.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms680924(v=vs.85).aspx
     *
     * @return string
     */
    public function createdAt();

    /**
     * This is the default NC for a particular server.
     *
     * By default, the DN for the domain of which this directory server is a member.
     *
     * @link https://support.microsoft.com/en-us/kb/219005
     *
     * @return string
     */
    public function defaultNamingContext();

    /**
     * Contains the name for the department in which the user works.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675490(v=vs.85).aspx
     *
     * @return string
     */
    public function department();

    /**
     * Contains the description to display for an object. This value is restricted
     * as single-valued for backward compatibility in some cases but
     * is allowed to be multi-valued in others.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675492(v=vs.85).aspx
     *
     * @return string
     */
    public function description();

    /**
     * The display name for an object. This is usually the combination
     * of the users first name, middle initial, and last name.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675514(v=vs.85).aspx
     *
     * @return string
     */
    public function displayName();

    /**
     * The LDAP API references an LDAP object by its distinguished name (DN).
     *
     * A DN is a sequence of relative distinguished names (RDN) connected by commas.
     *
     * @link https://msdn.microsoft.com/en-us/library/aa366101(v=vs.85).aspx
     *
     * @return string
     */
    public function distinguishedName();

    /**
     * Name of computer as registered in DNS.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675524(v=vs.85).aspx
     *
     * @return string
     */
    public function dnsHostName();

    /**
     * Domain Component located inside an RDN.
     *
     * @link https://msdn.microsoft.com/en-us/library/aa366101(v=vs.85).aspx
     *
     * @return string
     */
    public function domainComponent();

    /**
     * The device driver name.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675652(v=vs.85).aspx
     *
     * @return string
     */
    public function driverName();

    /**
     * The Version number of device driver.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675653(v=vs.85).aspx
     *
     * @return string
     */
    public function driverVersion();

    /**
     * The list of email addresses for a contact.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676855(v=vs.85).aspx
     *
     * @return string
     */
    public function email();

    /**
     * The email nickname for the user.
     *
     * @return string
     */
    public function emailNickname();

    /**
     * The ID of an employee.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675662(v=vs.85).aspx
     *
     * @return string
     */
    public function employeeId();

    /**
     * The number assigned to an employee other than the ID.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675663(v=vs.85).aspx
     *
     * @return string
     */
    public function employeeNumber();

    /**
     * The job category for an employee.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675664(v=vs.85).aspx
     *
     * @return string
     */
    public function employeeType();

    /**
     * The AD false bool in string form for conversion.
     *
     * @return string
     */
    public function false();

    /**
     * Contains the given name (first name) of the user.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675719(v=vs.85).aspx
     *
     * @return string
     */
    public function firstName();

    /**
     * Contains a set of flags that define the type and scope of a group object.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms675935(v=vs.85).aspx
     *
     * @return string
     */
    public function groupType();

    /**
     * A user's home address.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676193(v=vs.85).aspx
     *
     * @return string
     */
    public function homeAddress();

    /**
     * The users mailbox database location.
     *
     * @return string
     */
    public function homeMdb();

    /**
     * The users extra notable information.
     *
     * @return string
     */
    public function info();

    /**
     * Contains the initials for parts of the user's full name.
     *
     * This may be used as the middle initial in the Windows Address Book.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676202(v=vs.85).aspx
     *
     * @return string
     */
    public function initials();

    /**
     * A bitfield that dictates how the object is instantiated on a particular server.
     *
     * The value of this attribute can differ on different replicas even if the replicas are in sync.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676204(v=vs.85).aspx
     *
     * @return string
     */
    public function instanceType();

    /**
     * If TRUE, the object hosting this attribute must be replicated during installation of a new replica.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676798(v=vs.85).aspx
     *
     * @return string
     */
    public function isCriticalSystemObject();

    /**
     * This attribute is not used.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676822(v=vs.85).aspx
     *
     * @return string
     */
    public function lastLogOff();

    /**
     * The last time the user logged on. This value is stored as a large integer that
     * represents the number of 100-nanosecond intervals since January 1, 1601 (UTC).
     *
     * A value of zero means that the last logon time is unknown.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676823(v=vs.85).aspx
     *
     * @return string
     */
    public function lastLogOn();

    /**
     * This is the time that the user last logged into the domain.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676824(v=vs.85).aspx
     *
     * @return string
     */
    public function lastLogOnTimestamp();

    /**
     * This attribute contains the family or last name for a user.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms679872(v=vs.85).aspx
     *
     * @return string
     */
    public function lastName();

    /**
     * The distinguished name previously used by Exchange.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676830(v=vs.85).aspx
     *
     * @return string
     */
    public function legacyExchangeDn();

    /**
     * The users locale.
     *
     * @return string
     */
    public function locale();

    /**
     * The user's location, such as office number.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676839(v=vs.85).aspx
     *
     * @return string
     */
    public function location();

    /**
     * The date and time (UTC) that this account was locked out. This value is stored
     * as a large integer that represents the number of 100-nanosecond intervals
     * since January 1, 1601 (UTC). A value of zero means that the
     * account is not currently locked out.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676843(v=vs.85).aspx
     *
     * @return string
     */
    public function lockoutTime();

    /**
     * Contains the distinguished name of the user who is the user's manager.
     *
     * The manager's user object contains a directReports property that
     * contains references to all user objects that have their manager
     * properties set to this distinguished name.
     *
     * @link https://msdn.microsoft.com/en-us/library/ms676859(v=vs.85).aspx
     *
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
