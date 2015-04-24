<?php

namespace Adldap\Classes;

use Adldap\Objects\Folder;
use Adldap\Adldap;

/**
 * Ldap Folder / OU management.
 *
 * Class AdldapFolders
 */
class AdldapFolders extends AdldapBase
{
    /**
     * Delete a distinguished name from Active Directory.
     * You should never need to call this yourself, just use
     * the wrapper functions user()->delete($username) and contact()->delete($name).
     *
     * @param string $dn The distinguished name to delete
     *
     * @return bool
     */
    public function delete($dn)
    {
        $this->adldap->utilities()->validateNotNull('Distinguished Name [dn]', $dn);

        return $this->connection->delete($dn);
    }

    /**
     * Returns a folder listing for a specific OU.
     * See http://adldap.sourceforge.net/wiki/doku.php?id=api_folder_functions.
     *
     * If folderName is set to NULL this will list the root, strongly recommended
     * to set $recursive to false in that instance!
     *
     * @param array  $folders
     * @param string $dnType
     * @param null   $recursive
     * @param null   $type
     *
     * @return array|bool
     */
    public function listing($folders = [], $dnType = Adldap::ADLDAP_FOLDER, $recursive = null, $type = null)
    {
        $search = $this->adldap->search();

        if (is_array($folders) && count($folders) > 0) {
            /*
             * Reverse the folder array so it's more
             * akin to navigating a folder structure
             */
            $folders = array_reverse($folders);

            /*
             * Get the combined OU string for the search.
             *
             * ex. OU=Users,OU=Acme
             */
            $ou = $dnType.'='.implode(','.$dnType.'=', $folders);

            $search->where('distinguishedname', '!', $ou.$this->adldap->getBaseDn());

            // Apply the OU to the base DN
            $dn = $ou.','.$this->adldap->getBaseDn();

            $search->setDn($dn);
        } else {
            $search->where('distinguishedname', '!', $this->adldap->getBaseDn());
        }

        if ($type === null) {
            $search->where('objectClass', '*');
        } else {
            $search->where('objectClass', '=', $type);
        }

        if ($recursive === false) {
            $search->recursive(false);
        }

        return $search->get();
    }

    /**
     * Create an organizational unit.
     *
     * @param array $attributes Default attributes of the ou
     *
     * @return bool|string
     */
    public function create(array $attributes)
    {
        $folder = new Folder($attributes);

        $folder->validateRequired();

        $folder->setAttribute('container', array_reverse($folder->getAttribute('container')));

        $add = [];

        $add['objectClass'] = 'organizationalUnit';
        $add['OU'] = $folder->getAttribute('ou_name');

        $containers = 'OU='.implode(',OU=', $folder->getAttribute('container'));

        $dn = 'OU='.$add['OU'].', '.$containers.$this->adldap->getBaseDn();

        return $this->connection->add($dn, $add);
    }
}
