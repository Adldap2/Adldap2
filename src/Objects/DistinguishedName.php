<?php

namespace Adldap\Objects;

use Adldap\Schemas\ActiveDirectory;

class DistinguishedName
{
    /**
     * Stores the domain components in the DN.
     *
     * @var array
     */
    protected $domainComponents = [];

    /**
     * Stores the common names in the DN.
     *
     * @var array
     */
    protected $commonNames = [];

    /**
     * Stores the organizational units in the DN.
     *
     * @var array
     */
    protected $organizationUnits = [];

    /**
     * Returns the complete distinguished name.
     *
     * @return string
     */
    public function get()
    {
        $cns = $this->assembleRdns(ActiveDirectory::COMMON_NAME, $this->commonNames);

        $ous = $this->assembleRdns(ActiveDirectory::ORGANIZATIONAL_UNIT, $this->organizationUnits);

        $dcs = $this->assembleRdns(ActiveDirectory::DOMAIN_COMPONENT, $this->domainComponents);

        return implode(',', [$cns, $ous, $dcs]);
    }

    /**
     * Adds a DC to the DC array.
     *
     * @param string $dc
     *
     * @return $this
     */
    public function addDc($dc)
    {
        $this->domainComponents[] = $dc;

        return $this;
    }

    /**
     * Adds a CN to the CN array.
     *
     * @param string $cn
     *
     * @return $this
     */
    public function addCn($cn)
    {
        $this->commonNames[] = $cn;

        return $this;
    }

    /**
     * Adds an OU to the OU array.
     *
     * @param string $ou
     *
     * @return $this
     */
    public function addOu($ou)
    {
        $this->organizationUnits[] = $ou;

        return $this;
    }

    /**
     * Assembles an RDN with the specified attribute and value.
     *
     * @param string $attribute
     * @param array  $values
     *
     * @return string
     */
    private function assembleRdns($attribute, array $values = [])
    {
        $values = array_map(function($value) use ($attribute) {
            return $attribute.'='.$value;
        }, $values);

        return implode(',', $values);
    }
}
