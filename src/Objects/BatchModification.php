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
     * Sets the original value of the attribute before modification.
     *
     * @param null $original
     */
    public function setOriginal($original = null)
    {
        $this->original = $original;
    }

    /**
     * Returns the original value of the attribute before modification.
     *
     * @return mixed
     */
    public function getOriginal()
    {
        return $this->original;
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
     * Returns the attribute of the modification.
     *
     * @return string
     */
    public function getAttribute()
    {
        return $this->attribute;
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
     * Returns the values of the modification.
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
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
     * Returns the type of the modification.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Builds the current batch modification.
     *
     * @return void
     */
    public function build()
    {
        $filtered = array_diff(array_map('trim', $this->values), ['']);

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
     * @return array|null
     */
    public function get()
    {
        $attrib = $this->attribute;
        $modtype = $this->type;
        $values = $this->values;

        switch ($modtype) {
            case LDAP_MODIFY_BATCH_REMOVE_ALL:
                // A values key cannot be provided when
                // a remove all type is selected.
                return compact('attrib', 'modtype');
            case LDAP_MODIFY_BATCH_REMOVE:
                return compact('attrib', 'modtype', 'values');
            case LDAP_MODIFY_BATCH_ADD:
                return compact('attrib', 'modtype', 'values');
            case LDAP_MODIFY_BATCH_REPLACE:
                return compact('attrib', 'modtype', 'values');
            default:
                // If the modtype isn't recognized, we'll return null.
                return;
        }
    }
}
