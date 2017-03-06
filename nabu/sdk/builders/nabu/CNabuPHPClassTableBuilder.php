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

namespace nabu\sdk\builders\nabu;
use nabu\sdk\builders\CNabuAbstractBuilder;
use nabu\sdk\builders\nabu\base\CNabuPHPClassTableAbstractBuilder;
use nabu\sdk\builders\php\CNabuPHPMethodBuilder;
use nabu\sdk\builders\php\CNabuPHPConstructorBuilder;
use nabu\core\exceptions\ENabuCoreException;
use nabu\db\interfaces\INabuDBConnector;
use nabu\db\interfaces\INabuDBSyntaxBuilder;

/**
 * This class is specialized in create a class skeleton code file from a table
 * that exists in the database. Created class is fulfilled with a large set
 * of methods to use it as "Nabu Style" classes.
 * Normally, after create a class we recommend to subclass created classes to
 * extend functionalities, allowing to you to recreate base class each time
 * that your table changes or when Nabu will increase the set of methods.
 * @author Rafael Gutierrez <rgutierrez@wiscot.com>
 * @since 3.0.0 Surface
 * @version 3.0.7 Surface
 * @package \nabu\sdk\builders\nabu
 */
class CNabuPHPClassTableBuilder extends CNabuPHPClassTableAbstractBuilder
{
    /**
     * Database connector instance.
     * @var INabuDBConnector
     */
    private $connector = null;
    /**
     * Database schema name.
     * @var string
     */
    private $schema = false;
    /**
     * Table name.
     * @var string
     */
    private $table = null;
    /**
     * Database Syntax Builder.
     * @var INabuDBSyntaxBuilder
     */
    private $db_syntax = null;
    /**
     * Have attributes field and needs to prepare getDataTree method.
     * @var bool
     */
    private $have_attributes = false;

    private $have_lang = false;
    private $translated_table = null;
    private $translated_desc = null;
    private $translated_primary_desc = null;

    /**
     * The constructor checks if all parameters have valid values, and throws an exception if not.
     * @param CNabuAbstractBuilder $container Container builder object
     * @param INabuDBConnector $connector Database connector to acquire table
     * @param string $namespace Namespace of the class to be generated
     * @param string $name Class name to be generated without namespace
     * @param string $entity_name Entity name. This value is used for comment purposes
     * @param string $table Table name to be extracted
     * @param string $schema Scheme name of the table
     * @param bool $abstract Defines if the class is abstract or not
     * @throws ENabuCoreException Throws an exception if some parameter is not valid
     */
    public function __construct(
        CNabuAbstractBuilder $container,
        INabuDBConnector $connector,
        $namespace,
        $name,
        $entity_name,
        $table,
        $schema = false,
        $abstract = false
    ) {
        if (strlen($table) === 0) {
            throw new ENabuCoreException(
                    ENabuCoreException::ERROR_CONSTRUCTOR_PARAMETER_IS_EMPTY,
                    array('$table')
            );
        }

        $this->connector = $connector;
        $this->table = $table;
        $this->schema = $schema;

        $this->db_syntax = $this->connector->getSyntaxBuilder();
        $table_desc = $this->db_syntax->describeStorage($this->table, $this->schema);

        parent::__construct($container, $table_desc, $namespace, $name, $entity_name, $abstract);
    }

    /**
     * Check if the table is a master of translations table.
     * @return bool Returns true if it is master and false if not.
     */
    public function isTranslatedTable()
    {
        return $this->have_lang;
    }

    /**
     * Check if the table is a translations table.
     * @return bool Returns true if table is a translations table and false if not.
     */
    public function isTranslationsTable()
    {
        return $this->is_translation;
    }

    /**
     * Prepares the class and define it in memory to be serialized after.
     * @param string $author_name Name of the author to place in class comments
     * @param string $author_email e-Mail of the author to place in class comments
     * @return bool Return true if the table was prepared or false if not.
     */
    public function prepare($author_name = false, $author_email = false)
    {
        if ($this->is_customer_child || $this->is_customer_foreign) {
            $this->getDocument()->addUse('\nabu\data\customer\traits\TNabuCustomerChild');
            $this->addUse('TNabuCustomerChild');
        }

        if ($this->is_commerce_child || $this->is_commerce_foreign) {
            $this->getDocument()->addUse('\nabu\data\commerce\traits\TNabuCommerceChild');
            $this->addUse('TNabuCommerceChild');
        }

        if ($this->is_catalog_child || $this->is_catalog_foreign) {
            $this->getDocument()->addUse('\nabu\data\catalog\traits\TNabuCatalogChild');
            $this->addUse('TNabuCatalogChild');
        }

        if ($this->is_site_child || $this->is_site_foreign) {
            $this->getDocument()->addUse('\nabu\data\site\traits\TNabuSiteChild');
            $this->addUse('TNabuSiteChild');
        }

        if ($this->is_site_target_child || $this->is_site_target_foreign) {
            $this->getDocument()->addUse('\nabu\data\site\traits\TNabuSiteTargetChild');
            $this->addUse('TNabuSiteTargetChild');
        }

        if ($this->is_medioteca_child || $this->is_medioteca_foreign) {
            $this->getDocument()->addUse('\nabu\data\medioteca\traits\TNabuMediotecaChild');
            $this->addUse('TNabuMediotecaChild');
        }

        if ($this->is_messaging_child || $this->is_messaging_foreign) {
            $this->getDocument()->addUse('\nabu\data\messaging\traits\TNabuMessagingChild');
            $this->addUse('TNabuMessagingChild');
        }

        if ($this->is_role_child || $this->is_role_foreign) {
            $this->getDocument()->adduse('\nabu\data\security\traits\TNabuRoleChild');
            $this->addUse('TNabuRoleChild');
        }

        if ($this->checkForTranslationsTable()) {
            $this->addInterface('INabuTranslated');
            $this->getDocument()->addUse('\nabu\data\lang\interfaces\INabuTranslated');
            $this->getDocument()->addUse('\nabu\data\lang\traits\TNabuTranslated');
            $this->addUse('TNabuTranslated');
        }

        if ($this->checkForTranslatedTable()) {
            $this->addInterface('INabuTranslation');
            $this->getDocument()->addUse('\nabu\data\lang\interfaces\INabuTranslation');
            $this->getDocument()->addUse('\nabu\data\lang\traits\TNabuTranslation');
            $this->addUse('TNabuTranslation');
        }

        $this->addComment(
                "Class to manage the entity $this->entity_name stored in the storage named $this->table."
        );
        if ($author_name !== null || $author_email !== null) {
            $this->addComment("@author"
                             . (is_string($author_name) ? ' ' . $author_name : '')
                             . (is_string($author_email) ? " <$author_email>" : '')
            );
        }
        $this->addComment("@version " . NABU_VERSION);
        $this->addComment("@package " . '\\' . $this->class_namespace);

        $this->prepareConstructor();
        $this->prepareGetStorageDescriptorPath();
        $this->prepareGetStorageName();
        $this->prepareGetSelectRegister();
        $this->prepareFindByKey();
        $this->prepareGetAllItems();
        $this->prepareGetFilteredItemList();

        $this->prepareTranslationMethods();
        $this->prepareTranslatedMethods();

        $this->prepareGettersAndSetters();
        $this->prepareGetTreeData();
    }

    private function checkForTranslationsTable()
    {
        $main_table = $this->getStorageDescriptor();
        if (!nb_strEndsWith($this->table, NABU_LANG_TABLE_SUFFIX) &&
            $main_table->hasPrimaryConstraint() &&
            ($child_table = $this->db_syntax->describeStorage(
                $this->table . NABU_LANG_TABLE_SUFFIX, $this->schema)
            ) !== null &&
            $child_table->hasPrimaryConstraint() &&
            $child_table->getPrimaryConstraintSize() === $main_table->getPrimaryConstraintSize() + 1
        ) {
            $key_diff = array_diff(
                $child_table->getPrimaryConstraintFieldNames(),
                $main_table->getPrimaryConstraintFieldNames()
            );
            $this->have_lang = (count($key_diff) === 1 && array_shift($key_diff) === NABU_LANG_FIELD_ID);
        }

        return $this->have_lang;
    }

