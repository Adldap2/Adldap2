<?php

namespace Adldap\Objects;

use Adldap\Schemas\ActiveDirectory;

class DistinguishedName
{
    /**
     * The complete DN string.
     *
     * @var string
     */
    protected $string;

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

    public function get()
    {

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
     * @param string $value
     *
     * @return string
     */
    private function assembleRdn($attribute, $value)
    {
        return sprintf('%s=%s', $attribute, $value);
    }
}
