<?php

namespace Adldap\Collections;

use Adldap\Adldap;

/**
 * Class AdldapCollection
 * @package Adldap\collections
 */
abstract class AdldapCollection
{
    /**
     * The current Adldap connection via dependency injection
     *
     * @var Adldap
     */
    protected $adldap;

    /**
     * The current object being modified / called
     *
     * @var mixed
     */
    protected $currentObject;

    /**
     * The raw info array from Active Directory
     *
     * @var array
     */
    protected $info;

    /**
     * Constructor.
     *
     * @param $info
     * @param Adldap $adldap
     */
    public function __construct($info, Adldap $adldap)
    {
        $this->setInfo($info);

        $this->adldap = $adldap;
    }

    /**
     * Set the raw info array from Active Directory
     *
     * @param array $info
     */
    public function setInfo(array $info)
    {
        if (isset($this->info) && sizeof($info) >= 1) unset($this->info);

        $this->info = $info;   
    }

    /**
     * Magic get method to retrieve data from the raw array in a formatted way
     *
     * @param string $attribute
     * @return array|null
     */
    public function __get($attribute)
    {
        if (isset($this->info[0]) && is_array($this->info[0]))
        {
            foreach ($this->info[0] as $keyAttr => $valueAttr)
            {
                if (strtolower($keyAttr) == strtolower($attribute))
                {
                    if ($this->info[0][strtolower($attribute)]['count'] == 1)
                    {
                        return $this->info[0][strtolower($attribute)][0];   
                    }
                    else
                    {
                        $array = array();

                        foreach ($this->info[0][strtolower($attribute)] as $key => $value)
                        {
                            if ((string)$key != 'count')
                            {
                                $array[$key] = $value;
                            } 
                        }

                        return $array;   
                    }
                }   
            }
        }

        return NULL;
    }    

    /**
     * Magic set method to update an attribute
     *
     * @param string $attribute
     * @param string $value
     * @return mixed
     */
    abstract public function __set($attribute, $value);

    /**
     * Magic isset method to check for the existence of an attribute
     *
     * @param $attribute
     * @return bool
     */
    public function __isset($attribute)
    {
        if (isset($this->info[0]) && is_array($this->info[0]))
        {
            foreach ($this->info[0] as $keyAttr => $valueAttr)
            {
                if (strtolower($keyAttr) == strtolower($attribute))
                {
                    return true; 
                } 
            } 
        }

        return false;
    }
}
?>