    private function checkForTranslatedTable()
    {
        if (nb_strEndsWith($this->table, NABU_LANG_TABLE_SUFFIX)) {
            $table_descriptor = $this->getStorageDescriptor();
            $this->translated_table = substr($this->table, 0, strlen($this->table) - strlen(NABU_LANG_TABLE_SUFFIX));
            ;
            if ($table_descriptor->hasPrimaryConstraint() &&
                ($this->translated_desc = $this->db_syntax->describeStorage($this->translated_table, $this->schema)) &&
                $this->translated_desc->getPrimaryConstraintSize() + 1 === $table_descriptor->getPrimaryConstraintSize()
            ) {
                $key_diff = array_diff(
                    $table_descriptor->getPrimaryConstraintFieldNames(),
                    $this->translated_desc->getPrimaryConstraintFieldNames()
                );
                $this->is_translation = (count($key_diff) === 1 && array_shift($key_diff) === NABU_LANG_FIELD_ID);
            }
        } else {
            $this->is_translation = false;
        }

        return $this->is_translation;
    }

    /**
     * Prepares the constructor of class
     */
    private function prepareConstructor()
    {
        $table_descriptor = $this->getStorageDescriptor();

        $this->getDocument()->addUse('\nabu\db\CNabuDBInternalObject');
        $this->setExtends('CNabuDBInternalObject');

        $fragment = new CNabuPHPConstructorBuilder($this);
        $fragment->addComment(
                "Instantiates the class. If you fill enough parameters to identify an instance "
              . "serialized in the storage, then the instance is deserialized from the storage.");
        if ($table_descriptor->hasPrimaryConstraint()) {
            foreach ($table_descriptor->getPrimaryConstraintFieldNames() as $field_name) {
                if (nb_strEndsWith($field_name, '_id')) {
                    $var_name = substr($field_name, 0, strlen($field_name) - 3);
                } else {
                    $var_name = $field_name;
                }
                $fragment->addParam(
                        $var_name, null, true, false, 'mixed',
                        "An instance of $this->name or another object descending from "
                      . "\\nabu\\data\\CNabuDataObject which contains a field named $field_name,"
                      . " or a valid ID.");
                $fragment->addFragment(
                        array(
                            "if (\$$var_name) {",
                            "    \$this->transferMixedValue(\$$var_name, '$field_name');",
                            "}",
                            ''
                        )
                );
            }
        }
        $fragment->addFragment('parent::__construct();');

        if ($this->have_lang) {
            $fragment->addFragment('$this->__translatedConstruct();');
        }

        $this->addFragment($fragment);
    }

    /**
     * Prepares the getStorageDescriptorPath method
     */
    private function prepareGetStorageDescriptorPath()
    {
        $fragment = new CNabuPHPMethodBuilder(
                $this,
                'getStorageDescriptorPath',
                CNabuPHPMethodBuilder::FUNCTION_PUBLIC,
                true
        );
        $fragment->addComment('Get the file name and path where is stored the descriptor in JSON format.');
        $fragment->addComment('@return string Return the file name with the full path');
        $fragment->addFragment("return preg_replace('/.php$/', '.json', __FILE__);");

        $this->addFragment($fragment);
    }

    /**
     * Prepares the getStorageName method
     */
    private function prepareGetStorageName()
    {
        $fragment = new CNabuPHPMethodBuilder(
            $this, 'getStorageName', CNabuPHPMethodBuilder::FUNCTION_PUBLIC, true
        );
        $fragment->addComment('Get the table name represented by this class');
        $fragment->addComment('@return string Return the table name');
        $fragment->addFragment("return '$this->table';");

        $this->addFragment($fragment);
    }

    /**
     * Prepares the getSelectRegister method
     */
    private function prepareGetSelectRegister()
    {
        $table_descriptor = $this->getStorageDescriptor();

        $fragment = new CNabuPHPMethodBuilder($this, 'getSelectRegister');
        $fragment->addComment('Gets SELECT sentence to load a single register from the storage.');
        $fragment->addComment('@return string Return the sentence.');

        if ($table_descriptor->hasPrimaryConstraint()) {
            $part_if = '';
            $part_where = array();
            foreach ($table_descriptor->getPrimaryConstraintFieldNames() as $field_name) {
                list($if, $where) = $this->buildSelectRegisterFragment($field_name);
                if (strlen($if) > 0 && strlen($where) > 0) {
                    $part_if .= (strlen($part_if) === 0 ? '' : ' && ') . $if;
                    $part_where[] = $where;
                }
            }

            $output = array(
                        "return ($part_if)",
                        "    ? \$this->buildSentence(",
                        "            'select * '",
                        "            . 'from $this->table '"
            );

            $c = 0;
            foreach ($part_where as $where) {
                $output[] = ($c++ === 0
                        ? "           . \"where $where \""
                        : "             . \"and $where \"");
            }
            $output[] = "      )";
            $output[] = "    : null;";

            $fragment->addFragment($output);
        }

        $this->addFragment($fragment);
    }

    /**
     * Build a table key comparison to prepare the getSelectRegister method
     * @param string $name Field name
     * @return array Returns a list with two elements: the evaluator value in PHP and the where value in SQL
     */
    private function buildSelectRegisterFragment($name)
    {
        $field = $this->getStorageDescriptor()->getField($name);

        switch ($field['data_type']) {
            case 'int':
                $part_if = "\$this->isValueNumeric('$name')";
                $part_where =  "$name=%$name\\\$d";
                break;
            case 'varchar':
                $part_if = "\$this->isValueString('$name')";
                $part_where = "$name='%$name\\\$s'";
                break;
            default:
                $part_if = null;
                $part_where = null;
        }

        return array($part_if, $part_where);
    }

    /**
     * Prepare getters and setters for each field in the table
     */
    private function prepareGettersAndSetters()
    {
        $table_descriptor = $this->getStorageDescriptor();

        foreach ($table_descriptor->getFieldNames() as $name) {
            $field = $table_descriptor->getField($name);
            if (array_key_exists('name', $field) &&
                array_key_exists('data_type', $field) &&
                array_key_exists('type', $field)
            ) {
                $field_name = $field['name'];
                if (nb_strStartsWith($field_name, $this->table)) {
                    $field_name = preg_replace('/^' . $this->table . '_*/', '', $field_name);
                }
                $name = nb_underscore2StudlyCaps($field_name, true, $this->dictionary);
                $entity = nb_underscore2EntityName($field['name'], true, $this->dictionary);
                $this->buildGetter($field, $name, $entity);
                $this->buildSetter($field, $field_name, $name, $entity);
            }
        }
    }

    private function buildGetter($field, $studly_name, $entity_name)
    {
        switch ($field['data_type']) {
            case 'int':
                $data_type = 'int';
                break;
            case 'enum':
            case 'varchar':
            case 'tinytext':
            case 'text':
            case 'longtext':
                $data_type = 'string';
                break;
            default:
                $data_type = 'mixed';
        }

        $table = $this->getStorageDescriptor()->getStorageName();
        $is_attr = $table . '_attributes' === $field['name'] &&
                   in_array($field['data_type'], array('text', 'tinytext', 'longtext', 'varchar', 'json'))
        ;
        $this->have_attributes = $this->have_attributes || $is_attr;
        $nullable = (!array_key_exists('is_nullable', $field) || $field['is_nullable']);
        $data_type = ($nullable && $data_type !== 'mixed' ? 'null|' : '')
                   . ($is_attr && $data_type === 'string' ? 'array' : $data_type);

        $fragment = new CNabuPHPMethodBuilder($this, 'get' . $studly_name);
        $fragment->addComment("Get $entity_name attribute value");
        $fragment->addComment("@return $data_type Returns the $entity_name value");
        if ($is_attr) {
            $fragment->addFragment("return \$this->getValueJSONDecoded('$field[name]');");
        } else {
            $fragment->addFragment("return \$this->getValue('$field[name]');");
        }

        $this->addFragment($fragment);
    }

