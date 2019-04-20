<?php

namespace Adldap\Models;

/**
 * Class ForeignSecurityPrincipal
 *
 * Represents an LDAP ForeignSecurityPrincipal.
 *
 * @package Adldap\Models
 */
class ForeignSecurityPrincipal extends Entry
{
    use Concerns\HasMemberOf;
}
