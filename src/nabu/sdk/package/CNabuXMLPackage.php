<?php

/** @license
 *  Copyright 2009-2011 Rafael Gutierrez Martinez
 *  Copyright 2012-2013 Welma WEB MKT LABS, S.L.
 *  Copyright 2014-2016 Where Ideas Simply Come True, S.L.
 *  Copyright 2017 nabu-3 Group
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
use SimpleXMLElement;
use nabu\data\CNabuDataObject;
use nabu\data\customer\CNabuCustomer;
use nabu\data\lang\CNabuLanguageList;
use nabu\data\security\CNabuRoleList;
use nabu\db\CNabuDBObject;
use nabu\xml\CNabuXMLDataObject;
use nabu\xml\lang\CNabuXMLLanguageList;
use nabu\xml\security\CNabuXMLRoleList;
use nabu\xml\site\CNabuXMLSiteList;

/**
 * This class manages the XML nabuPackage root in package distributions of nabu-3.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.8 Surface
 * @version 3.0.8 Surface
 */
class CNabuXMLPackage extends CNabuXMLDataObject
{
    /** @var CNabuXMLRoleList $xml_role_list List of Roles in the package. */
    private $xml_role_list = null;
    /** @var CNabuXMLLanguageList $xml_language_list List of Languages in the package. */
    private $xml_language_list = null;
    /** @var CNabuXMLSiteList $xml_site_list List of Sites in the package. */
    private $xml_site_list = null;

    public function __construct(CNabuCustomer $nb_customer = null)
    {
        parent::__construct($nb_customer);
    }

    protected static function getTagName(): string
    {
        return 'nabuPackage';
    }

    protected function getAttributes(SimpleXMLElement $element)
    {
    }

    protected function setAttributes(SimpleXMLElement $element)
    {
        $element->addAttribute('customer', $this->nb_data_object->grantHash(true));
    }

    protected function getChilds(SimpleXMLElement $element)
    {
        $children = $element->children();

        if (isset($children->languages)) {
            $this->getXMLLanguageList($children->languages);
        }

        if (isset($children->roles)) {
            $this->getXMLRoleList($children->roles);
        }
    }

    protected function setChilds(SimpleXMLElement $element)
    {
        if ($this->xml_language_list !== null) {
            $this->xml_language_list->build($element);
        }

        if ($this->xml_role_list !== null) {
            $this->xml_role_list->build($element);
        }

        if ($this->xml_site_list !== null) {
            $this->xml_site_list->build($element);
        }
    }

    protected function locateDataObject(SimpleXMLElement $element, CNabuDataObject $data_parent = null) : bool
    {
        $retval = false;

        $retval = (
            isset($element->attributes()->customer) &&
            is_string($guid = (string)$element->attributes()->customer) &&
            ($this->nb_data_object = CNabuCustomer::findByHash($guid)) instanceof CNabuCustomer
        );

        return $retval;
    }

    /**
     * Gets the XML Language List from a XML branch.
     * @param SimpleXMLElement $element The XML Element where the list is placed.
     */
    private function getXMLLanguageList(SimpleXMLElement $element)
    {
        $this->xml_language_list = new CNabuXMLLanguageList(new CNabuLanguageList());
        $this->xml_language_list->collect($element);
    }

    /**
     * Sets the XML Language List in the package.
     * @param CNabuXMLLanguageList $xml_language_list XML Language List.
     * @return CNabuXMLPackage Returns self pointer to grant chained setters call.
     */
    public function setXMLLanguageList(CNabuXMLLanguageList $xml_language_list) : CNabuXMLPackage
    {
        $this->xml_language_list = $xml_language_list;

        return $this;
    }

    /**
     * Gets the XML Role List from a XML branch.
     * @param SimpleXMLElement $element The XML Element where the list is placed.
     */
    private function getXMLRoleList(SimpleXMLElement $element)
    {
        $this->xml_role_list = new CNabuXMLRoleList(new CNabuRoleList($this->nb_data_object));
        $this->xml_role_list->collect($element);
    }

    /**
     * Sets the XML Role List in the package.
     * @param CNabuXMLRoleList $xml_role_list XML Role List.
     * @return CNabuXMLPackage Returns self pointer to grant chained setters call.
     */
    public function setXMLRoleList(CNabuXMLRoleList $xml_role_list) : CNabuXMLPackage
    {
        $this->xml_role_list = $xml_role_list;

        return $this;
    }

    /**
     * Sets the XML Site List in the package.
     * @param CNabuXMLSiteList $xml_site_list XML Site List.
     * @return CNabuXMLPackage Returns self pointer to grant chained setters call.
     */
    public function setXMLSiteList(CNabuXMLSiteList $xml_site_list) : CNabuXMLPackage
    {
        $this->xml_site_list = $xml_site_list;

        return $this;
    }

    public function save()
    {
    }
}