    private function buildSetter($field, $param_name, $studly_name, $entity_name)
    {
        $name = $field['name'];

        switch ($field['data_type']) {
            case 'int':
                $data_type = 'int';
                break;
            case 'enum':
            case 'varchar':
            case 'tinytext':
            case 'text':
            case 'longtext':
                $data_type = 'string';
                break;
            default:
                $data_type = 'mixed';
        }

        $table = $this->getStorageDescriptor()->getStorageName();
        $is_attr = $table . '_attributes' === $field['name'] &&
                   in_array($field['data_type'], array('text', 'tinytext', 'longtext', 'varchar', 'json'))
        ;
        $this->have_attributes = $this->have_attributes || $is_attr;
        $nullable = (!array_key_exists('is_nullable', $field) || $field['is_nullable']);
        $data_type = ($nullable && $data_type !== 'mixed' ? 'null|' : '')
                   . $data_type
                   . ($is_attr && $data_type !== 'mixed' ? '|array' : '');

        $fragment = new CNabuPHPMethodBuilder($this, 'set' . $studly_name);
        $fragment->addComment("Sets the $entity_name attribute value");
        $fragment->addComment("@return $this->name Returns \$this");
        $fragment->addParam($param_name, null, false, false, $data_type, 'New value for attribute');
        if (!$nullable) {
            $fragment->addFragment(array(
                "if (\$$param_name === null) {",
                "    throw new ENabuCoreException(",
                "            ENabuCoreException::ERROR_NULL_VALUE_NOT_ALLOWED_IN,",
                "            array(\"\\\$$param_name\")",
                "    );",
                "}"
            ));
            $this->getDocument()->addUse('\nabu\core\exceptions\ENabuCoreException');
        }

        $table = $this->getStorageDescriptor()->getStorageName();
        if ($table . '_attributes' === $field['name'] &&
            in_array($field['data_type'], array('text', 'tinytext', 'longtext', 'varchar', 'json'))
        ) {
            $fragment->addFragment("\$this->setValueJSONEncoded('$name', \$$param_name);");
        } else {
            $fragment->addFragment("\$this->setValue('$name', \$$param_name);");
        }

        $fragment->addFragment(array(
            '',
            'return $this;'
        ));

        $this->addFragment($fragment);
    }

    /**
     * Prepare the getAllItems method
     */
    private function prepareGetAllItems()
    {
        $table_descriptor = $this->getStorageDescriptor();

        if ($table_descriptor->getPrimaryConstraintSize() === 1) {
            $keys = $table_descriptor->getPrimaryConstraintFieldNames();
            $key = array_shift($keys);
            $func_name = 'getAll' . str_replace(' ', '', $this->entity_name) . 's';

            $fragment = new CNabuPHPMethodBuilder($this, $func_name, CNabuPHPMethodBuilder::FUNCTION_PUBLIC, true);
            $fragment->addComment(
                    "Get all items in the storage as an associative array where the field '$key' "
                  . "is the index, and each value is an instance of class $this->name.");
            $fragment->addComment("@return mixed Returns and array with all items.");

            if ($this->is_customer_child || $this->is_customer_foreign) {
                $this->getDocument()->addUse('\nabu\data\customer\CNabuCustomer');
                $fragment->addParam(
                    NABU_CUSTOMER_TABLE, 'CNabuCustomer', false, false,
                    'CNabuCustomer', "The CNabuCustomer instance of the Customer that owns the $this->entity_name List."
                );
                $fragment->addFragment(array(
                    "\$" . NABU_CUSTOMER_FIELD_ID
                         ." = nb_getMixedValue(\$"
                         . NABU_CUSTOMER_TABLE
                         . ", '"
                         . NABU_CUSTOMER_FIELD_ID
                         . "');",
                    "if (is_numeric(\$" . NABU_CUSTOMER_FIELD_ID . ")) {",
                    "    \$retval = forward_static_call(",
                    "    array(get_called_class(), 'buildObjectListFromSQL'),",
                    "        '$key',",
                    "        'select * '",
                    "        . 'from $this->table '",
                    "       . 'where " . NABU_CUSTOMER_FIELD_ID . "=%cust_id\$d',",
                    "        array(",
                    "            'cust_id' => \$" . NABU_CUSTOMER_FIELD_ID,
                    "        ),",
                    "        \$" . NABU_CUSTOMER_TABLE,
                    "    );",
                    "} else {",
                    "    \$retval = null;",
                    "}",
                    "",
                    "return \$retval;"
                ));
            } elseif ($this->is_site_child || $this->is_site_foreign) {
                $this->getDocument()->addUse('\nabu\data\site\CNabuSite');
                $fragment->addParam(
                    NABU_SITE_TABLE, 'CNabuSite', false, false,
                    'CNabuSite', "The CNabuSite instance of the Site that owns the $this->entity_name List."
                );
                $fragment->addFragment(array(
                    "\$" . NABU_SITE_FIELD_ID
                         ." = nb_getMixedValue(\$"
                         . NABU_SITE_TABLE
                         . ", '"
                         . NABU_SITE_FIELD_ID
                         . "');",
                    "if (is_numeric(\$" . NABU_SITE_FIELD_ID . ")) {",
                    "    \$retval = forward_static_call(",
                    "    array(get_called_class(), 'buildObjectListFromSQL'),",
                    "        '$key',",
                    "        'select * '",
                    "        . 'from $this->table '",
                    "       . 'where " . NABU_SITE_FIELD_ID . "=%site_id\$d',",
                    "        array(",
                    "            'site_id' => \$" . NABU_SITE_FIELD_ID,
                    "        ),",
                    "        \$" . NABU_SITE_TABLE,
                    "    );",
                    "} else {",
                    "    \$retval = null;",
                    "}",
                    "",
                    "return \$retval;"
                ));
            } elseif ($this->is_commerce_child || $this->is_commerce_foreign) {
                $this->getDocument()->addUse('\nabu\data\commerce\CNabuCommerce');
                $fragment->addParam(
                    NABU_COMMERCE_TABLE, 'CNabuCommerce', false, false,
                    'CNabuCommerce', "The CNabuCommerce instance of the Commerce that owns the $this->entity_name List."
                );
                $fragment->addFragment(array(
                    "\$" . NABU_COMMERCE_FIELD_ID
                         ." = nb_getMixedValue(\$"
                         . NABU_COMMERCE_TABLE
                         . ", '"
                         . NABU_COMMERCE_FIELD_ID
                         . "');",
                    "if (is_numeric(\$" . NABU_COMMERCE_FIELD_ID . ")) {",
                    "    \$retval = forward_static_call(",
                    "    array(get_called_class(), 'buildObjectListFromSQL'),",
                    "        '$key',",
                    "        'select * '",
                    "        . 'from $this->table '",
                    "       . 'where " . NABU_COMMERCE_FIELD_ID . "=%commerce_id\$d',",
                    "        array(",
                    "            'commerce_id' => \$" . NABU_COMMERCE_FIELD_ID,
                    "        ),",
                    "        \$" . NABU_COMMERCE_TABLE,
                    "    );",
                    "} else {",
                    "    \$retval = null;",
                    "}",
                    "",
                    "return \$retval;"
                ));
            } elseif ($this->is_catalog_child || $this->is_catalog_foreign) {
                $this->getDocument()->addUse('\nabu\data\catalog\CNabuCatalog');
                $fragment->addParam(
                    NABU_CATALOG_TABLE, 'CNabuCatalog', false, false,
                    'CNabuCatalog', "The CNabuCatalog instance of the Catalog that owns the $this->entity_name List."
                );
                $fragment->addFragment(array(
                    "\$" . NABU_CATALOG_FIELD_ID
                         ." = nb_getMixedValue(\$"
                         . NABU_CATALOG_TABLE
                         . ", '"
                         . NABU_CATALOG_FIELD_ID
                         . "');",
                    "if (is_numeric(\$" . NABU_CATALOG_FIELD_ID . ")) {",
                    "    \$retval = forward_static_call(",
                    "    array(get_called_class(), 'buildObjectListFromSQL'),",
                    "        '$key',",
                    "        'select * '",
                    "        . 'from $this->table '",
                    "       . 'where " . NABU_CATALOG_FIELD_ID . "=%catalog_id\$d',",
                    "        array(",
                    "            'catalog_id' => \$" . NABU_CATALOG_FIELD_ID,
                    "        ),",
                    "        \$" . NABU_CATALOG_TABLE,
                    "    );",
                    "} else {",
                    "    \$retval = null;",
                    "}",
                    "",
                    "return \$retval;"
                ));
            } elseif ($this->is_medioteca_child || $this->is_medioteca_foreign) {
                $this->getDocument()->addUse('\nabu\data\medioteca\CNabuMedioteca');
                $fragment->addParam(
                    NABU_MEDIOTECA_TABLE, 'CNabuMedioteca', false, false,
                    'CNabuMedioteca', "The CNabuMedioteca instance of the Medioteca that owns the $this->entity_name List."
                );
                $fragment->addFragment(array(
                    "\$" . NABU_MEDIOTECA_FIELD_ID
                         ." = nb_getMixedValue(\$"
                         . NABU_MEDIOTECA_TABLE
                         . ", '"
                         . NABU_MEDIOTECA_FIELD_ID
                         . "');",
                    "if (is_numeric(\$" . NABU_MEDIOTECA_FIELD_ID . ")) {",
                    "    \$retval = forward_static_call(",
                    "    array(get_called_class(), 'buildObjectListFromSQL'),",
                    "        '$key',",
                    "        'select * '",
                    "        . 'from $this->table '",
                    "       . 'where " . NABU_MEDIOTECA_FIELD_ID . "=%medioteca_id\$d',",
                    "        array(",
                    "            'medioteca_id' => \$" . NABU_MEDIOTECA_FIELD_ID,
                    "        ),",
                    "        \$" . NABU_MEDIOTECA_TABLE,
                    "    );",
                    "} else {",
                    "    \$retval = null;",
                    "}",
                    "",
                    "return \$retval;"
                ));
            } elseif ($this->is_messaging_child || $this->is_messaging_foreign) {
                $this->getDocument()->addUse('\nabu\data\messaging\CNabuMessaging');
                $fragment->addParam(
                    NABU_MESSAGING_TABLE, 'CNabuMessaging', false, false,
                    'CNabuMessaging', "The CNabuMessaging instance of the Messagin that owns the $this->entity_name List"
                );
                $fragment->addFragment(array(
                    "\$" . NABU_MESSAGING_FIELD_ID
                         . " = nb_getMixedValue(\$"
                         . NABU_MESSAGING_TABLE
                         . ", '"
                         . NABU_MESSAGING_FIELD_ID
                         . "');",
                    "if (is_numeric(\$" . NABU_MESSAGING_FIELD_ID . ")) {",
                    "    \$retval = forward_static_call(",
                    "    array(get_called_class(), 'buildObjectListFromSQL'),",
                    "        '$key',",
                    "        'select * '",
                    "        . 'from $this->table '",
                    "       . 'where " . NABU_MESSAGING_FIELD_ID . "=%messaging_id\$d',",
                    "        array(",
                    "            'messaging_id' => \$" . NABU_MESSAGING_FIELD_ID,
                    "        ),",
                    "        \$" . NABU_MESSAGING_TABLE,
                    "    );",
                    "} else {",
                    "    \$retval = null;",
                    "}",
                    "",
                    "return \$retval;"
                ));
            } else {
                $fragment->addFragment(array(
                    "return forward_static_call(",
                    "        array(get_called_class(), 'buildObjectListFromSQL'),",
                    "        '$key',",
                    "        'select * from $this->table'",
                    ");"
                ));
            }

            $this->addFragment($fragment);
        }
    }

