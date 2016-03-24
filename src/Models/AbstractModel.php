<?php

namespace Adldap\Models;

use Adldap\Classes\Utilities;
use Adldap\Exceptions\AdldapException;
use Adldap\Exceptions\ModelNotFoundException;
use Adldap\Objects\BatchModification;
use Adldap\Objects\DistinguishedName;
use Adldap\Query\Builder;
use Adldap\Schemas\ActiveDirectory;
use ArrayAccess;
use DateTime;
use JsonSerializable;

abstract class AbstractModel implements ArrayAccess, JsonSerializable
{
    /**
     * Indicates if the model exists in active directory.
     *
     * @var bool
     */
    public $exists = false;

    /**
     * The default output date format for all time related methods.
     *
     * Default format is suited for MySQL timestamps.
     *
     * @var string
     */
    public $dateFormat = 'Y-m-d H:i:s';

    /**
     * The current LDAP connection instance.
     *
     * @var Builder
     */
    protected $query;

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
     * @param array   $attributes
     * @param Builder $builder
     */
    public function __construct(array $attributes, Builder $builder)
    {
        $this->fill($attributes);

        $this->query = $builder;
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
        $attributes = $this->getAttributes();

        // We need to remove the object SID and GUID from
        // being serialized as these attributes contain
        // characters that cannot be serialized.
        unset($attributes[ActiveDirectory::OBJECT_SID]);
        unset($attributes[ActiveDirectory::OBJECT_GUID]);

        return $attributes;
    }

    /**
     * Synchronizes the original attributes with
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
     * Re-sets the models raw attributes by looking up the
     * current models DN in AD.
     *
     * @return bool
     */
    public function syncRaw()
    {
        $query = $this->query->newInstance();

        $model = $query->findByDn($this->getDn());

        if ($model instanceof $this) {
            $this->setRawAttributes($model->getAttributes());

            return true;
        }

        return false;
    }

