<?php

namespace Adldap\Objects;

class BatchModification
{
    /**
     * The original value of the attribute before modification.
     *
     * @var null
     */
    protected $original = null;

    /**
     * The attribute of the modification.
     *
     * @var int|string
     */
    protected $attribute;

    /**
     * The values of the modification.
     *
     * @var array
     */
    protected $values = [];

    /**
     * The modtype integer of the batch modification.
     *
     * @var int
     */
    protected $type;

    /**
     * Constructor.
     *
     * @param mixed|null $original
     * @param int|string $attribute
     * @param array      $values
     */
    public function __construct($original = null, $attribute, array $values = [])
    {
        $this->setOriginal($original);
        $this->setAttribute($attribute);
        $this->setValues($values);
    }

    /**
     * Sets the original value of the attribute before modification.
     *
     * @param null $original
     */
    public function setOriginal($original = null)
    {
        $this->original = $original;
    }

    /**
     * Sets the attribute of the modification.
     *
     * @param string $attribute
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * Sets the values of the modification.
     *
     * @param array $values
     */
    public function setValues(array $values)
    {
        $this->values = $values;
    }

    /**
     * Sets the type of the modification.
     *
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Builds the current batch modification.
     *
     * @return void
     */
    public function build()
    {
        $filtered = array_filter($this->values);

        if (is_null($this->original)) {
            // If the original value is null, we'll assume
            // that the attribute doesn't exist yet.
            if (!empty($filtered)) {
                // if the filtered array is not empty, we'll
                // assume the developer is looking to
                // add attributes to the model.
                $this->setType(LDAP_MODIFY_BATCH_ADD);
            }

            // If the filtered array is empty and there is no original
            // value, then we can ignore this attribute since
            // we can't push null values to AD.
        } else {
            if (empty($filtered)) {
                // If there's an original value and the array is
                // empty then we can assume the developer is
                // looking to completely remove all values
                // of the specified attribute.
                $this->setType(LDAP_MODIFY_BATCH_REMOVE_ALL);
            } else {
                // If the array isn't empty then we can assume the
                // developer is trying to replace all attributes.
                $this->setType(LDAP_MODIFY_BATCH_REPLACE);
            }
        }
    }

    /**
     * Returns the built batch modification array.
     *
     * @return array
     */
    public function get()
    {
        $this->build();

        $attrib = $this->attribute;
        $modtype = $this->type;

        switch($modtype) {
            case LDAP_MODIFY_BATCH_REMOVE_ALL:
                // A values key cannot be provided when
                // a remove all type is selected.
                return compact('attrib', 'modtype');
            default:
                $values = $this->values;

                return compact('attrib', 'modtype', 'values');
        }
    }
}