    /**
     * Prepare the getFilteredItemList method
     */
    private function prepareGetFilteredItemList()
    {
        if (!$this->is_translation) {
            $func_name = 'getFiltered' . str_replace(' ', '', $this->entity_name) . 'List';
            $fragment = new CNabuPHPMethodBuilder($this, $func_name, CNabuPHPMethodBuilder::FUNCTION_PUBLIC, true);
            $fragment->addComment(
                  "Gets a filtered list of $this->entity_name instances represented as an array. "
                . 'Params allows the capability of select a subset of fields, order by concrete fields, '
                . 'or truncate the list by a number of rows starting in an offset.'
            );
            if ($this->is_customer_foreign) {
                $fragment->addParam(
                    'nb_customer', null, false, false,
                    'mixed', 'Customer instance, object containing a Customer Id field or an Id.'
                );
            }
            if ($this->is_site_foreign) {
                $fragment->addParam(
                    'nb_site', null, false, false,
                    'mixed', 'Site instance, object containing a Site Id field or an Id.'
                );
            }
            if ($this->is_catalog_foreign) {
                $fragment->addParam(
                    'nb_catalog', null, false, false,
                    'mixed', 'Catalog instance, object containing a Catalog Id field or an Id.'
                );
            }
            if ($this->is_medioteca_foreign) {
                $fragment->addParam(
                    'nb_medioteca', null, true, null,
                    'mixed', 'Medioteca instance, object containing a Medioteca Id field or an Id.'
                );
            }
            if ($this->is_messaging_foreign) {
                $fragment->addParam(
                    'nb_messaging', null, true, null,
                    'mixed', 'Messaging instance, object containing a Messaging Id field or an Id.'
                );
            }
            $fragment->addParam(
                'q', null, true, null, 'string', 'Query string to filter results using a context index.'
            );
            $fragment->addParam(
                'fields', null, true, null,
                'string|array', 'List of fields to put in the results.'
            );
            $fragment->addParam(
                'order', null, true, null,
                  'string|array' , 'List of fields to order the results. Each field can be suffixed '
                . 'with "ASC" or "DESC" to determine the short order'
            );
            $fragment->addParam(
                'offset', null, true, 0,
                'int', 'Offset of first row in the results having the first row at offset 0.'
            );
            $fragment->addParam(
                'num_items', null, true, 0,
                'int', 'Number of continue rows to get as maximum in the results.'
            );
            $fragment->addComment('@return array Returns an array with all rows found using the criteria.');
            $fragment->addComment(
                  '@throws \nabu\core\exceptions\ENabuCoreException Raises an exception if $fields or $order '
                . 'have invalid values.'
            );

            $this->addFragment($fragment);

            $is_enclosed = $this->is_customer_foreign || $this->is_site_foreign || $this->is_medioteca_foreign ||
                           $this->is_commerce_foreign || $this->is_catalog_foreign || $this->is_messaging_foreign
            ;

            if ($this->is_customer_foreign) {
                $fragment->addFragment(array(
                    '$' . NABU_CUSTOMER_FIELD_ID . ' = nb_getMixedValue($nb_customer, NABU_CUSTOMER_FIELD_ID);',
                    'if (is_numeric($' . NABU_CUSTOMER_FIELD_ID . ')) {'
                ));
            } elseif ($this->is_site_foreign) {
                $fragment->addFragment(array(
                    '$' . NABU_SITE_FIELD_ID . ' = nb_getMixedValue($nb_customer, NABU_SITE_FIELD_ID);',
                    'if (is_numeric($' . NABU_SITE_FIELD_ID . ')) {'
                ));
            } elseif ($this->is_commerce_foreign) {
                $fragment->addFragment(array(
                    '$' . NABU_COMMERCE_FIELD_ID . ' = nb_getMixedValue($nb_customer, NABU_COMMERCE_FIELD_ID);',
                    'if (is_numeric($' . NABU_COMMERCE_FIELD_ID . ')) {'
                ));
            } elseif ($this->is_catalog_foreign) {
                $fragment->addFragment(array(
                    '$' . NABU_CATALOG_FIELD_ID . ' = nb_getMixedValue($nbu_customer, NABU_CATALOG_FIELD_ID);',
                    'if (is_numeric($' . NABU_CATALOG_FIELD_ID . ')) {'
                ));
            } elseif ($this->is_medioteca_foreign) {
                $fragment->addFragment(array(
                    '$' . NABU_MEDIOTECA_FIELD_ID . ' = nb_getMixedValue($nb_customer, NABU_MEDIOTECA_FIELD_ID);',
                    'if (is_numeric($' . NABU_MEDIOTECA_FIELD_ID . ')) {'
                ));
            } elseif ($this->is_messaging_foreign) {
                $fragment->addFragment(array(
                    '$' . NABU_MESSAGING_FIELD_ID . ' = nb_getMixedValue($nb_customer, NABU_MESSAGING_FIELD_ID);',
                    'if (is_numeric($' . NABU_MESSAGING_FIELD_ID . ')) {'
                ));
            }

            $padding = ($is_enclosed ? '    ' : '');

            $fragment->addFragment(array(
                $padding . "\$fields_part = nb_prefixFieldList($this->name::getStorageName(), \$fields, false, true, '`');",
                $padding . "\$order_part = nb_prefixFieldList($this->name::getStorageName(), \$fields, false, false, '`');",
                '',
                $padding . 'if ($num_items !== 0) {',
                $padding . '    $limit_part = ($offset > 0 ? $offset . \', \' : \'\') . $num_items;',
                $padding . '} else {',
                $padding . '    $limit_part = false;',
                $padding . '}',
                '',
                $padding . '$nb_item_list = CNabuEngine::getEngine()->getMainDB()->getQueryAsArray(',
                $padding . '    "select " . ($fields_part ? $fields_part . \' \' : \'* \')',
                $padding . "    . 'from $this->table '"
            ));
            if ($this->is_customer_foreign) {
                $fragment->addFragment($padding . "   . 'where ' . NABU_CUSTOMER_FIELD_ID . '=%cust_id\$d '");
            } elseif ($this->is_site_foreign) {
                $fragment->addFragment($padding . "   . 'where ' . NABU_SITE_FIELD_ID . '=%site_id\$d '");
            } elseif ($this->is_commerce_foreign) {
                $fragment->addFragment($padding . "   . 'where ' . NABU_COMMERCE_FIELD_ID . '=%commerce_id\$d '");
            } elseif ($this->is_catalog_foreign) {
                $fragment->addFragment($padding . "   . 'where ' . NABU_CATALOG_FIELD_ID . '=%catalog_id\$d '");
            } elseif ($this->is_medioteca_foreign) {
                $fragment->addFragment($padding . "   . 'where ' . NABU_MEDIOTECA_FIELD_ID . '=%medioteca_id\$d '");
            } elseif ($this->is_messaging_foreign) {
                $fragment->addFragment($padding . "   . 'where ' . NABU_MESSAGING_FIELD_ID . '=%messaging_id\$d '");
            }
            $fragment->addFragment(array(
                $padding . '    . ($order_part ? "order by $order_part " : \'\')',
                $padding . '    . ($limit_part ? "limit $limit_part" : \'\'),',
                $padding . '    array('
            ));
            if ($this->is_customer_foreign) {
                $fragment->addFragment($padding . '        \'cust_id\' => $' . NABU_CUSTOMER_FIELD_ID);
            } elseif ($this->is_site_foreign) {
                $fragment->addFragment($padding . '        \'site_id\' => $' . NABU_SITE_FIELD_ID);
            } elseif ($this->is_commerce_foreign) {
                $fragment->addFragment($padding . '        \'commerce_id\' => $' . NABU_COMMERCE_FIELD_ID);
            } elseif ($this->is_catalog_foreign) {
                $fragment->addFragment($padding . '        \'catalog_id\' => $' . NABU_CATALOG_FIELD_ID);
            } elseif ($this->is_medioteca_foreign) {
                $fragment->addFragment($padding . '        \'medioteca_id\' => $' . NABU_MEDIOTECA_FIELD_ID);
            } elseif ($this->is_messaging_foreign) {
                $fragment->addFragment($padding . '        \'messaging_id\' => $' . NABU_MESSAGING_FIELD_ID);
            }
            $fragment->addFragment(array(
                $padding . '    )',
                $padding . ');'
            ));

            if ($is_enclosed) {
                $fragment->addFragment(array(
                    '} else {',
                    '    $nb_item_list = null;',
                    '}'
                ));
            }

            $fragment->addFragment(array(
                '', 'return $nb_item_list;'
            ));

            $this->getDocument()->addUse('\nabu\core\CNabuEngine');
        }
    }

