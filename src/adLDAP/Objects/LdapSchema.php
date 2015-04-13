<?php

namespace adLDAP\Objects;

class LdapSchema extends AbstractObject
{
    public function setAttribute($key, $value)
    {
        $this->attributes[$key][0] = $value;

        return $this;
    }
}