<?php

namespace Adldap\Objects;

/**
 * Class AccountControl
 * @package Adldap\Objects
 */
class AccountControl extends AbstractObject
{
    /**
     * If the specified $attribute exists, the 'value' attribute
     * is updated by the specified $value parameter by adding the
     * current 'value' attribute to it.
     *
     * @param $attribute
     * @param $value
     */
    public function setValueIfAttributeExists($attribute, $value)
    {
        if($this->hasAttribute($attribute))
        {
            $currentValue = intval($this->getAttribute('value'));

            $this->setAttribute('value', $currentValue + $value);
        }
    }
}