    private function prepareFindByKey()
    {
        $table_descriptor = $this->getStorageDescriptor();

        $key_name = $this->table . '_key';

        if ($table_descriptor->hasField($key_name) &&
            is_array($field = $table_descriptor->getField($key_name)) &&
            array_key_exists('data_type', $field) &&
            $field['data_type'] === 'varchar'
        ) {
            $keyed_class = nb_strEndsWith($this->name, 'Base')
                         ? preg_replace('/Base$/', '', $this->name)
                         : $this->name
            ;
            $keyed_namespace = nb_strEndsWith($this->class_namespace, '\base')
                             ? preg_replace('/\\\\base$/', '', $this->class_namespace)
                             : $this->class_namespace
            ;
            $fragment = new CNabuPHPMethodBuilder($this, 'findByKey', CNabuPHPMethodBuilder::FUNCTION_PUBLIC, true);
            $fragment->addComment("Find an instance identified by $key_name field.");
            $fragment->addComment("@return $keyed_class Returns a valid instance if exists or null if not.");
            $with_customer = false;
            $with_site = false;
            if ($this->is_customer_child || $this->is_customer_foreign) {
                $fragment->addParam(
                    NABU_CUSTOMER_TABLE, null, false, false,
                    'mixed', "Customer that owns $this->entity_name"
                );
            } elseif ($this->is_site_child || $this->is_site_foreign) {
                $fragment->addParam(
                    NABU_SITE_TABLE, null, false, false,
                    'mixed', "Site that owns $this->entity_name"
                );
            } elseif ($this->is_commerce_child || $this->is_commerce_foreign) {
                $fragment->addParam(
                    NABU_COMMERCE_TABLE, null, false, false,
                    'mixed', "Commerce that owns $this->entity_name"
                );
            } elseif ($this->is_catalog_child || $this->is_catalog_foreign) {
                $fragment->addParam(
                    NABU_CATALOG_TABLE, null, false, false,
                    'mixed', "Catalog that owns $this->entity_name"
                );
            } elseif ($this->is_medioteca_child || $this->is_medioteca_foreign) {
                $fragment->addParam(
                    NABU_MEDIOTECA_TABLE, null, false, false,
                    'mixed', "Medioteca that owns $this->entity_name"
                );
            } elseif ($this->is_messaging_child || $this->is_messaging_foreign) {
                $fragment->addParam(
                    NABU_MESSAGING_TABLE, null, false, false,
                    'mixed', "Messaging that owns $this->entity_name"
                );
            }
            $fragment->addParam('key', null, false, false, 'string', 'Key to search');

            if ($this->is_customer_child || $this->is_customer_foreign) {
                $fragment->addFragment(array(
                    "\$" . NABU_CUSTOMER_FIELD_ID
                         ." = nb_getMixedValue(\$"
                         . NABU_CUSTOMER_TABLE
                         . ", '"
                         . NABU_CUSTOMER_FIELD_ID
                         . "');",
                    "if (is_numeric(\$" . NABU_CUSTOMER_FIELD_ID . ")) {",
                    "    \$retval = $keyed_class::buildObjectFromSQL(",
                    "            'select * '",
                    "            . 'from $this->table '",
                    "           . 'where " . NABU_CUSTOMER_FIELD_ID . "=%cust_id\$d '",
                    "             . \"and $key_name='%key\\\$s'\",",
                    "            array(",
                    "                'cust_id' => \$" . NABU_CUSTOMER_FIELD_ID . ",",
                    "                'key' => \$key",
                    "            )",
                    "    );",
                    "} else {",
                    "    \$retval = null;",
                    "}",
                    "",
                    "return \$retval;"
                ));
            } elseif ($this->is_site_child || $this->is_site_foreign) {
                $fragment->addFragment(array(
                    "\$" . NABU_SITE_FIELD_ID
                         . " = nb_getMixedValue(\$"
                         . NABU_SITE_TABLE
                         . ", '"
                         . NABU_SITE_FIELD_ID
                         . "');",
                    "if (is_numeric(\$" . NABU_SITE_FIELD_ID . ")) {",
                    "    \$retval = $keyed_class::buildObjectFromSQL(",
                    "            'select * '",
                    "            . 'from $this->table '",
                    "           . 'where " . NABU_SITE_FIELD_ID . "=%site_id\$d '",
                    "             . \"and $key_name='%key\\\$s'\",",
                    "            array(",
                    "                'site_id' => \$" . NABU_SITE_FIELD_ID . ",",
                    "                'key' => \$key",
                    "            )",
                    "    );",
                    "} else {",
                    "    \$retval = null;",
                    "}",
                    "",
                    "return \$retval;"
                ));
            } elseif ($this->is_commerce_child || $this->is_commerce_foreign) {
                $fragment->addFragment(array(
                    "\$" . NABU_COMMERCE_FIELD_ID
                         . " = nb_getMixedValue(\$"
                         . NABU_COMMERCE_TABLE
                         . ", '"
                         . NABU_COMMERCE_FIELD_ID
                         . "');",
                    "if (is_numeric(\$" . NABU_COMMERCE_FIELD_ID . ")) {",
                    "    \$retval = $keyed_class::buildObjectFromSQL(",
                    "            'select * '",
                    "            . 'from $this->table '",
                    "           . 'where " . NABU_COMMERCE_FIELD_ID . "=%commerce_id\$d '",
                    "             . \"and $key_name='%key\\\$s'\",",
                    "            array(",
                    "                'commerce_id' => \$" . NABU_COMMERCE_FIELD_ID . ",",
                    "                'key' => \$key",
                    "            )",
                    "    );",
                    "} else {",
                    "    \$retval = null;",
                    "}",
                    "",
                    "return \$retval;"
                ));
            } elseif ($this->is_catalog_child || $this->is_catalog_foreign) {
                $fragment->addFragment(array(
                    "\$" . NABU_CATALOG_FIELD_ID
                         . " = nb_getMixedValue(\$"
                         . NABU_CATALOG_TABLE
                         . ", '"
                         . NABU_CATALOG_FIELD_ID
                         . "');",
                    "if (is_numeric(\$" . NABU_CATALOG_FIELD_ID . ")) {",
                    "    \$retval = $keyed_class::buildObjectFromSQL(",
                    "            'select * '",
                    "            . 'from $this->table '",
                    "           . 'where " . NABU_CATALOG_FIELD_ID . "=%catalog_id\$d '",
                    "             . \"and $key_name='%key\\\$s'\",",
                    "            array(",
                    "                'catalog_id' => \$" . NABU_CATALOG_FIELD_ID . ",",
                    "                'key' => \$key",
                    "            )",
                    "    );",
                    "} else {",
                    "    \$retval = null;",
                    "}",
                    "",
                    "return \$retval;"
                ));
            } elseif ($this->is_medioteca_child || $this->is_medioteca_foreign) {
                $fragment->addFragment(array(
                    "\$" . NABU_MEDIOTECA_FIELD_ID
                         . " = nb_getMixedValue(\$"
                         . NABU_MEDIOTECA_TABLE
                         . ", '"
                         . NABU_MEDIOTECA_FIELD_ID
                         . "');",
                    "if (is_numeric(\$" . NABU_MEDIOTECA_FIELD_ID . ")) {",
                    "    \$retval = $keyed_class::buildObjectFromSQL(",
                    "            'select * '",
                    "            . 'from $this->table '",
                    "           . 'where " . NABU_MEDIOTECA_FIELD_ID . "=%medioteca_id\$d '",
                    "             . \"and $key_name='%key\\\$s'\",",
                    "            array(",
                    "                'medioteca_id' => \$" . NABU_MEDIOTECA_FIELD_ID . ",",
                    "                'key' => \$key",
                    "            )",
                    "    );",
                    "} else {",
                    "    \$retval = null;",
                    "}",
                    "",
                    "return \$retval;"
                ));
            } elseif ($this->is_messaging_child || $this->is_messaging_foreign) {
                $fragment->addFragment(array(
                    "\$" . NABU_MESSAGING_FIELD_ID
                         . " = nb_getMixedValue(\$"
                         . NABU_MESSAGING_TABLE
                         . ", '"
                         . NABU_MESSAGING_FIELD_ID
                         . "');",
                    "if (is_numeric(\$" . NABU_MESSAGING_FIELD_ID . ")) {",
                    "    \$retval = $keyed_class::buildObjectFromSQL(",
                    "            'select * '",
                    "            . 'from $this->table '",
                    "           . 'where " . NABU_MESSAGING_FIELD_ID . "=%messaging_id\$d '",
                    "             . \"and $key_name='%key\\\$s'\",",
                    "            array(",
                    "                'messaging_id' => \$" . NABU_MESSAGING_FIELD_ID . ",",
                    "                'key' => \$key",
                    "            )",
                    "    );",
                    "} else {",
                    "    \$retval = null;",
                    "}",
                    "",
                    "return \$retval;"
                ));
            } else {
                $fragment->addFragment(array(
                    "return $keyed_class::buildObjectFromSQL(",
                    "        'select * '",
                    "        . 'from $this->table '",
                    "       . \"where $key_name='%key\\\$s'\",",
                    "        array(",
                    "            'key' => \$key",
                    "        )",
                    ");"
                ));
            }

            $this->addFragment($fragment);

            $this->getDocument()->addUse('\\' . $keyed_namespace . '\\' . $keyed_class);
        }
    }

