<?php

namespace Adldap\Objects;

use Adldap\Classes\Utilities;
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
     * Adds a DC to the DC array.
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
     * Removes a DC from the DC array.
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
     * Adds a CN to the CN array.
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
     * Removes a CN from the CN array.
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
     * Adds an OU to the OU array.
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
     * Removes an OU from the OU array.
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
     * Sets the base DB string.
     *
     * @param string $base
     *
     * @return DistinguishedName
     */
    public function setBase($base)
    {
        if (!is_null($base)) {
            // If the base DN isn't null we'll try to explode it.
            $base = Utilities::explodeDn($base, false);

            // Since exploding a DN returns false on failure,
            // we'll double check to make sure it's an array
            if (is_array($base)) {
                // We need to reverse the base to keep the order in tact since RDNs
                // are already reversed to follow the right to left pattern
                $base = array_reverse($base);

                foreach ($base as $key => $rdn) {
                    // We'll avoid going through the count key as it's
                    // automatically created when exploding a DN
                    if ($key !== 'count') {
                        // We'll break the RDN into pieces
                        $pieces = explode('=', $rdn);

                        // If there's exactly 2 pieces, then we can work with it.
                        if (count($pieces) === 2) {
                            // We see what type of RDN it is and add each accordingly
                            switch (strtoupper($pieces[0])) {
                                case 'DC':
                                    $this->addDc($pieces[1]);
                                    break;
                                case 'OU':
                                    $this->addOu($pieces[1]);
                                    break;
                                case 'CN':
                                    $this->addCn($pieces[1]);
                                    break;
                            }
                        }
                    }
                }
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
        $cns = $this->assembleCns();

        $ous = $this->assembleOus();

        $dcs = $this->assembleDcs();

        return implode(',', array_filter([$cns, $ous, $dcs]));
    }

    /**
     * Assembles the common names in the Distinguished name.
     *
     * @return null|string
     */
    public function assembleCns()
    {
        return $this->assembleRdns(ActiveDirectory::COMMON_NAME, $this->commonNames);
    }

    /**
     * Assembles the organizational units in the Distinguished Name.
     *
     * @return null|string
     */
    public function assembleOus()
    {
        return $this->assembleRdns(ActiveDirectory::ORGANIZATIONAL_UNIT_SHORT, $this->organizationUnits);
    }

    /**
     * Assembles the domain components in the Distinguished Name.
     *
     * @return null|string
     */
    public function assembleDcs()
    {
        return $this->assembleRdns(ActiveDirectory::DOMAIN_COMPONENT, $this->domainComponents);
    }

    /**
     * Assembles an RDN with the specified attribute and value.
     *
     * @param string $attribute
     * @param array  $values
     *
     * @return null|string
     */
    protected function assembleRdns($attribute, array $values = [])
    {
        if (count($values) > 0) {
            $values = array_reverse($values);

            $values = array_map(function ($value) use ($attribute) {
                return sprintf('%s=%s', $attribute, Utilities::escape($value, '', 2));
            }, $values);

            return implode(',', $values);
        }
    }
}
