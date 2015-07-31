<?php

namespace Adldap\Models;

use Adldap\Exceptions\AdldapException;
use Adldap\Exceptions\ModelNotFoundException;
use Adldap\Schemas\ActiveDirectory;
use Adldap\Adldap;

abstract class AbstractModel
{
    /**
     * Indicates if the model exists in active directory.
     *
     * @var bool
     */
    public $exists = false;

    /**
     * The current LDAP connection instance.
     *
     * @var Adldap
     */
    protected $adldap;

    /**
     * Holds the current objects attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Holds the current objects original attributes.
     *
     * @var array
     */
    protected $original = [];

    /**
     * Holds the current objects modified attributes.
     *
     * @var array
     */
    protected $modifications = [];

    /**
     * Constructor.
     *
     * @param array  $attributes
     * @param Adldap $adldap
     */
    public function __construct(array $attributes = [], Adldap $adldap)
    {
        $this->syncOriginal();

        $this->fill($attributes);

        $this->adldap = $adldap;
    }

    /**
     * Dynamically retrieve attributes on the object.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the object.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return $this
     */
    public function __set($key, $value)
    {
        return $this->setAttribute($key, $value);
    }

    /**
     * Returns the entry's distinguished name string.
     *
     * https://msdn.microsoft.com/en-us/library/aa366101(v=vs.85).aspx
     *
     * @return string
     */
    public function getDistinguishedName()
    {
        return $this->getAttribute(ActiveDirectory::DISTINGUISHED_NAME);
    }

    /**
     * Sets the entry's distinguished name attribute.
     *
     * @param string $dn
     *
     * @return Entry
     */
    public function setDistinguishedName($dn)
    {
        return $this->setAttribute(ActiveDirectory::DISTINGUISHED_NAME, (string) $dn);
    }

    /**
     * Returns the entry's distinguished name string.
     *
     * (Alias for getDistinguishedName())
     *
     * https://msdn.microsoft.com/en-us/library/aa366101(v=vs.85).aspx
     *
     * @return string
     */
    public function getDn()
    {
        return $this->getDistinguishedName();
    }

    /**
     *  Sets the entry's distinguished name attribute.
     *
     * (Alias for setDistinguishedName())
     *
     * @param string $dn
     *
     * @return Entry
     */
    public function setDn($dn)
    {
        return $this->setDistinguishedName($dn);
    }

    /**
     * Syncs the original attributes with
     * the entry's current attributes.
     *
     * @return $this
     */
    public function syncOriginal()
    {
        $this->original = $this->attributes;

        return $this;
    }

    /**
     * Retrieves the specified key from the attribute array.
     *
     * If a sub-key is specified, it will try
     * and retrieve it from the parent keys array.
     *
     * @param int|string $key
     * @param int|string $subKey
     *
     * @return mixed
     */
    public function getAttribute($key, $subKey = null)
    {
        if(is_null($subKey)) {
            if ($this->hasAttribute($key)) {
                return $this->attributes[$key];
            }
        } else {
            if ($this->hasAttribute($key, $subKey)) {
                return $this->attributes[$key][$subKey];
            }
        }

        return null;
    }

    /**
     * Retrieves the attributes array property.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Fills the entry with the supplied attributes.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function fill(array $attributes = [])
    {
        foreach($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Sets attributes on the current entry.
     *
     * @param int|string $key
     * @param mixed      $value
     * @param int|string $subKey
     *
     * @return $this
     */
    public function setAttribute($key, $value, $subKey = null)
    {
        if(is_null($subKey)) {
            $this->attributes[$key] = $value;
        } else {
            $this->attributes[$key][$subKey] = $value;
        }

        return $this;
    }

