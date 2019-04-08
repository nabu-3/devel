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

use \nabu\core\CNabuEngine;

/**
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @version 3.0.0 Surface
 */

require_once 'common.php';
require_once 'cli.php';
require_once 'db2class.php';

$nb_engine = CNabuEngine::getEngine();
$mysql_connector = $nb_engine->getMainDB();

if (!$mysql_connector->isConnected()) {
    die("Database not found or not connected\n");
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

echo "\nWe go to create all required base classes of nabu-3.\n";
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
    'sku' => 'SKU',
    'vr' => 'VR',
    'signin' => 'SignIn',
    'mimetype' => 'MIMEType'
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
createXML(
    'nb_language',
    'nabu\xml\lang\base',
    'CNabuXMLLanguageBase',
    'Language',
    'language',
    array(
        'nb_language_type' => 'type',
        'nb_language_enabled' => 'enabled',
        'nb_language_ISO639_1' => 'ISO639v1',
        'nb_language_ISO639_2' => 'ISO639v2',
        'nb_language_is_api' => 'isAPI',
        'nb_language_default_country_code' => 'defaultCountryCode',
        'nb_language_flag_url' => 'flagURL'
    ),
    array(
        'nb_language_name' => 'name'
    ),
    null,
    null,
    'nabu\data\lang',
    'CNabuLanguage',
    true,
    $dictionary,
    '3.0.12 Surface'
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
createXML(
    'nb_role',
    'nabu\xml\security\base',
    'CNabuXMLRoleBase',
    'Role',
    'role',
    array(
        'nb_role_key' => 'key',
        'nb_role_root' => 'root'
    ),
    null,
    null,
    array(
        'nb_role_lang_name' => 'name'
    ),
    'nabu\data\security',
    'CNabuRole',
    true,
    $dictionary,
    '3.0.12 Surface'
);
createEntity(
    'nb_user',
    'nabu\data\security\base',
    'CNabuUserBase',
    'User',
    true,
    $dictionary
);
createEntity(
    'nb_user_group_type',
    'nabu\data\security\base',
    'CNabuUserGroupTypeBase',
    'User Group Type',
    true,
    $dictionary
);
createEntity(
    'nb_user_group',
    'nabu\data\security\base',
    'CNabuUserGroupBase',
    'User Group',
    true,
    $dictionary
);
createEntity(
    'nb_user_group_member',
    'nabu\data\security\base',
    'CNabuUserGroupMemberBase',
    'User Group Member',
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
    $dictionary,
    '3.0.0 Surface'
);
createXML(
    'nb_site',
    'nabu\xml\site\base',
    'CNabuXMLSiteBase',
    'Site',
    'site',
    array(
        'nb_site_key' => 'key',
        'nb_site_http_support' => 'http_support',
        'nb_site_https_support' => 'https_support'
    ),
    null,
    array(
        'nb_site_lang_enabled' => 'enabled',
        'nb_site_lang_translation_status' => 'status',
        'nb_site_lang_order' => 'order',
        'nb_site_lang_editable' => 'editable'
    ),
    array(
        'nb_site_lang_name' => 'name'
    ),
    'nabu\data\site',
    'CNabuSite',
    true,
    $dictionary,
    '3.0.12 Surface'
);
createEntity(
    'nb_site_module',
    'nabu\data\site\base',
    'CNabuSiteModuleBase',
    'Site Module',
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
createXML(
    'nb_site_target',
    'nabu\xml\site\base',
    'CNabuXMLSiteTargetBase',
    'Site Target',
    'target',
    array(
        'nb_site_target_key' => 'key',
        'nb_site_target_order' => 'order',
        'nb_site_target_begin_date' => 'beginDate',
        'nb_site_target_plugin_name' => 'plugin',
        'nb_site_target_php_trace' => 'trace',
        'nb_site_target_use_commerce' => 'useCommerce'
    ),
    array(
        'nb_site_target_attributes' => 'attributes'
    ),
    array(
        'nb_site_target_lang_main_image' => 'image'
    ),
    array(
        'nb_site_target_lang_title' => 'title',
        'nb_site_target_lang_subtitle' => 'subtitle',
        'nb_site_target_lang_opening' => 'opening',
        'nb_site_target_lang_content' => 'content',
        'nb_site_target_lang_footer' => 'footer',
        'nb_site_target_lang_aside' => 'aside'
    ),
    'nabu\data\site',
    'CNabuSiteTarget',
    true,
    $dictionary,
    '3.0.12 Surface'
);
createEntity(
    'nb_site_target_section',
    'nabu\data\site\base',
    'CNabuSiteTargetSectionBase',
    'Site Target Section',
    true,
    $dictionary
);
createXML(
    'nb_site_target_section',
    'nabu\xml\site\base',
    'CNabuXMLSiteTargetSectionBase',
    'Site Target Section',
    'section',
    array(
        'nb_site_target_section_key' => 'key',
        'nb_site_target_section_order' => 'order',
        'nb_site_target_section_anchor' => 'anchor',
        'nb_site_target_section_css_class' => 'cssClass'
    ),
    null,
    array(
        'nb_site_target_section_main_image' => 'image'
    ),
    array(
        'nb_site_target_section_lang_title' => 'title',
        'nb_site_target_section_lang_subtitle' => 'subtitle',
        'nb_site_target_section_lang_opening' => 'opening',
        'nb_site_target_section_lang_content' => 'content',
        'nb_site_target_section_lang_footer' => 'footer',
        'nb_site_target_section_lang_aside' => 'aside'
    ),
    'nabu\data\site',
    'CNabuSiteTargetSection',
    true,
    $dictionary,
    '3.0.12 Surface'
);
createEntity(
    'nb_site_target_cta',
    'nabu\data\site\base',
    'CNabuSiteTargetCTABase',
    'Site Target CTA',
    true,
    $dictionary
);
createXML(
    'nb_site_target_cta',
    'nabu\xml\site\base',
    'CNabuXMLSiteTargetCTABase',
    'Site Target CTA',
    'cta',
    array(
        'nb_site_target_cta_key' => 'key',
        'nb_site_target_cta_order' => 'order',
        'nb_site_target_cta_css_class' => 'cssClass'
    ),
    null,
    array(
        'nb_site_target_cta_lang_image' => 'image'
    ),
    array(
        'nb_site_target_cta_lang_title' => 'title',
        'nb_site_target_cta_lang_alternate' => 'alt',
        'nb_site_target_cta_lang_anchor_text' => 'anchorText'
    ),
    'nabu\data\site',
    'CNabuSiteTargetCTA',
    true,
    $dictionary,
    '3.0.12 Surface'
);
createEntity(
    'nb_site_target_cta_role',
    'nabu\data\site\base',
    'CNabuSiteTargetCTARoleBase',
    'Site Target CTA Role',
    true,
    $dictionary
);
createXML(
    'nb_site_target_cta_role',
    'nabu\xml\site\base',
    'CNabuXMLSiteTargetCTARoleBase',
    'Site Target CTA Role',
    'role',
    array(
        'nb_site_target_cta_role_zone' => 'zone'
    ),
    null,
    null,
    null,
    'nabu\data\site',
    'CNabuSiteTargetCTARole',
    true,
    $dictionary,
    '3.0.12 Surface'
);
createEntity(
    'nb_site_target_medioteca',
    'nabu\data\site\base',
    'CNabuSiteTargetMediotecaBase',
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
createXML(
    'nb_site_map',
    'nabu\xml\site\base',
    'CNabuXMLSiteMapBase',
    'Site Map',
    'map',
    array(
        'nb_site_map_key' => 'key',
        'nb_site_map_order' => 'order',
        'nb_site_map_customer_required' => 'customerRequired',
        'nb_site_map_open_popup' => 'openPopup',
        'nb_site_map_visible' => 'visible',
        'nb_site_map_separator' => 'separator',
    ),
    null,
    array(
        'nb_site_map_lang_translation_status' => 'status',
        'nb_site_map_lang_image' => 'image'
    ),
    array(
        'nb_site_map_lang_title' => 'title',
        'nb_site_map_lang_subtitle' => 'subtitle',
        'nb_site_map_lang_content' => 'content'
    ),
    'nabu\data\site',
    'CNabuSiteMap',
    true,
    $dictionary,
    '3.0.12 Surface'
);
createEntity(
    'nb_site_map_role',
    'nabu\data\site\base',
    'CNabuSiteMapRoleBase',
    'Site Map Role',
    true,
    $dictionary
);
createXML(
    'nb_site_map_role',
    'nabu\xml\site\base',
    'CNabuXMLSiteMapRoleBase',
    'Site Map Role',
    'role',
    array(
        'nb_site_map_role_zone' => 'zone'
    ),
    null,
    null,
    null,
    'nabu\data\site',
    'CNabuSiteMapRole',
    true,
    $dictionary,
    '3.0.12 Surface'
);
createEntity(
    'nb_site_static_content',
    'nabu\data\site\base',
    'CNabuSiteStaticContentBase',
    'Site Static Content',
    true,
    $dictionary
);
createXML(
    'nb_site_static_content',
    'nabu\xml\site\base',
    'CNabuXMLSiteStaticContentBase',
    'Site Static Content',
    'staticContent',
    array(
        'nb_site_static_content_key' => 'key',
        'nb_site_static_content_type' => 'type',
        'nb_site_static_content_use_alternative' => 'alternative'
    ),
    array(
        'nb_site_static_content_notes' => 'notes'
    ),
    null,
    array(
        'nb_site_static_content_lang_text' => 'text'
    ),
    'nabu\data\site',
    'CNabuSiteStaticContent',
    true,
    $dictionary,
    '3.0.12 Surface'
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
createEntity(
    'nb_site_visual_editor_item',
    'nabu\visual\site\base',
    'CNabuSiteVisualEditorItemBase',
    'Site Visual Editor Item',
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

// Messaging: nabu\data\messaging\base
createEntity(
    'nb_messaging',
    'nabu\data\messaging\base',
    'CNabuMessagingBase',
    'Messaging',
    true,
    $dictionary
);
createEntity(
    'nb_messaging_service',
    'nabu\data\messaging\base',
    'CNabuMessagingServiceBase',
    'Messaging Service',
    true,
    $dictionary
);
createEntity(
    'nb_messaging_template',
    'nabu\data\messaging\base',
    'CNabuMessagingTemplateBase',
    'Messaging Template',
    true,
    $dictionary
);
createEntity(
    'nb_messaging_service_template',
    'nabu\data\messaging\base',
    'CNabuMessagingServiceTemplateBase',
    'Messaging Service Template',
    true,
    $dictionary
);
createEntity(
    'nb_messaging_service_stack',
    'nabu\data\messaging\base',
    'CNabuMessagingServiceStackBase',
    'Messaging Service Stack',
    true,
    $dictionary
);
createEntity(
    'nb_messaging_service_stack_attachment',
    'nabu\data\messaging\base',
    'CNabuMessagingServiceStackAttachmentBase',
    'Messaging Service Stack Attachment',
    true,
    $dictionary
);

// iContact: nabu\data\icontact\base
createEntity(
    'nb_icontact',
    'nabu\data\icontact\base',
    'CNabuIContactBase',
    'iContact',
    true,
    $dictionary
);
createEntity(
    'nb_icontact_prospect',
    'nabu\data\icontact\base',
    'CNabuIContactProspectBase',
    'iContact Prospect',
    true,
    $dictionary
);
createEntity(
    'nb_icontact_prospect_attachment',
    'nabu\data\icontact\base',
    'CNabuIContactProspectAttachmentBase',
    'iContact Prospect Attachment',
    true,
    $dictionary
);
createEntity(
    'nb_icontact_prospect_diary',
    'nabu\data\icontact\base',
    'CNabuIContactProspectDiaryBase',
    'iContact Prospect Diary',
    true,
    $dictionary
);
createEntity(
    'nb_icontact_prospect_status_type',
    'nabu\data\icontact\base',
    'CNabuIContactProspectStatusTypeBase',
    'iContact Prospect Status Type',
    true,
    $dictionary
);

// Project: nabu\data\project\base
createEntity(
    'nb_project',
    'nabu\data\project\base',
    'CNabuProjectBase',
    'Project',
    true,
    $dictionary
);
createEntity(
    'nb_project_version',
    'nabu\data\project\base',
    'CNabuProjectVersionBase',
    'Project Version',
    true,
    $dictionary
);