    /**
     * Retrieves the specified key from the attribute array.
     *
     * If a sub-key is specified, it will try and
     * retrieve it from the parent keys array.
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
        $this->attributes = $this->filterRawAttributes($attributes);

        $this->syncOriginal();

        // Set exists to true since raw attributes are only
        // set in the case of attributes being loaded by
        // query results.
        $this->exists = true;

        return $this;
    }

    /**
     * Filters the count key recursively from raw LDAP attributes.
     *
     * @param array  $attributes
     * @param string $key
     *
     * @return array
     */
    public function filterRawAttributes(array $attributes = [], $key = 'count')
    {
        unset($attributes[$key]);

        foreach ($attributes as &$value) {
            if (is_array($value)) {
                $value = $this->filterRawAttributes($value, $key);
            }
        }

        return $attributes;
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
            // exists in the nested attribute array.
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
     * Get the attributes that have been changed since last sync.
     *
     * @return array
     */
    public function getDirty()
    {
        $dirty = [];

        foreach ($this->attributes as $key => $value) {
            if (!array_key_exists($key, $this->original)) {
                $dirty[$key] = $value;
            } elseif ($value !== $this->original[$key] &&
                !$this->originalIsNumericallyEquivalent($key)) {
                $dirty[$key] = $value;
            }
        }

        return $dirty;
    }

    /**
     * Sets and returns the models modifications.
     *
     * @return array
     */
    public function getModifications()
    {
        $dirty = $this->getDirty();

        foreach ($dirty as $attribute => $values) {
            if (!is_array($values)) {
                // Make sure values is always an array.
                $values = [$values];
            }

            $modification = new BatchModification();

            if (array_key_exists($attribute, $this->original)) {
                $modification->setOriginal($this->original[$attribute]);
            }

            $modification->setAttribute($attribute);
            $modification->setValues($values);
            $modification->build();

            $this->addModification($modification);
        }

        return $this->modifications;
    }

    /**
     * Sets the models modifications array.
     *
     * @param array $modifications
     *
     * @return $this
     */
    public function setModifications(array $modifications = [])
    {
        $this->modifications = $modifications;

        return $this;
    }

    /**
     * Adds a modification to the models modifications array.
     *
     * @param BatchModification $modification
     *
     * @return $this
     */
    public function addModification(BatchModification $modification)
    {
        $batch = $modification->get();

        if (is_array($batch)) {
            $this->modifications[] = $batch;
        }

        return $this;
    }

    /**
     * Returns true / false if the current model is writeable
     * by checking its instance type integer.
     *
     * @return bool
     */
    public function isWriteable()
    {
        return (int) $this->getInstanceType() === 4;
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
     * Returns the model's `whenCreated` time.
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
     * Returns the created at time in a mysql formatted date.
     *
     * @return string
     */
    public function getCreatedAtDate()
    {
        $timestamp = $this->getCreatedAtTimestamp();

        return (new DateTime())->setTimestamp($timestamp)->format($this->dateFormat);
    }

    /**
     * Returns the created at time in a unix timestamp format.
     *
     * @return float
     */
    public function getCreatedAtTimestamp()
    {
        return DateTime::createFromFormat('YmdHis.0Z', $this->getCreatedAt())->getTimestamp();
    }

    /**
     * Returns the model's `whenChanged` time.
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
     * Returns the updated at time in a mysql formatted date.
     *
     * @return string
     */
    public function getUpdatedAtDate()
    {
        $timestamp = $this->getUpdatedAtTimestamp();

        return (new DateTime())->setTimestamp($timestamp)->format($this->dateFormat);
    }

    /**
     * Returns the updated at time in a unix timestamp format.
     *
     * @return float
     */
    public function getUpdatedAtTimestamp()
    {
        return DateTime::createFromFormat('YmdHis.0Z', $this->getUpdatedAt())->getTimestamp();
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
        return $this->query->findByDn($this->getObjectCategoryDn());
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
            $updated = $this->query->getConnection()->modifyBatch($dn, $modifications);

            if ($updated) {
                $this->syncRaw();

                return true;
            }

            // Modification failed, return false.
            return false;
        }

        // We need to return true here because modify batch will
        // return false if no modifications are made
        // but this may not always be the case.
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
        if ($this->exists) {
            $add = [$attribute => $value];

            return $this->query->getConnection()->modAdd($this->getDn(), $add);
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

        $created = $this->query->getConnection()->add($dn, $attributes);

        if ($created) {
            $this->syncRaw();

            return true;
        }

        return false;
    }

    /**
     * Updates the specified attribute with the specified value.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function updateAttribute($attribute, $value)
    {
        if ($this->exists) {
            $modify = [$attribute => $value];

            $updated = $this->query->getConnection()->modReplace($this->getDn(), $modify);

            if ($updated) {
                // If the models attribute was successfully updated, we'll
                // resynchronize the models raw attributes.
                $this->syncRaw();

                return true;
            }
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
        if ($this->exists) {
            // We need to pass in an empty array as the value
            // for the attribute so AD knows to remove it.
            $remove = [$attribute => []];

            $deleted = $this->query->getConnection()->modDelete($this->getDn(), $remove);

            if ($deleted) {
                // If the models attribute was successfully deleted, we'll
                // resynchronize the models raw attributes.
                $this->syncRaw();

                return true;
            }
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

        $deleted = $this->query->getConnection()->delete($dn);

        if ($deleted) {
            // We'll set the exists property to false on delete
            // so the dev can run create operations.
            $this->exists = false;

            return true;
        }

        return false;
    }

    /**
     * Moves the current model to a new RDN and new parent.
     *
     * @param string     $rdn
     * @param string     $newParentDn
     * @param bool|false $deleteOldRdn
     *
     * @return bool
     */
    public function move($rdn, $newParentDn, $deleteOldRdn = false)
    {
        return $this->query->getConnection()->rename($this->getDn(), $rdn, $newParentDn, $deleteOldRdn);
    }

    /**
     * Determine if the new and old values for a given key are numerically equivalent.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function originalIsNumericallyEquivalent($key)
    {
        $current = $this->attributes[$key];

        $original = $this->original[$key];

        return is_numeric($current) && is_numeric($original) && strcmp((string) $current, (string) $original) === 0;
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
