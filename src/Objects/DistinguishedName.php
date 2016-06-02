<?php

namespace Adldap\Objects;

use Adldap\Schemas\Schema;
use Adldap\Utilities;

class DistinguishedName
{
    /**
     * The domain components in the DN.
     *
     * @var array
     */
    protected $domainComponents = [];

    /**
     * The common names in the DN.
     *
     * @var array
     */
    protected $commonNames = [];

    /**
     * The organizational units in the DN.
     *
     * @var array
     */
    protected $organizationUnits = [];

    /**
     * The organization names in the DN.
     *
     * @var array
     */
    protected $organizationNames = [];

    /**
     * The RDN attribute types.
     *
     * @var array
     */
    protected $types = [
        'o',
        'dc',
        'ou',
        'cn',
    ];

    /**
     * Constructor.
     *
     * @param null $baseDn
     */
    public function __construct($baseDn = null)
    {
        $this->setBase($baseDn);
    }

    /**
     * Returns the complete distinguished name.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }

    /**
     * Returns the complete distinguished name.
     *
     * @return string
     */
    public function get()
    {
        return $this->assemble();
    }

    /**
     * Adds a domain component.
     *
     * @param string $dc
     *
     * @return DistinguishedName
     */
    public function addDc($dc)
    {
        $this->domainComponents[] = $dc;

        return $this;
    }

    /**
     * Removes a domain component.
     *
     * @param string $dc
     *
     * @return DistinguishedName
     */
    public function removeDc($dc)
    {
        $this->domainComponents = array_diff($this->domainComponents, [$dc]);

        return $this;
    }

    /**
     * Adds an organization name.
     *
     * @param string $o
     *
     * @return $this
     */
    public function addO($o)
    {
        $this->organizationNames[] = $o;

        return $this;
    }

    /**
     * Removes an organization name.
     *
     * @param string $o
     *
     * @return DistinguishedName
     */
    public function removeO($o)
    {
        $this->organizationNames = array_diff($this->organizationNames, [$o]);

        return $this;
    }

    /**
     * Adds a common name.
     *
     * @param string $cn
     *
     * @return DistinguishedName
     */
    public function addCn($cn)
    {
        $this->commonNames[] = $cn;

        return $this;
    }

    /**
     * Removes a common name.
     *
     * @param string $cn
     *
     * @return DistinguishedName
     */
    public function removeCn($cn)
    {
        $this->commonNames = array_diff($this->commonNames, [$cn]);

        return $this;
    }

    /**
     * Adds an organizational unit.
     *
     * @param string $ou
     *
     * @return DistinguishedName
     */
    public function addOu($ou)
    {
        $this->organizationUnits[] = $ou;

        return $this;
    }

    /**
     * Removes an organizational unit.
     *
     * @param string $ou
     *
     * @return DistinguishedName
     */
    public function removeOu($ou)
    {
        $this->organizationUnits = array_diff($this->organizationUnits, [$ou]);

        return $this;
    }

    /**
     * Sets the base RDN of the distinguished name.
     *
     * @param string $base
     *
     * @return DistinguishedName
     */
    public function setBase($base)
    {
        // If the base DN isn't null we'll try to explode it.
        $base = (Utilities::explodeDn($base, false) ?: []);

        // Remove the count key from the exploded distinguished name.
        unset($base['count']);

        foreach ($base as $key => $rdn) {
            // We'll break the RDN into pieces
            $pieces = (explode('=', $rdn) ?: []);

            // If there's exactly 2 pieces, then we can work with it.
            if (count($pieces) === 2) {
                $attribute = ucfirst($pieces[0]);

                // We see what type of RDN it is and add each accordingly
                call_user_func_array([$this, 'add'.$attribute], [$pieces[1]]);
            }
        }

        return $this;
    }

    /**
     * Assembles all of the RDNs and returns the result.
     *
     * @return string
     */
    public function assemble()
    {
        return implode(',', array_filter([
            $this->assembleCns(),
            $this->assembleOus(),
            $this->assembleDcs(),
            $this->assembleOs(),
        ]));
    }

    /**
     * Assembles the common names in the Distinguished name.
     *
     * @return string
     */
    public function assembleCns()
    {
        return $this->assembleRdns(Schema::get()->commonName(), $this->commonNames);
    }

    /**
     * Assembles the organizational units in the Distinguished Name.
     *
     * @return string
     */
    public function assembleOus()
    {
        return $this->assembleRdns(Schema::get()->organizationalUnitShort(), $this->organizationUnits);
    }

    /**
     * Assembles the domain components in the Distinguished Name.
     *
     * @return string
     */
    public function assembleDcs()
    {
        return $this->assembleRdns(Schema::get()->domainComponent(), $this->domainComponents);
    }

    /**
     * Assembles the organization names in the Distinguished name.
     *
     * @return string
     */
    public function assembleOs()
    {
        return $this->assembleRdns(Schema::get()->organizationName(), $this->organizationNames);
    }

    /**
     * Assembles an RDN with the specified attribute and value.
     *
     * @param string $attribute
     * @param array  $values
     *
     * @return string
     */
    protected function assembleRdns($attribute, array $values = [])
    {
        return implode(',', array_map(function ($value) use ($attribute) {
            return sprintf('%s=%s', $attribute, Utilities::escape($value, '', 2));
        }, $values));
    }
}