    private function prepareTranslatedMethods()
    {
        if ($this->have_lang) {
            $lang_class = nb_strEndsWith($this->name, 'Base')
                       ? preg_replace('/Base$/', 'Language', $this->name)
                       : $this->name . 'Language'
            ;
            $lang_namespace = nb_strEndsWith($this->class_namespace, '\base')
                            ? preg_replace('/\\\\base$/', '', $this->class_namespace)
                            : $this->class_namespace
            ;
            $this->getDocument()->addUse('\\' . $lang_namespace . '\\' . $lang_class);
            $this->prepareCheckForValidTranslationInstance($lang_namespace, $lang_class);
            $this->prepareGetLanguages($lang_namespace, $lang_class);
            $this->prepareGetTranslations($lang_namespace, $lang_class);
            $this->prepareNewTranslation($lang_namespace, $lang_class);
            if ($this->is_customer_child || $this->is_customer_foreign) {
                $this->prepareGetCustomerUsedLanguages($lang_namespace, $lang_class);
            }
            $this->prepareRefresh();
        }
    }

    private function prepareTranslationMethods()
    {
        if ($this->is_translation) {
            $translated_class = nb_strEndsWith($this->name, 'Language')
                        ? preg_replace('/Language$/', '', $this->name)
                        : (nb_strEndsWith($this->name, 'LanguageBase')
                           ? preg_replace('/LanguageBase$/', '', $this->name)
                           : null
                          )
            ;
            if ($translated_class !== null) {
                $translated_namespace = nb_strEndsWith($this->class_namespace, '\base')
                                      ? preg_replace('/\\\\base$/', '', $this->class_namespace)
                                      : $this->class_namespace
                ;
                $translated_table = substr($this->table, 0, strlen($this->table) - strlen(NABU_LANG_TABLE_SUFFIX));
                $this->prepareGetLanguagesForTraslatedObject(
                        $translated_table,
                        $translated_namespace,
                        $translated_class
                );
                $this->prepareGetTranslationsForTraslatedObject(
                        $translated_table,
                        $translated_namespace,
                        $translated_class
                );
            }
        }
    }

