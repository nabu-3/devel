<?php

/*  Copyright 2009-2011 Rafael Gutierrez Martinez
 *  Copyright 2012-2013 Welma WEB MKT LABS, S.L.
 *  Copyright 2014-2016 Where Ideas Simply Come True, S.L.
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace nabu\sdk\package;
use nabu\core\CNabuObject;
use nabu\core\exceptions\ENabuCoreException;
use nabu\core\exceptions\ENabuSecurityException;
use nabu\data\customer\CNabuCustomer;
use nabu\data\customer\traits\TNabuCustomerChild;
use nabu\data\lang\CNabuLanguageList;
use nabu\data\security\CNabuRoleList;
use nabu\data\site\CNabuSite;
use nabu\data\site\CNabuSiteList;
use nabu\sdk\builders\xml\CNabuXMLBuilder;
use nabu\sdk\builders\zip\CNabuZipStream;
use nabu\sdk\builders\zip\CNabuZipBuilder;
use nabu\sdk\readers\CNabuAbstractReader;
use nabu\sdk\readers\interfaces\INabuReaderWalker;
use nabu\sdk\readers\zip\CNabuZipReader;
use nabu\xml\lang\CNabuXMLLanguageList;
use nabu\xml\security\CNabuXMLRoleList;
use nabu\xml\site\CNabuXMLSiteList;

/** @var string NABU_PACKAGE_FILE_EXT nabu-3 Package file extension. */
define ('NABU_PACKAGE_FILE_EXT', 'nak');

/**
 * This class manages a package distribution of nabu-3.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.8 Surface
 * @version 3.0.8 Surface
 */
class CNabuPackage extends CNabuObject implements INabuReaderWalker
{
    use TNabuCustomerChild;

    /** @var CNabuLanguageList $nb_language_list List of Languages to be included in this package. */
    private $nb_language_list = null;
    /** @var CNabuRoleList $nb_role_list List of Roles to be included in this package. */
    private $nb_role_list = null;
    /** @var CNabuSiteList $nb_site_list List of Sites to be included in this package. */
    private $nb_site_list = null;

    public function __construct(CNabuCustomer $nb_customer = null)
    {
        parent::__construct();

        if ($nb_customer !== null) {
            $this->setCustomer($nb_customer);
        }
        $this->nb_language_list = new CNabuLanguageList();
        $this->nb_role_list = new CNabuRoleList($nb_customer);
        $this->nb_site_list = new CNabuSiteList($nb_customer);
    }

    public function getWalkerMode(): int
    {
        return self::MODE_DIRECT;
    }

    /**
     * Add a list of Sites and their dependencies to this package instance.
     * @param array $list Mixed list of CNabuList instances and/or Site Ids.
     * @return int Returns the number of Sites added.
     */
    public function addSites(array $list) : int
    {
        $count = $this->getObjectsCount();
        $nb_customer = $this->getCustomer();

        foreach ($list as $item) {
            if (!($item instanceof CNabuSite) &&
                is_numeric($nb_site_id = nb_getMixedValue($item, NABU_SITE_FIELD_ID))
            ) {
                $nb_site = $nb_customer->getSite($nb_site_id);
                if (!$nb_site instanceof CNabuSite) {
                    throw new ENabuCoreException(ENabuCoreException::ERROR_SITE_NOT_FOUND);
                }
            } elseif ($item instanceof CNabuSite) {
                $nb_site = $item;
                if (!$nb_site->validateCustomer($nb_customer)) {
                    throw new ENabuSecurityException(ENabuSecurityException::ERROR_CUSTOMER_NOT_OWNER);
                }
            } else {
                throw new ENabuCoreException(ENabuCoreException::ERROR_SITE_NOT_FOUND);
            }

            $nb_site->refresh(true, true);
            $this->nb_site_list->addItem($nb_site);
            $this->nb_language_list->merge($nb_site->getLanguages(true));
            $this->nb_role_list->merge($nb_site->getRoles(true));
        }

        return $this->getObjectsCount() - $count;
    }

    /**
     * Get the number of root objects availables in the package.
     * @return int Returns the count of objects.
     */
    public function getObjectsCount() : int
    {
        return $this->nb_language_list->getSize() +
               $this->nb_role_list->getSize() +
               $this->nb_site_list->getSize()
        ;
    }

    /**
     * Export the package to a file and save it using the propietary NAK structure.
     * @param string $filename File name to export package.
     */
    public function export(string $filename)
    {
        $zip = new CNabuZipBuilder();
        $unlink_files = array();

        if ($this->getObjectsCount() > 0) {
            $file = new CNabuXMLBuilder();
            $package = new CNabuXMLPackage($this->nb_customer);
            if ($this->nb_language_list->getSize() > 0) {
                $package->setXMLLanguageList(new CNabuXMLLanguageList($this->nb_language_list));
            }
            if ($this->nb_role_list->getSize() > 0) {
                $package->setXMLRoleList(new CNabuXMLRoleList($this->nb_role_list));
            }
            if ($this->nb_site_list->getSize() > 0) {
                $package->setXMLSiteList(new CNabuXMLSiteList($this->nb_site_list));
            }
            $file->addFragment($package);
            $file->create();
            $zip->addFragment(new CNabuZipStream($zip, 'data', 'package.xml', $file));
        }

        $zip->exportToFile($filename);

        foreach ($unlink_files as $filename) {
            unlink($filename);
        }
    }

    /**
     * Import the package from a file and store it in the database.
     * @param string $filename File name of file to import.
     */
    public function import(string $filename)
    {
        $zip = new CNabuZipReader();
        $zip->importFromFile($filename, $this);
    }

    public function processSource(CNabuAbstractReader $reader): int
    {
        $raw_package = $reader->seekFragment('data/package.xml');
        if (strlen($raw_package) > 0) {
            $package = new CNabuXMLPackage($this->nb_customer);
            $package->parse($raw_package);
            $package->save();
        }

        return 1;
    }
}
