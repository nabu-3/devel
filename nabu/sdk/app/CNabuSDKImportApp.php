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

namespace nabu\sdk\app;
use nabu\cli\app\CNabuCLIApplication;
use nabu\data\customer\CNabuCustomer;
use nabu\sdk\package\CNabuPackage;

/**
 * Class based in CLI Application to manage SDK Import tool from the command line.
 * This class works coordinated with the bin file nabu-import.sh
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.8 Surface
 * @version 3.0.8 Surface
 * @package \nabu\sdk\app
 */
class CNabuSDKImportApp extends CNabuCLIApplication
{
    /** @var CNabuCustomer $nb_customer Customer that owns imported objects. */
    private $nb_customer = null;
    /** @var string import_filename File name to import. */
    private $import_filename = 'nabu-3-dump.nak';

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
            if (($count = count($this->arguments)) > 1 &&
                !nb_strStartsWith($this->arguments[$count - 1], '-')
            ) {
                $this->import_filename = $this->arguments[$count - 1];
            }
        }
    }

    public function run()
    {
        if (is_string($this->import_filename) &&
            file_exists($this->import_filename) &&
            $this->nb_customer instanceof CNabuCustomer
        ) {
            $package = new CNabuPackage($this->nb_customer);
            $package->import($this->import_filename);
        }

        return true;
    }
}