    private function prepareCheckForValidTranslationInstance($lang_namespace, $lang_class)
    {
        $fragment = new CNabuPHPMethodBuilder(
                $this,
                'checkForValidTranslationInstance',
                CNabuPHPMethodBuilder::FUNCTION_PROTECTED
        );
        $fragment->addComment(
                "Check if the instance passed as parameter \$translation is a valid child "
              . "translation for this object"
        );
        $fragment->addComment("@return bool Return true if a valid object is passed as instance or false elsewhere");
        $fragment->addParam('translation', null, false, false, 'INabuTranslation', 'Translation instance to check');

        $fields = $this->getStorageDescriptor()->getPrimaryConstraintFieldNames();
        $lines = array(
                "return (\$translation !== null &&",
                "        \$translation instanceof $lang_class &&"
        );
        $c = count($fields);
        for ($i = 0; $i < $c; $i++) {
            $lines[] = "        \$translation->matchValue(\$this, '$fields[$i]')" . ($i + 1 < $c ? ' &&' : '');
        }
        $lines[] = ");";
        $fragment->addFragment($lines);

        $this->addFragment($fragment);

        $this->getDocument()->addUse('\nabu\data\lang\interfaces\INabuTranslation');
        $this->getDocument()->addUse('\\' . $lang_namespace . '\\' . $lang_class);
    }

    private function prepareGetLanguages($lang_namespace, $lang_class)
    {
        $output = array(
            'if (!CNabuEngine::getEngine()->isOperationModeStandalone() &&',
            '    ($this->languages_list->getSize() === 0 || $force)',
            ') {',
            "    \$this->languages_list = $lang_class::getLanguagesForTranslatedObject(\$this);",
            '}',
            '',
            'return $this->languages_list;'
        );

        $fragment = new CNabuPHPMethodBuilder($this, 'getLanguages');
        $fragment->addComment('Get all language instances corresponding to available translations.');
        $fragment->addComment(
                '@return null|array Return an array of \nabu\data\lang\CNabuLanguage instances if '
               . 'they have translations or null if not.'
        );
        $fragment->addParam(
                'force', null, true, false, 'bool',
                'If true force to reload languages list from storage.'
        );
        $fragment->addFragment($output);

        $this->addFragment($fragment);

        $this->getDocument()->addUse('\nabu\core\CNabuEngine');
        $this->getDocument()->addUse('\\' . $lang_namespace . '\\' . $lang_class);
    }

    private function prepareGetCustomerUsedLanguages($lang_namespace, $lang_class)
    {
        $fragment = new CNabuPHPMethodBuilder(
            $this, 'getCustomerUsedLanguages', CNabuPHPMethodBuilder::FUNCTION_PUBLIC, true
        );
        $fragment->addComment("Get all language instances used along of all $this->entity_name set of a Customer");
        $fragment->addComment("@return CNabuLanguageList Returns the list of language instances used.");
        $fragment->addParam(
            NABU_CUSTOMER_TABLE, null, false, false, 'mixed',
            'A CNabuDataObject instance containing a field named ' . NABU_CUSTOMER_FIELD_ID . ' or a Customer ID'
        );

        $fragment->addFragment(array(
            '$' . NABU_CUSTOMER_FIELD_ID . ' = nb_getMixedValue($' . NABU_CUSTOMER_TABLE .', NABU_CUSTOMER_FIELD_ID);',
            'if (is_numeric($' . NABU_CUSTOMER_FIELD_ID . ')) {',
            '    $' . NABU_LANG_TABLE . '_list = CNabuLanguage::buildObjectListFromSQL(',
            "        '" . NABU_LANG_FIELD_ID . "',",
            "        'select l.* '",
            "        . 'from " . NABU_LANG_TABLE . " l, '",
            "             . '(select distinct " . NABU_LANG_FIELD_ID . " '",
            "                . 'from nb_catalog ca, nb_catalog_lang cal '",
            "               . 'where ca.nb_catalog_id=cal.nb_catalog_id '",
            "                 . 'and ca." . NABU_CUSTOMER_FIELD_ID . "=%cust_id\$d) as lid '",
            "       . 'where l." . NABU_LANG_FIELD_ID . "=lid." . NABU_LANG_FIELD_ID . "',",
            "        array('cust_id' => \$" . NABU_CUSTOMER_FIELD_ID . ")",
            '    );',
            '} else {',
            '    $' . NABU_LANG_TABLE . '_list = new CNabuLanguageList();',
            '}',
            '',
            'return $' . NABU_LANG_TABLE . '_list;'
        ));

        $this->addFragment($fragment);

        $this->getDocument()->addUse('\nabu\data\lang\CNabuLanguage');
        $this->getDocument()->addUse('\nabu\data\lang\CNabuLanguageList');
    }

    private function prepareGetTranslations($lang_namespace, $lang_class)
    {
        $output = array(
            'if (!CNabuEngine::getEngine()->isOperationModeStandalone() &&',
            '    ($this->translations_list->getSize() === 0 || $force)',
            ') {',
            "    \$this->translations_list = $lang_class::getTranslationsForTranslatedObject(\$this);",
            '}',
            '',
            'return $this->translations_list;'
        );

        $fragment = new CNabuPHPMethodBuilder($this, 'getTranslations');
        $fragment->addComment("Gets available translation instances.");
        $fragment->addComment(
                "@return null|array Return an array of \\$lang_namespace\\$lang_class "
              . "instances if they have translations or null if not."
        );
        $fragment->addParam(
                'force', null, true, false, 'bool',
                'If true force to reload translations list from storage.'
        );
        $fragment->addFragment($output);

        $this->addFragment($fragment);

        $this->getDocument()->addUse('\nabu\core\CNabuEngine');
        $this->getDocument()->addUse('\\' . $lang_namespace . '\\' . $lang_class);
    }

    private function prepareNewTranslation($lang_namespace, $lang_class)
    {
        $builtin_class = str_replace('CNabu', 'CNabuBuiltIn', $lang_class);

        $output = array(
            '$nb_language_id = nb_getMixedValue($nb_language, NABU_LANG_FIELD_ID);',
            'if (is_numeric($nb_language_id) || nb_isValidGUID($nb_language_id)) {',
            "    \$nb_translation = \$this->isBuiltIn()",
            "                    ? new $builtin_class()",
            "                    : new $lang_class()",
            "    ;"
        );

        foreach ($this->getStorageDescriptor()->getPrimaryConstraintFieldNames() as $key_field) {
            $output[] = "    \$nb_translation->transferValue(\$this, '$key_field');";
        }

        $output[] = '    $nb_translation->transferValue($nb_language, NABU_LANG_FIELD_ID);';
        $output[] = '    $this->setTranslation($nb_translation);';
        $output[] = '} else {';
        $output[] = '    $nb_translation = null;';
        $output[] = '}';
        $output[] = '';
        $output[] = 'return $nb_translation;';

        $fragment = new CNabuPHPMethodBuilder($this, 'newTranslation');
        $fragment->addComment('Creates a new translation instance. I the translation already exists then replaces ancient translation with this new.');
        $fragment->addComment("@return $lang_class Returns the created instance to store translation or null if not valid language was provided.");

        $fragment->addParam(
            'nb_language', null, false, false, 'int|string|CNabuDataObject',
            'A valid Id or object containing a ' . NABU_LANG_FIELD_ID . ' field to identify the language of new translation.'
        );
        $fragment->addFragment($output);
        $this->addFragment($fragment);

        $this->getDocument()->addUse('\nabu\data\CNabuDataObject');
        $this->getDocument()->addUse('\\' . $lang_namespace . '\\' . $lang_class);
        $this->getDocument()->addUse('\\' . $lang_namespace . '\\builtin\\' . $builtin_class);
    }

