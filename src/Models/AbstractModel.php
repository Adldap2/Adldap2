<?php

namespace Adldap\Models;

use ArrayAccess;
use JsonSerializable;
use Adldap\Adldap;
use Adldap\Classes\Utilities;
use Adldap\Exceptions\AdldapException;
use Adldap\Exceptions\ModelNotFoundException;
use Adldap\Objects\DistinguishedName;
use Adldap\Schemas\ActiveDirectory;

abstract class AbstractModel implements ArrayAccess, JsonSerializable
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
     * Determine if the given offset exists.
     *
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->{$offset});
    }

    /**
     * Get the value for a given offset.
     *
     * @param string $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->{$offset};
    }

    /**
     * Set the value at the given offset.
     *
     * @param string $offset
     * @param mixed  $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->{$offset} = $value;
    }

    /**
     * Unset the value at the given offset.
     *
     * @param string $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->{$offset});
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->getAttributes();
    }

    /**
     * Syncs the original attributes with
     * the model's current attributes.
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
        if (is_null($subKey)) {
            if ($this->hasAttribute($key)) {
                return $this->attributes[$key];
            }
        } else {
            if ($this->hasAttribute($key, $subKey)) {
                return $this->attributes[$key][$subKey];
            }
        }

        return;
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
        foreach ($attributes as $key => $value) {
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
        if (is_null($subKey)) {
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
            if (!is_null($subKey)) {
                if (is_array($this->attributes[$key]) && array_key_exists($subKey, $this->attributes[$key])) {
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
        foreach ($this->attributes as $key => $value) {
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
                } elseif ($value !== $this->original[$key]) {
                    if (is_null($value)) {
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
        if (!is_array($values)) {
            $values = [$values];
        }

        $this->modifications[] = [
            'attrib'  => $key,
            'modtype' => $type,
            'values'  => $values,
        ];

        return $this;
    }

    /**
     * Returns the model's name. An AD alias for the CN attribute.
     *
     * https://msdn.microsoft.com/en-us/library/ms675449(v=vs.85).aspx
     *
     * @return string
     */
    public function getName()
    {
        return $this->getAttribute(ActiveDirectory::NAME, 0);
    }

    /**
     * Sets the model's name.
     *
     * @param string $name
     *
     * @return Entry
     */
    public function setName($name)
    {
        return $this->setAttribute(ActiveDirectory::NAME, $name, 0);
    }

    /**
     * Returns the model's common name.
     *
     * https://msdn.microsoft.com/en-us/library/ms675449(v=vs.85).aspx
     *
     * @return string
     */
    public function getCommonName()
    {
        return $this->getAttribute(ActiveDirectory::COMMON_NAME, 0);
    }

    /**
     * Sets the model's common name.
     *
     * @param string $name
     *
     * @return Entry
     */
    public function setCommonName($name)
    {
        return $this->setAttribute(ActiveDirectory::COMMON_NAME, $name, 0);
    }

    /**
     * Returns the model's samaccountname.
     *
     * https://msdn.microsoft.com/en-us/library/ms679635(v=vs.85).aspx
     *
     * @return string
     */
    public function getAccountName()
    {
        return $this->getAttribute(ActiveDirectory::ACCOUNT_NAME, 0);
    }

    /**
     * Sets the model's samaccountname.
     *
     * @param string $accountName
     *
     * @return AbstractModel
     */
    public function setAccountName($accountName)
    {
        return $this->setAttribute(ActiveDirectory::ACCOUNT_NAME, $accountName, 0);
    }

    /**
     * Returns the model's samaccounttype.
     *
     * https://msdn.microsoft.com/en-us/library/ms679637(v=vs.85).aspx
     *
     * @return string
     */
    public function getAccountType()
    {
        return $this->getAttribute(ActiveDirectory::ACCOUNT_TYPE, 0);
    }

    /**
     * Returns the model's `when created` time.
     *
     * https://msdn.microsoft.com/en-us/library/ms680924(v=vs.85).aspx
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getAttribute(ActiveDirectory::CREATED_AT, 0);
    }

    /**
     * Returns the model's `when changed` time.
     *
     * https://msdn.microsoft.com/en-us/library/ms680921(v=vs.85).aspx
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getAttribute(ActiveDirectory::UPDATED_AT, 0);
    }

    /**
     * Returns the Container of the current Model.
     *
     * https://msdn.microsoft.com/en-us/library/ms679012(v=vs.85).aspx
     *
     * @return Container|Entry|bool
     */
    public function getObjectClass()
    {
        return $this->getAdldap()->search()->findByDn($this->getObjectCategoryDn());
    }

    /**
     * Returns the CN of the model's object category.
     *
     * @return null|string
     */
    public function getObjectCategory()
    {
        $category = $this->getObjectCategoryArray();

        if (is_array($category) && array_key_exists(0, $category)) {
            return $category[0];
        }

        return;
    }

    /**
     * Returns the model's object category DN in an exploded array.
     *
     * @return array
     */
    public function getObjectCategoryArray()
    {
        return Utilities::explodeDn($this->getObjectCategoryDn());
    }

    /**
     * Returns the model's object category DN string.
     *
     * @return null|string
     */
    public function getObjectCategoryDn()
    {
        return $this->getAttribute(ActiveDirectory::OBJECT_CATEGORY, 0);
    }

    /**
     * Returns the model's object SID.
     *
     * https://msdn.microsoft.com/en-us/library/ms679024(v=vs.85).aspx
     *
     * @return string
     */
    public function getObjectSid()
    {
        return $this->getAttribute(ActiveDirectory::OBJECT_SID, 0);
    }

    /**
     * Returns the model's primary group ID.
     *
     * https://msdn.microsoft.com/en-us/library/ms679375(v=vs.85).aspx
     *
     * @return string
     */
    public function getPrimaryGroupId()
    {
        return $this->getAttribute(ActiveDirectory::PRIMARY_GROUP_ID, 0);
    }

    /**
     * Returns the model's instance type.
     *
     * https://msdn.microsoft.com/en-us/library/ms676204(v=vs.85).aspx
     *
     * @return int
     */
    public function getInstanceType()
    {
        return $this->getAttribute(ActiveDirectory::INSTANCE_TYPE, 0);
    }

    /**
     * Returns the model's GUID.
     *
     * @return string
     */
    public function getGuid()
    {
        return $this->getAttribute(ActiveDirectory::OBJECT_GUID, 0);
    }

    /**
     * Returns the model's SID.
     *
     * @return string
     */
    public function getSid()
    {
        return $this->getAttribute(ActiveDirectory::OBJECT_SID, 0);
    }

    /**
     * Returns the model's max password age.
     *
     * @return string
     */
    public function getMaxPasswordAge()
    {
        return $this->getAttribute(ActiveDirectory::MAX_PASSWORD_AGE, 0);
    }

    /**
     * Returns the model's distinguished name string.
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
     * Sets the model's distinguished name attribute.
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
     * Returns the model's distinguished name string.
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
     * Returns a new DistinguishedName object for building onto.
     *
     * @return DistinguishedName
     */
    public function getDnBuilder()
    {
        return new DistinguishedName($this->getDistinguishedName());
    }

    /**
     *  Sets the model's distinguished name attribute.
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
     * Persists the changes to the LDAP server and returns the result.
     *
     * @return bool|$this
     */
    public function save()
    {
        if ($this->exists) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    /**
     * Persists attribute updates to the active directory record.
     *
     * @return bool|$this
     */
    public function update()
    {
        $modifications = $this->getModifications();

        $dn = $this->getDn();

        if (count($modifications) > 0) {
            $modified = $this->getAdldap()->getConnection()->modifyBatch($dn, $modifications);

            if($modified) {
                return $this->getAdldap()->search()->findByDn($dn);
            }

            return false;
        }

        // We need to return true here because modify batch will
        // return false if no modifications are made
        return true;
    }

    /**
     * Creates an attribute on the current model.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function createAttribute($attribute, $value)
    {
        if($this->exists) {
            $add = [$attribute => $value];

            return $this->getAdldap()->getConnection()->modAdd($this->getDn(), $add);
        }

        return false;
    }

    /**
     * Creates an active directory record.
     *
     * @return bool|$this
     */
    public function create()
    {
        $dn = $this->getDn();

        $attributes = $this->getAttributes();

        // We need to remove the dn from the attributes array
        // as it's inserted independently.
        unset($attributes['dn']);

        $added = $this->getAdldap()->getConnection()->add($dn, $attributes);

        if($added) {
            return $this->getAdldap()->search()->findByDn($dn);
        }

        return false;
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
        if($this->exists) {
            // We need to pass in an empty array as the value
            // for the attribute so AD knows to remove it.
            $remove = [$attribute => []];

            return $this->getAdldap()->getConnection()->modDelete($this->getDn(), $remove);
        }

        return false;
    }

    /**
     * Deletes the current entry.
     *
     * @throws ModelNotFoundException
     * @throws AdldapException
     *
     * @return bool
     */
    public function delete()
    {
        $dn = $this->getDn();

        if (!$this->exists) {
            // Make sure the record exists before we can delete it
            $message = 'Model does not exist in active directory.';

            throw new ModelNotFoundException($message);
        } elseif (is_null($dn) || empty($dn)) {
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

        if ($bool === ActiveDirectory::FALSE) {
            return false;
        } elseif ($bool === ActiveDirectory::TRUE) {
            return true;
        } else {
            return;
        }
    }
}
