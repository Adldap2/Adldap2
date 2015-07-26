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

        $ous = $this->assembleRdns(ActiveDirectory::ORGANIZATIONAL_UNIT_SHORT, $this->organizationUnits);

        $dcs = $this->assembleRdns(ActiveDirectory::DOMAIN_COMPONENT, $this->domainComponents);

        return implode(',', array_filter([$cns, $ous, $dcs]));
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
     * Removes a DC from the DC array.
     *
     * @param string $dc
     *
     * @return $this
     */
    public function removeDc($dc)
    {
        $this->domainComponents = array_diff($this->domainComponents, [$dc]);

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
     * Removes a CN from the CN array.
     *
     * @param string $cn
     *
     * @return $this
     */
    public function removeCn($cn)
    {
        $this->commonNames = array_diff($this->commonNames, [$cn]);

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
     * Removes an OU from the OU array.
     *
     * @param string $ou
     *
     * @return $this
     */
    public function removeOu($ou)
    {
        $this->organizationUnits = array_diff($this->organizationUnits, [$ou]);

        return $this;
    }

    /**
     * Assembles an RDN with the specified attribute and value.
     *
     * @param string $attribute
     * @param array  $values
     *
     * @return null|string
     */
    private function assembleRdns($attribute, array $values = [])
    {
        if(count($values) > 0) {
            $values = array_map(function($value) use ($attribute) {
                return $attribute.'='.$value;
            }, $values);

            return implode(',', $values);
        }

        return null;
    }
}
