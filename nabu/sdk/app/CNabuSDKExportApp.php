<?php

/*  Copyright 2009-2011 Rafael Gutierrez Martinez
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

namespace nabu\sdk\app;
use nabu\cli\app\CNabuCLIApplication;
use nabu\data\customer\CNabuCustomer;
use nabu\data\site\CNabuSite;
use nabu\data\site\CNabuSiteList;
use nabu\sdk\package\CNabuPackage;

/**
 * Class based in CLI Application to manage SDK Export tool from the command line.
 * This class works coordinated with the bin file nabu-export.sh
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.8 Surface
 * @version 3.0.8 Surface
 * @package \nabu\sdk\app
 */
class CNabuSDKExportApp extends CNabuCLIApplication
{
    /** @var CNabuCustomer $nb_customer Customer that owns exported objects. */
    private $nb_customer = null;
    /** @var CNabuSiteList $nb_site_list List of sites to export. */
    private $nb_site_list = null;
    /** @var string export_filename File name to save export. */
    private $export_filename = 'nabu-3-dump.nak';

    public function prepareEnvironment()
    {
        $nb_customer_id = nbCLICheckOption('c', 'customer', ':', true);

        if (is_numeric($nb_customer_id)) {
            $nb_customer = new CNabuCustomer($nb_customer_id);
            if ($nb_customer->isFetched()) {
                $this->nb_customer = $nb_customer;
            }
        }

        if ($this->nb_customer instanceof CNabuCustomer) {
            echo "Customer: $nb_customer_id\n";
            $nb_sites_list = nbCLICheckOption('s', 'sites', '::');
            if (strlen($nb_sites_list) > 0) {
                $this->nb_site_list = new CNabuSiteList($this->nb_customer);
                foreach (explode(',', $nb_sites_list) as $id) {
                    if (is_numeric($id) && ($nb_site = $this->nb_customer->getSite($id)) instanceof CNabuSite) {
                        $this->nb_site_list->addItem($nb_site);
                        echo "Site [$id] added to export.\n";
                    } elseif (is_string($id) && ($nb_site = $this->nb_customer->getSiteByKey($id)) instanceof CNabuSite) {
                        $this->nb_site_list->addItem($nb_site);
                        echo "Site [$id] added to export.\n";
                    } else {
                        echo "Invalid site identified by [$id].\nBe sure that the Site exists and is owned by selected Customer.\n";
                    }
                }
            }
            if (($count = count($this->arguments)) > 2 &&
                !nb_strStartsWith($this->arguments[$count - 1], '-')
            ) {
                $this->export_filename = $this->arguments[$count - 1];
            }
        } else {
            echo "Invalid customer [$nb_customer_id]";
        }
    }

    public function run()
    {
        if ($this->nb_site_list !== null && $this->nb_site_list->getSize() > 0) {
            echo "Begin export...\n";
            $package = new CNabuPackage($this->nb_customer);
            echo "    ...adding Sites\n";
            $package->addSites($this->nb_site_list->getItems());
            echo "    ...exporting to package file $this->export_filename\n";
            $package->export($this->export_filename);
            echo "    ...end export.\n";
        }
        return 0;
    }
}
