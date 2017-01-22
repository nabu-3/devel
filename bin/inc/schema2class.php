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

use \nabu\core\CNabuEngine;

/**
 * @author Rafael Gutierrez <rgutierrez@wiscot.com>
 * @version 3.0.0 Surface
 */

require_once 'common.php';
require_once 'cli.php';
require_once 'db2class.php';

$nb_engine = CNabuEngine::getEngine();
$mysql_connector = $nb_engine->getMainDB();

if (!$mysql_connector->isConnected()) {
    die ("Database not found or not connected\n");
}

$creator_name = nbCLICheckOption('a', 'author', ':', null, false);
if ($creator_name === null) {
    $creator_name = nbCLIInput('Author name', false);
}

$creator_email = nbCLICheckOption('e', 'author-email', ':', null, false);
if ($creator_email === null) {
    $creator_email = nbCLIInput('Author email', false);
}

$table_schema = nbCLICheckOption('s', 'schema', ':', null, false);
if ($table_schema === null) {
    $table_schema = nbCLIInput('Schema', false);
}

$class_path = nbCLICheckOption('t', 'path', ':', null, false);
if ($class_path === null) {
    $class_path = nbCLIInput('Base path: ', false);
}

echo "\nWe go to create all required base classes of Nabu-3.\n";
echo "If you continue you will loose all changes made in previous files.\n\n";

nbCLICheckContinue();

$dictionary = array(
    'os' => 'OS',
    'ip' => 'IP',
    'wmr' => 'WMR',
    'http' => 'HTTP',
    'https' => 'HTTPS',
    'vhosts' => 'VirtualHosts',
    'vhost' => 'VirtualHost',
    'phputils' => 'PHPUtils',
    'php' => 'PHP',
    'wgeo' => 'WGEO',
    'url' => 'URL',
    'uri' => 'URI',
    'iso639' => 'ISO639',
    'ISO639' => 'ISO639',
    'css' => 'CSS',
    'ssl' => 'SSL',
    'passwd' => 'Password',
    'zip' => 'ZIP',
    'awstats' => 'AWStats',
    'wsearch' => 'WSearch',
    'cta' => 'CTA',
    'sku' => 'SKU'
);

// Language: nabu\data\lang
createEntity(
    'nb_language',
    'nabu\data\lang\base',
    'CNabuLanguageBase',
    'Language',
    true,
    $dictionary
);

// Customer: nabu\data\customer\base
createEntity(
    'nb_customer',
    'nabu\data\customer\base',
    'CNabuCustomerBase',
    'Customer',
    true,
    $dictionary
);
createEntity(
    'nb_customer_user',
    'nabu\data\customer\base',
    'CNabuCustomerUserBase',
    'Customer User',
    true,
    $dictionary
);

// Cluster: nabu\data\cluster\base
createEntity(
    'nb_ip',
    'nabu\data\cluster\base',
    'CNabuIPBase',
    'IP',
    true,
    $dictionary
);
createEntity(
    'nb_server',
    'nabu\data\cluster\base',
    'CNabuServerBase',
    'Server',
    true,
    $dictionary
);
createEntity(
    'nb_server_host',
    'nabu\data\cluster\base',
    'CNabuServerHostBase',
    'ServerHost',
    true,
    $dictionary
);
createEntity(
    'nb_cluster_user_group',
    'nabu\data\cluster\base',
    'CNabuClusterUserGroupBase',
    'Cluster User Group',
    true,
    $dictionary
);
createEntity(
    'nb_cluster_user',
    'nabu\data\cluster\base',
    'CNabuClusterUserBase',
    'Cluster User',
    true,
    $dictionary
);
createEntity(
    'nb_cluster_group',
    'nabu\data\cluster\base',
    'CNabuClusterGroupBase',
    'Cluster Group',
    true,
    $dictionary
);
createEntity(
    'nb_cluster_group_service',
    'nabu\data\cluster\base',
    'CNabuClusterGroupServiceBase',
    'Cluster Group Service',
    true,
    $dictionary
);

// Security: nabu\data\security\base
createEntity(
    'nb_role',
    'nabu\data\security\base',
    'CNabuRoleBase',
    'Role',
    true,
    $dictionary
);
createEntity(
    'nb_user',
    'nabu\data\security\base',
    'CNabuUserBase',
    'User',
    true,
    $dictionary
);

// Domains: nabu\data\domain\base
createEntity(
    'nb_domain_zone',
    'nabu\data\domain\base',
    'CNabuDomainZoneBase',
    'Domain Zone',
    true,
    $dictionary
);
createEntity(
    'nb_domain_zone_host',
    'nabu\data\domain\base',
    'CNabuDomainZoneHostBase',
    'Domain Zone Host',
    true,
    $dictionary
);