    /**
     * Sets the attributes property.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function setRawAttributes(array $attributes = [])
    {
        $this->attributes = $attributes;

        $this->exists = true;

        $this->syncOriginal();

        return $this;
    }

    /**
     * Returns true / false if the specified attribute
     * exists in the attributes array.
     *
     * @param int|string $key
     * @param int|string $subKey
     *
     * @return bool
     */
    public function hasAttribute($key, $subKey = null)
    {
        if (array_key_exists($key, $this->attributes)) {
            // If a sub key is given, we'll check if it
            // exists in the nested attribute array
            if(!is_null($subKey)) {
                if(is_array($this->attributes[$key]) && array_key_exists($subKey, $this->attributes[$key])) {
                    return true;
                } else {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Returns the number of attributes inside
     * the attributes property.
     *
     * @return int
     */
    public function countAttributes()
    {
        return count($this->getAttributes());
    }

    /**
     * Returns the objects modifications.
     *
     * @return array
     */
    public function getModifications()
    {
        foreach($this->attributes as $key => $value) {
            // If the key still exists inside the original attributes,
            // the developer is modifying an attribute.
            if (array_key_exists($key, $this->original)) {
                if (is_array($value)) {
                    if (count(array_diff($value, $this->original[$key])) > 0) {
                        // Make sure we remove the count key as we don't
                        // want to push that attribute into AD
                        unset($value['count']);

                        // If the value of the set attribute is an array and the differences
                        // are greater than zero, we'll replace the attribute.
                        $this->setModification($key, LDAP_MODIFY_BATCH_REPLACE, $value);
                    }
                } else if ($value !== $this->original[$key]) {
                    if(is_null($value)) {
                        // If the value is set to null, then we'll
                        // assume they want the attribute removed
                        $this->setModification($key, LDAP_MODIFY_BATCH_REMOVE, $value);
                    } else {
                        // If the value doesn't equal it's original, we'll replace it.
                        $this->setModification($key, LDAP_MODIFY_BATCH_REPLACE, $value);
                    }
                }
            } else {
                // The value doesn't exist at all, we'll add it.
                $this->setModification($key, LDAP_MODIFY_BATCH_ADD, $value);
            }
        }

        return $this->modifications;
    }

    /**
     * Sets a modification in the objects modifications array.
     *
     * @param int|string $key
     * @param int        $type
     * @param mixed      $values
     *
     * @return $this
     */
    public function setModification($key, $type, $values)
    {
        // We need to make sure the values given are always in an array.
        if(!is_array($values)) {
            $values = [$values];
        }

        $this->modifications[] = [
            'attrib' => $key,
            'modtype' => $type,
            'values' => $values,
        ];

        return $this;
    }

    /**
     * Persists the changes to the LDAP server and returns the result.
     *
     * @return bool
     */
    public function save()
    {
        if($this->exists) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    /**
     * Persists attribute updates to the active directory record.
     *
     * @return bool
     */
    public function update()
    {
        $modifications = $this->getModifications();

        if(count($modifications) > 0) {
            return $this->getAdldap()->getConnection()->modifyBatch($this->getDn(), $modifications);
        }

        return true;
    }

    /**
     * Creates an active directory record.
     *
     * @return bool
     */
    public function create()
    {
        return $this->getAdldap()->getConnection()->add($this->getDn(), $this->getAttributes());
    }

    /**
     * Deletes an attribute on the current entry.
     *
     * @param string $attribute
     *
     * @return bool
     */
    public function deleteAttribute($attribute)
    {
        // We need to pass in an empty array as the value
        // for the attribute so LDAP knows to remove it.
        $remove = [$attribute => []];

        return $this->getAdldap()->getConnection()->modDelete($this->getDn(), $remove);
    }

    /**
     * Deletes the current entry.
     *
     * @return bool
     *
     * @throws ModelNotFoundException
     * @throws AdldapException
     */
    public function delete()
    {
        $dn = $this->getDn();

        if(!$this->exists) {
            // Make sure the record exists before we can delete it
            $message = 'Model does not exist in active directory.';

            throw new ModelNotFoundException($message);
        } else if(is_null($dn) || empty($dn)) {
            // If the record exists but the DN attribute does
            // not exist, we can't process a delete.
            $message = 'Unable to delete. The current model does not have a distinguished name present.';

            throw new AdldapException($message);
        }

        return $this->getAdldap()->getConnection()->delete($dn);
    }

    /**
     * Returns the current Adldap instance.
     *
     * @return Adldap
     */
    public function getAdldap()
    {
        return $this->adldap;
    }

    /**
     * Converts the inserted string boolean to a PHP boolean.
     *
     * @param string $bool
     *
     * @return null|bool
     */
    protected function convertStringToBool($bool)
    {
        $bool = strtoupper($bool);

        if($bool === ActiveDirectory::FALSE) {
            return false;
        } else if($bool === ActiveDirectory::TRUE) {
            return true;
        } else {
            return null;
        }
    }
}
