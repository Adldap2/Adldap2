<?php

namespace Adldap\Models;

use Adldap\Exceptions\AdldapException;
use Adldap\Connections\ConnectionInterface;
use Adldap\Schemas\ActiveDirectory;

class Entry
{
    /**
     * Indicates if the entry exist in active directory.
     *
     * @var bool
     */
    public $exists = false;

    /**
     * The current LDAP connection instance.
     *
     * @var ConnectionInterface
     */
    protected $connection;

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
     * @param array               $attributes
     * @param ConnectionInterface $connection
     */
    public function __construct(array $attributes = [], ConnectionInterface $connection)
    {
        $this->syncOriginal();

        $this->fill($attributes);

        $this->connection = $connection;
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
     * Sets the original attributes on the entry.
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
        if(!is_null($subKey)) {
            if ($this->hasAttribute($key, $subKey)) {
                return $this->attributes[$key][$subKey];
            }
        } else {
            if ($this->hasAttribute($key)) {
                return $this->attributes[$key];
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
     *
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
     *
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;

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
            /*
             * If a sub key is given, we'll check if
             * it exists in the nested attribute array
             */
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
     * Returns the entry's name. An AD alias for the CN attribute.
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
     * Sets the entry's name.
     *
     * @param string $name
     *
     * @return Entry
     */
    public function setName($name)
    {
        return $this->setAttribute(ActiveDirectory::NAME, $name);
    }

    /**
     * Returns the entry's common name.
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
     * Sets the entry's common name.
     *
     * @param string $name
     *
     * @return Entry
     */
    public function setCommonName($name)
    {
        return $this->setAttribute(ActiveDirectory::COMMON_NAME, $name);
    }

    /**
     * Returns the entry's samaccountname.
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
     * Returns the entry's samaccounttype.
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
     * Returns the entry's `when created` time.
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
     * Returns the entry's `when changed` time.
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
     * Returns the entry's object class.
     *
     * https://msdn.microsoft.com/en-us/library/ms679012(v=vs.85).aspx
     *
     * @return string
     */
    public function getObjectClass()
    {
        return $this->getAttribute(ActiveDirectory::OBJECT_CLASS, 0);
    }

    /**
     * Returns the entry's object SID.
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
     * Returns the entry's primary group ID.
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
     * Returns the entry's instance type.
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
     * Returns the entry's GUID.
     *
     * @return string
     */
    public function getGuid()
    {
        return $this->getAttribute(ActiveDirectory::OBJECT_GUID, 0);
    }

    /**
     * Returns the entry's SID.
     *
     * @return string
     */
    public function getSid()
    {
        return $this->getAttribute(ActiveDirectory::OBJECT_SID, 0);
    }

    /**
     * Returns the entry' max password age.
     *
     * @return string
     */
    public function getMaxPasswordAge()
    {
        return $this->getAttribute(ActiveDirectory::MAX_PASSWORD_AGE, 0);
    }

    /**
     * Returns the objects modifications.
     *
     * @return array
     */
    public function getModifications()
    {
        foreach($this->attributes as $key => $value) {
            /*
             * If the key still exists inside the original attributes,
             * the developer is modifying an attribute.
             */
            if (array_key_exists($key, $this->original)) {
                if (is_array($value)) {
                    if (count(array_diff($value, $this->original[$key])) > 0) {
                        /*
                         * If the value of the set attribute is an array and the differences
                         * are greater than zero, we'll replace the attribute.
                         */
                        $this->setModification($key, LDAP_MODIFY_BATCH_REPLACE, $value);
                    }
                } else if ($value !== $this->original[$key]) {
                    // If the value doesn't equal it's original, we'll replace it.
                    $this->setModification($key, LDAP_MODIFY_BATCH_REPLACE, $value);
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
        /*
         * We need to make sure the values
         * given are always in an array.
         */
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
     *
     * @throws AdldapException
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
        return $this->connection->modifyBatch($this->getDn(), $this->getModifications());
    }

    /**
     * Creates an active directory record.
     *
     * @return bool
     */
    public function create()
    {
        return $this->connection->add($this->getDn(), $this->getAttributes());
    }

    /**
     * Deletes the current entry.
     *
     * @return bool
     *
     * @throws AdldapException
     */
    public function delete()
    {
        $dn = $this->getDn();

        if(is_null($dn) || empty($dn)) {
            $message = 'Unable to delete. The current entry does not have a distinguished name present.';

            throw new AdldapException($message);
        }

        return $this->connection->delete($dn);
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