// Sites: nabu\data\site\base
createEntity(
    'nb_site',
    'nabu\data\site\base',
    'CNabuSiteBase',
    'Site',
    true,
    $dictionary
);
createEntity(
    'nb_site_alias',
    'nabu\data\site\base',
    'CNabuSiteAliasBase',
    'Site Alias',
    true,
    $dictionary
);
createEntity(
    'nb_site_alias_service',
    'nabu\data\site\base',
    'CNabuSiteAliasServiceBase',
    'Site Alias Service',
    true,
    $dictionary
);
createEntity(
    'nb_site_alias_host',
    'nabu\data\site\base',
    'CNabuSiteAliasHostBase',
    'Site Alias Host',
    true,
    $dictionary
);
createEntity(
    'nb_site_medioteca',
    'nabu\data\site\base',
    'CNabuSiteMediotecaBase',
    'Site Medioteca',
    true,
    $dictionary
);
createEntity(
    'nb_site_target',
    'nabu\data\site\base',
    'CNabuSiteTargetBase',
    'Site Target',
    true,
    $dictionary
);
createEntity(
    'nb_site_target_section',
    'nabu\data\site\base',
    'CNabuSiteTargetSectionBase',
    'Site Target Section',
    true,
    $dictionary
);
createEntity(
    'nb_site_target_cta',
    'nabu\data\site\base',
    'CNabuSiteTargetCTABase',
    'Site Target CTA',
    true,
    $dictionary
);
createEntity(
    'nb_site_target_cta_role',
    'nabu\data\site\base',
    'CNabuSiteTargetCTARoleBase',
    'Site Target CTA Role',
    true,
    $dictionary
);
createEntity(
    'nb_site_target_medioteca',
    'nabu\data\site\base',
    'CNabuSiteTargetMedioteca',
    'Site Target Medioteca',
    true,
    $dictionary
);
createEntity(
    'nb_site_map',
    'nabu\data\site\base',
    'CNabuSiteMapBase',
    'Site Map',
    true,
    $dictionary
);
createEntity(
    'nb_site_map_role',
    'nabu\data\site\base',
    'CNabuSiteMapRoleBase',
    'Site Map Role',
    true,
    $dictionary
);
createEntity(
    'nb_site_static_content',
    'nabu\data\site\base',
    'CNabuSiteStaticContentBase',
    'Site Static Content',
    true,
    $dictionary
);
createEntity(
    'nb_site_role',
    'nabu\data\site\base',
    'CNabuSiteRoleBase',
    'Site Role',
    true,
    $dictionary
);
createEntity(
    'nb_site_user',
    'nabu\data\site\base',
    'CNabuSiteUserBase',
    'Site User',
    true,
    $dictionary
);

// Commerces: nabu\data\commerce\base
createEntity(
    'nb_commerce',
    'nabu\data\commerce\base',
    'CNabuCommerceBase',
    'Commerce',
    true,
    $dictionary
);
createEntity(
    'nb_commerce_product_category',
    'nabu\data\commerce\base',
    'CNabuCommerceProductCategoryBase',
    'Commerce Product Category',
    true,
    $dictionary
);
createEntity(
    'nb_commerce_product',
    'nabu\data\commerce\base',
    'CNabuCommerceProductBase',
    'Commerce Product',
    true,
    $dictionary
);

// Catalogs: nabu\data\catalog\base
createEntity(
    'nb_catalog',
    'nabu\data\catalog\base',
    'CNabuCatalogBase',
    'Catalog',
    true,
    $dictionary
);
createEntity(
    'nb_catalog_taxonomy',
    'nabu\data\catalog\base',
    'CNabuCatalogTaxonomyBase',
    'Catalog Taxonomy',
    true,
    $dictionary
);
createEntity(
    'nb_catalog_item',
    'nabu\data\catalog\base',
    'CNabuCatalogItemBase',
    'Catalog Item',
    true,
    $dictionary
);
createEntity(
    'nb_catalog_tag',
    'nabu\data\catalog\base',
    'CNabuCatalogTagBase',
    'Catalog Tag',
    true,
    $dictionary
);
createEntity(
    'nb_catalog_item_tag',
    'nabu\data\catalog\base',
    'CNabuCatalogItemTagBase',
    'Catalog Item Tag',
    true,
    $dictionary
);

// Mediotecas: nabu\data\medioteca\base
createEntity(
    'nb_medioteca_type',
    'nabu\data\medioteca\base',
    'CNabuMediotecaTypeBase',
    'Medioteca Type',
    true,
    $dictionary
);
createEntity(
    'nb_medioteca',
    'nabu\data\medioteca\base',
    'CNabuMediotecaBase',
    'Medioteca',
    true,
    $dictionary
);
createEntity(
    'nb_medioteca_item',
    'nabu\data\medioteca\base',
    'CNabuMediotecaItemBase',
    'Medioteca Item',
    true,
    $dictionary
);