    private function prepareGetLanguagesForTraslatedObject($translated_table, $translated_namespace, $translated_class)
    {
        $output = $this->buildBodyForTranslatedObjectMethods(
                $translated_table,
                $translated_namespace,
                $translated_class,
                'CNabuLanguage',
                false
        );

        $fragment = new CNabuPHPMethodBuilder(
                $this,
                'getLanguagesForTranslatedObject',
                CNabuPHPMethodBuilder::FUNCTION_PUBLIC,
                true
        );
        $fragment->addComment(
                'Query the storage to retrieve the full list of available languages (those that '
              . 'correspond to existent translations) for $translated and returns an associative '
              . 'array in which each one is of class \nabu\data\lang\CNabuLanguage.'
        );
        $fragment->addComment(
                '@return false|null|array Returns an associative array indexed by the language Id, '
              . 'null if no languages are available, or false if $translated cannot be identified.'
        );
        $fragment->addParam(
                'translated', null, false, false, 'object',
                'Translated object to retrieve languages'
        );
        $fragment->addFragment($output);

        $this->addFragment($fragment);

        $this->getDocument()->addUse('\nabu\data\lang\CNabuLanguage');
        $this->getDocument()->addUse('\nabu\data\lang\CNabuLanguageList');
    }

    private function prepareGetTreeData()
    {
        if ($this->have_attributes || $this->have_lang) {
            $fragment = new CNabuPHPMethodBuilder($this, 'getTreeData');
            $fragment->addComment('Overrides this method to add support to traits and/or attributes.');
            $fragment->addComment('@return array Returns a multilevel associative array with all data.');
            $fragment->addParam(
                'nb_language', null, true, null,
                'int|CNabuDataObject', 'Instance or Id of the language to be used.'
            );
            $fragment->addParam(
                'dataonly', null, true, false,
                'bool', 'Render only field values and ommit class control flags.'
            );

            $fragment->addFragment(array('$trdata = parent::getTreeData($nb_language, $dataonly);', ''));
            if ($this->have_attributes) {
                $fragment->addFragment('$trdata[\'attributes\'] = $this->getAttributes();');
            }
            if ($this->have_lang) {
                $fragment->addFragment('$trdata = $this->appendTranslatedTreeData($trdata, $nb_language, $dataonly);');
            }
            $fragment->addFragment(array(
                '',
                'return $trdata;'
            ));

            $this->addFragment($fragment);
        }
    }

    private function prepareRefresh()
    {
        if ($this->have_lang) {
            $fragment = new CNabuPHPMethodBuilder($this, 'refresh');
            $fragment->addComment('Overrides refresh method to add translations branch to refresh.');
            $fragment->addComment('@return bool Returns true if transations are empty or refreshed.');
            $fragment->addFragment('return parent::refresh() && $this->appendTranslatedRefresh();');

            $this->addFragment($fragment);
        }
    }

    private function prepareGetTranslationsForTraslatedObject(
        $translated_table,
        $translated_namespace,
        $translated_class
    ) {
        $subclass_name = preg_replace('/Base$/', '', $this->name);
        $subclass_namespace = preg_replace('/\\\\base$/', '', $this->class_namespace);
        $output = $this->buildBodyForTranslatedObjectMethods(
                $translated_table,
                $translated_namespace,
                $translated_class,
                $subclass_name,
                true
        );

        $fragment = new CNabuPHPMethodBuilder(
                $this,
                'getTranslationsForTranslatedObject',
                CNabuPHPMethodBuilder::FUNCTION_PUBLIC,
                true
        );
        $fragment->addComment(
                "Query the storage to retrieve the full list of available translations for "
              . "\$translated and returns an associative array in which each one is of class "
              . "\\$subclass_namespace\\$subclass_name."
        );
        $fragment->addComment(
                '@return false|null|array Returns an associative array indexed by the language Id, '
              . 'null if no languages are available, or false if $translated cannot be identified.'
        );
        $fragment->addParam(
                'translated', null, false, false, 'object',
                'Translated object to retrieve translations'
        );
        $fragment->addFragment($output);

        $this->addFragment($fragment);

        $this->getDocument()->addUse('\nabu\data\lang\CNabuLanguage');
        $this->getDocument()->addUse('\\' . $subclass_namespace . '\\' . $subclass_name);
        $this->getDocument()->addUse('\\' . $subclass_namespace . '\\' . $subclass_name . 'List');
    }

    private function buildBodyForTranslatedObjectMethods(
        $translated_table,
        $translated_namespace,
        $translated_class,
        $final_class,
        $is_translation = false
    ) {
        $primary = $this->translated_desc->getPrimaryConstraintFieldNames();

        foreach ($primary as $key_field) {
            $output[] = "\$$key_field = nb_getMixedValue(";
            $output[] = "        \$translated,";
            $output[] = "        '$key_field',";
            $output[] = "        '\\\\" . str_replace('\\', '\\\\', $translated_namespace) . "\\\\$translated_class'";
            $output[] = ");";
        }

        $c = count($primary);
        foreach ($primary as $key_field) {
            $desc = $this->translated_desc->getField($key_field);
            switch ($desc['data_type']) {
                case 'int':
                    $comp = "is_numeric(\$$key_field)";
                    break;
                case 'varchar':
                    $comp = "is_string(\$$key_field)";
                    break;
            }
            $output[] = ($c === count($primary) ? 'if (' : '    ')
                      . $comp
                      . ($c > 1 ? ' &&' : (count($primary) === 1 ? ') {' : ''));
            $c--;
        }
        if (count($primary) > 1) {
            $output[] = '   )';
            $output[] = '{';
        }

        $output[] = "    \$retval = $final_class::buildObjectListFromSQL(";
        $output[] = "            '" . NABU_LANG_FIELD_ID . "',";
        $output[] = "            'select " . ($is_translation ? 't2' : 'l') . ".* '";
        $output[] = "            . 'from " . NABU_LANG_TABLE . " l, $translated_table t1, $this->table t2 '";

        $aux = array();
        foreach ($primary as $key_field) {
            $desc = $this->translated_desc->getField($key_field);
            switch ($desc['data_type']) {
                case 'int':
                    $comp1 = "t1.$key_field=t2.$key_field";
                    $comp2 = "t1.$key_field=%$key_field\$d";
                    break;
                case 'string':
                    $comp1 = "t1.$key_field=t2.$key_field";
                    $comp2 = "t1.$key_field='%$key_field\$s'";
                    break;
                default:
                    $comp = null;
            }
            $aux[] = (count($aux) === 0 ? "           . 'where " : "             . 'and ") . $comp1 . " '";
            $aux[] = "             . 'and $comp2 '";
            $aux[] = "             . 'and l." . NABU_LANG_FIELD_ID . "=t2." . NABU_LANG_FIELD_ID . " '";
        }

        if ($this->getStorageDescriptor()->hasField($this->table . '_order')) {
            $aux[] = "           . 'order by t2.$this->table" . "_order'";
        }
        $aux[count($aux) - 1] = $aux[count($aux) - 1] . ',';
        $output = array_merge($output, $aux);
        $output[] = '            array(';
        $c = count($primary);
        foreach ($primary as $key_field) {
            $output[] = "                '$key_field' => \$$key_field" . ($c-- > 1 ? ',' : '');
        }
        $output[] = '            )';
        $output[] = '    );';

        if ($is_translation) {
            $output[] = '    $retval->iterate(';
            $output[] = '        function ($key, $nb_translation) use($translated) {';
            $output[] = '            $nb_translation->setTranslatedObject($translated);';
            $output[] = '        }';
            $output[] = '    );';
        }

        $output[] = '} else {';
        $output[] = "    \$retval = new $final_class" . 'List();';
        $output[] = '}';
        $output[] = '';
        $output[] = 'return $retval;';

        return $output;
    }
}
