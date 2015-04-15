<?php

namespace Adldap\Classes;

use Adldap\Objects\Folder;
use Adldap\Adldap;

/**
 * Ldap Folder / OU management
 *
 * Class AdldapFolders
 * @package Adldap\classes
 */
class AdldapFolders extends AdldapBase
{
    /**
     * Delete a distinguished name from Active Directory.
     * You should never need to call this yourself, just use the wrapper functions user_delete and contact_delete
     *
     * @param string $dn The distinguished name to delete
     * @return bool
     */
    public function delete($dn)
    {
        return $this->connection->delete($dn);
    }

    /**
     * Returns a folder listing for a specific OU
     * See http://adldap.sourceforge.net/wiki/doku.php?id=api_folder_functions
     *
     * If folderName is set to NULL this will list the root, strongly recommended
     * to set $recursive to false in that instance!
     *
     * @param null $folderName An array to the OU you wish to list
     * @param string $dnType The type of record to list.  This can be ADLDAP_FOLDER or ADLDAP_CONTAINER.
     * @param null $recursive
     * @param null $type
     * @return array|bool
     */
    public function listing($folderName = NULL, $dnType = Adldap::ADLDAP_FOLDER, $recursive = NULL, $type = NULL)
    {
        $this->adldap->utilities()->validateLdapIsBound();

        if ($recursive === NULL) $recursive = $this->adldap->getRecursiveGroups(); //use the default option if they haven't set it

        $filter = '(&';

        if ($type !== NULL)
        {
            $filter .= $this->typeToObjectClassString($type);
        } else
        {
            $filter .= '(objectClass=*)';   
        }

        /*
         * If the folder name is null then we will search the root level of AD.
         * This requires us to not have an OU= part, just the base_dn
         */
        $searchOu = $this->adldap->getBaseDn();

        if (is_array($folderName))
        {
            $ou = $dnType . "=" . implode("," . $dnType . "=", $folderName);

            $filter .= '(!(distinguishedname=' . $ou . ',' . $this->adldap->getBaseDn() . ')))';

            $searchOu = $ou . ',' . $this->adldap->getBaseDn();
        }
        else
        {
            $filter .= '(!(distinguishedname=' . $this->adldap->getBaseDn() . ')))';
        }

        $fields = array(
            'objectclass',
            'distinguishedname',
            'samaccountname',
            'description',
        );

        if ($recursive === true)
        {
            $results = $this->connection->search($searchOu, $filter, $fields);
        }
        else
        {
            $results = $this->connection->listing($searchOu, $filter, $fields);
        }

        $entries = $this->connection->getEntries($results);

        if (is_array($entries)) return $entries;

        return false;
    }

    /**
     * Create an organizational unit.
     *
     * @param array $attributes Default attributes of the ou
     * @return bool|string
     */
    public function create(array $attributes)
    {
        $folder = new Folder($attributes);

        $folder->validateRequired();

        $folder->setAttribute('container', array_reverse($folder->getAttribute('container')));

        $add = array();

        $add["objectClass"] = "organizationalUnit";
        $add["OU"] = $folder->getAttribute('ou_name');

        $containers = "OU=" . implode(",OU=", $folder->getAttribute("container"));

        $dn = "OU=" . $add["OU"] . ", " . $containers . $this->adldap->getBaseDn();

        return $this->connection->add($dn, $add);
    }

    /**
     * Converts a folder type string into a object class
     * filter string compatible with LDAP.
     *
     * @param string$type
     * @return string
     */
    private function typeToObjectClassString($type)
    {
        $filter = '';

        switch ($type)
        {
            case 'contact':
                $filter .= '(objectClass=contact)';
                break;
            case 'computer':
                $filter .= '(objectClass=computer)';
                break;
            case 'group':
                $filter .= '(objectClass=group)';
                break;
            case 'folder':
                $filter .= '(objectClass=organizationalUnit)';
                break;
            case 'container':
                $filter .= '(objectClass=container)';
                break;
            case 'domain':
                $filter .= '(objectClass=builtinDomain)';
                break;
            default:
                $filter .= '(objectClass=user)';
                break;
        }

        return $filter;
    }
}
