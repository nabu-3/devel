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

namespace nabu\sdk\builders\nabu\base;
use nabu\db\exceptions\ENabuDBException;
use nabu\db\interfaces\INabuDBConnector;
use nabu\db\interfaces\INabuDBSyntaxBuilder;
use nabu\sdk\builders\CNabuAbstractBuilder;
use nabu\sdk\builders\php\CNabuPHPClassBuilder;
use nabu\core\exceptions\ENabuCoreException;
use nabu\db\interfaces\INabuDBDescriptor;

/**
 * This class implements a lot of methods shared between different child classes.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.0 Surface
 * @version 3.0.12 Surface
 * @package \nabu\sdk\builders\nabu\base
 */
class CNabuPHPClassTableAbstractBuilder extends CNabuPHPClassBuilder
{
    /** @var string $entity_name Entity Name used for comment purposes. */
    protected $entity_name = null;
    /** @var string $class_namespace Class Namespace. */
    protected $class_namespace = null;
    /** @var string $table_name Name of the table. */
    protected $table_name = null;
    /** @var string $schema_name Name of the schema. */
    protected $schema_name = null;
    /** @var string $translated_table Main translated table name. */
    protected $translated_table = null;
    /** @var bool $is_translated Flag to determine if entity have translations. */
    protected $is_translated = false;
    /** @var bool $is_translation Flag to determine if entity are translations. */
    protected $is_translation = false;
    /** @var bool $is_hashed Flag to determine if table have a hash field. */
    protected $is_hashed = false;
    /** @var bool $is_keyed Flag to determine if table have a key field. */
    protected $is_keyed = false;
    /** @var bool $is_ordered Flag to determine if table have a order field. */
    protected $is_ordered = false;
    /** @var bool $is_customer_child Flag to determine if entity is a Customer child entity. */
    protected $is_customer_child = false;
    /** @var bool $is_customer_foreign Flag to determine if entity is a Customer foreign entity. */
    protected $is_customer_foreign = false;
    /** @var bool $is_site_child Flag to determine if entity is a Site child entity. */
    protected $is_site_child = false;
    /** @var bool $is_site_foreign Flag to determine if entity is a Site foreign entity. */
    protected $is_site_foreign = false;
    /** @var bool $is_site_target_child Flag to determine if is a Site Target child entity. */
    protected $is_site_target_child = false;
    /** @var bool $is_site_target_foreign Flag to determine if is a Site Target foreign entity. */
    protected $is_site_target_foreign = false;
    /** @var bool $is_commerce_child Flag to determine if is a Commerce child entity. */
    protected $is_commerce_child = false;
    /** @var bool $is_commerce_foreign Flag to determine if is a Commerce foreign entity. */
    protected $is_commerce_foreign = false;
    /** @var bool $is_catalog_child Flag to determine if is a Catalog child entity. */
    protected $is_catalog_child = false;
    /** @var bool $is_catalog_foreign Flag to determine if is a Catalog foreign entity. */
    protected $is_catalog_foreign = false;
    /** @var bool $is_medioteca_child Flag to determine if is a Medioteca child entity. */
    protected $is_medioteca_child = false;
    /** @var bool $is_medioteca_foreign Flag to determine if is a Medioteca foreign entity. */
    protected $is_medioteca_foreign = false;
    /** @var bool $is_messaging_child Flag to determine if entity is a Messaging child entity. */
    protected $is_messaging_child = false;
    /** @var bool $is_messaging_foreign Flat to determine if entity is a Messaging foreign entity. */
    protected $is_messaging_foreign = false;
    /** @var bool $is_messaging_service_child Flag to determine if entity is a Messaging Service child entity. */
    protected $is_messaging_service_child = false;
    /** @var bool $is_messaging_service_foreign Flag to determine if entity is a Messaging Service foreign entity. */
    protected $is_messaging_service_foreign = false;
    /** @var bool $is_project_child Flag to determine if entity is a Project child entity. */
    protected $is_project_child = false;
    /** @var bool $is_project_foreign Flag to determine if entity is a Project foreign entity. */
    protected $is_project_foreign = false;
    /** @var bool $is_role_child Flag to determine if is a Role child entity. */
    protected $is_role_child = false;
    /** @var bool $is_role_foreign Flag to determine if is a Role foreign entity. */
    protected $is_role_foreign = false;

    /** @var array $dictionary Dictionary for table to entity conversion names. */
    protected $dictionary = false;

    /** @var INabuDBConnector $connector Database connector instance. */
    private $connector = null;
    /** @var INabuDBSyntaxBuilder $db_syntax Database Syntax Builder. */
    private $db_syntax = null;
    /** @var INabuDBDescriptor $table_descriptor Table descriptor in array raw format. */
    private $table_descriptor = null;
    /** @var string $translated_desc Main translated table descriptor. */
    private $translated_desc = null;

    /**
     * The constructor checks if all parameters have valid values, and throws an exception if not.
     * @param CNabuAbstractBuilder $container Container builder object
     * @param string $namespace Namespace of the class to be generated
     * @param string $name Class name to be generated without namespace
     * @param string $entity_name Entity name. This value is used for comment purposes
     * @param INabuDBDescriptor $table_descriptor Table descriptor instance.
     * @param bool $abstract Defines if the class is abstract or not
     * @throws ENabuCoreException Throws an exception if some parameter or the primary key are not valids.
     */
    public function __construct(
        CNabuAbstractBuilder $container,
        INabuDBConnector $connector,
        string $namespace,
        string $name,
        string $entity_name,
        string $table,
        string $schema = null,
        bool $abstract = false
    ) {
        parent::__construct($container, $name, $abstract);

        if (strlen($namespace) === 0) {
            throw new ENabuCoreException(
                    ENabuCoreException::ERROR_CONSTRUCTOR_PARAMETER_IS_EMPTY,
                    array('$entity_namespace')
            );
        }

        if (strlen($entity_name) === 0) {
            throw new ENabuCoreException(
                    ENabuCoreException::ERROR_CONSTRUCTOR_PARAMETER_IS_EMPTY,
                    array('$entity_name')
            );
        }

        $this->class_namespace = $namespace;
        $this->entity_name = $entity_name;

        $this->table_name = $table;
        $this->schema_name = $schema;

        $this->connector = $connector;
        $this->db_syntax = $this->connector->getSyntaxBuilder();
        $this->table_descriptor = $this->db_syntax->describeStorage($this->table_name, $this->schema_name);

        if (!$this->table_descriptor) {
            throw new ENabuDBException(ENabuDBException::ERROR_STORAGE_DESCRIPTOR_NOT_AVAILABLE);
        }

        if (!$this->checkTable()) {
            throw new ENabuCoreException(ENabuCoreException::ERROR_CLASS_CANNOT_BE_BUILT, array($this->name));
        }

        $this->checkSecondaryConstraints();
    }

    /**
     * Dump in the error stream all statuses of this object.
     * @param string $padding Default padding to insert before each line dumped.
     */
    public function dumpStatus($padding)
    {
        error_log("");
        error_log($padding . "Translated: " . ($this->is_translated ? 'YES' : 'NO'));
        error_log($padding . "Translation: " . ($this->is_translation ? 'YES' : 'NO'));
        error_log($padding . "Hash: " . ($this->is_hashed ? 'YES' : 'NO'));
        error_log($padding . "Customer Child: " . ($this->is_customer_child ? 'YES' : 'NO'));
        error_log($padding . "Customer Foreign: " . ($this->is_customer_foreign ? 'YES' : 'NO'));
        error_log($padding . "Commerce Child: " . ($this->is_commerce_child ? 'YES' : 'NO'));
        error_log($padding . "Commerce Foreign: " . ($this->is_commerce_foreign ? 'YES' : 'NO'));
        error_log($padding . "Catalog Child: " . ($this->is_catalog_child ? 'YES' : 'NO'));
        error_log($padding . "Catalog Foreign: " . ($this->is_catalog_foreign ? 'YES' : 'NO'));
        error_log($padding . "Site Child: " . ($this->is_site_child ? 'YES' : 'NO'));
        error_log($padding . "Site Foreign: " . ($this->is_site_foreign ? 'YES' : 'NO'));
        error_log($padding . "Site Target Child: " . ($this->is_site_target_child ? 'YES' : 'NO'));
        error_log($padding . "Site Target Foreign: " . ($this->is_site_target_foreign ? 'YES' : 'NO'));
        error_log($padding . "Medioteca Child: " . ($this->is_medioteca_child ? 'YES' : 'NO'));
        error_log($padding . "Medioteca Foreign: " . ($this->is_medioteca_foreign ? 'YES' : 'NO'));
        error_log($padding . "Messaging Child: " . ($this->is_messaging_child ? 'YES' : 'NO'));
        error_log($padding . "Messaging Foreign: " . ($this->is_messaging_foreign ? 'YES' : 'NO'));
        error_log($padding . "Messaging Service Child: " . ($this->is_messaging_service_child ? 'YES' : 'NO'));
        error_log($padding . "Messaging Service Foreign: " . ($this->is_messaging_service_foreign ? 'YES' : 'NO'));
        error_log($padding . "Project Child: " . ($this->is_project_child ? 'YES' : 'NO'));
        error_log($padding . "Project Foreign: " . ($this->is_project_foreign ? 'YES' : 'NO'));
        error_log($padding . "Role Child: " . ($this->is_role_child ? 'YES' : 'NO'));
        error_log($padding . "Role Foreign: " . ($this->is_role_foreign ? 'YES' : 'NO'));
    }

    /**
     * Gets current dictionary
     * @return array Returns the dictionary if exists or null if none.
     */
    public function getDictionary()
    {
        return $this->dictionary;
    }

    /**
     * Stablishes the dictionary to be used to transform table names into entity or method names
     * @param array $dictionary
     */
    public function setDictionary($dictionary)
    {
        $this->dictionary = $dictionary;
    }

    /**
     * Get the Storage Descriptor of the table.
     * @return INabuDBDescriptor Returns the descriptor instance if exists or null if not.
     */
    public function getStorageDescriptor() : INabuDBDescriptor
    {
        return $this->table_descriptor;
    }

    /**
     * Get the Storage Descriptor of the translated table.
     * @return INabuDBDescriptor Returns the descriptor instance if exists or null if not.
     */
    public function getTranslatedDescriptor() : INabuDBDescriptor
    {
        return $this->translated_desc;
    }

    /**
     * Checks the table descriptor integrity and the validity of the primary key.
     * @return bool Returns true if the descriptor is valid.
     */
    public function checkTable()
    {
        return is_string($this->table_name = $this->table_descriptor->getStorageName()) &&
            $this->table_descriptor->hasFields() &&
            $this->checkPrimaryConstraint()
        ;
    }

    /**
     * Check the Primary Constraint to determine if class can be built.
     * @return bool Returns true if the Primary Key is valid.
     */
    public function checkPrimaryConstraint()
    {
        $retval = false;
        $this->is_translation = false;

        if (($retval = $this->table_descriptor->hasPrimaryConstraint())) {
            if ($this->table_descriptor->getPrimaryConstraintSize() > 1) {
                $this->is_translation =
                    $this->table_descriptor->hasPrimaryConstraintField(NABU_LANG_FIELD_ID, 2);
                $this->is_customer_child =
                    $this->checkPrimaryRelationship(NABU_CUSTOMER_TABLE, NABU_CUSTOMER_FIELD_ID);
                $this->is_commerce_child =
                    $this->checkPrimaryRelationship(NABU_COMMERCE_TABLE, NABU_COMMERCE_FIELD_ID);
                $this->is_catalog_child =
                    $this->checkPrimaryRelationship(NABU_CATALOG_TABLE, NABU_CATALOG_FIELD_ID);
                $this->is_site_child =
                    $this->checkPrimaryRelationship(NABU_SITE_TABLE, NABU_SITE_FIELD_ID);
                $this->is_site_target_child =
                    $this->checkPrimaryRelationship(NABU_SITE_TARGET_TABLE, NABU_SITE_TARGET_FIELD_ID);
                $this->is_medioteca_child =
                    $this->checkPrimaryRelationship(NABU_MEDIOTECA_TABLE, NABU_MEDIOTECA_FIELD_ID);
                $this->is_messaging_child =
                    $this->checkPrimaryRelationship(NABU_MESSAGING_TABLE, NABU_MESSAGING_FIELD_ID);
                $this->is_messaging_service_child =
                    $this->checkPrimaryRelationship(NABU_MESSAGING_SERVICE_TABLE, NABU_MESSAGING_SERVICE_FIELD_ID);
                $this->is_project_child =
                    $this->checkPrimaryRelationship(NABU_PROJECT_TABLE, NABU_PROJECT_FIELD_ID);
                $this->is_role_child =
                    $this->checkPrimaryRelationship(NABU_ROLE_TABLE, NABU_ROLE_FIELD_ID);
            }
        }

        return $retval;
    }

    /**
     * Check if a table is related with another by their Primary Constraint.
     * @param string $table Table name to match.
     * @param string $field Field in $table to match.
     * @param false|int $position If setted, then determines the position where the field would be present. By default in
     * first position. If false, any position is valid.
     * @return bool Returns true if both tables are connected.
     */
    protected function checkPrimaryRelationship($table, $field, $position = 1)
    {
        return $this->table_name !== $table &&
            nb_strStartsWith($this->table_name, $table . '_') &&
            $this->table_descriptor->hasPrimaryConstraintField($field, $position)
        ;
    }

    /**
     * Check if secondary constraints exists and they are related with main entities of Nabu.
     */
    public function checkSecondaryConstraints()
    {
        $this->is_hashed = $this->checkSecondaryRelationship($this->table_descriptor->getStorageName() . '_hash');
        $this->is_keyed = $this->checkSecondaryRelationship($this->table_descriptor->getStorageName() . '_key');
        $this->is_ordered = $this->checkSecondaryRelationship($this->table_descriptor->getStorageName() . '_order');

        $this->is_customer_foreign = $this->checkSecondaryRelationship(NABU_CUSTOMER_FIELD_ID);
        $this->is_commerce_foreign = $this->checkSecondaryRelationship(NABU_COMMERCE_FIELD_ID);
        $this->is_catalog_foreign = $this->checkSecondaryRelationship(NABU_CATALOG_FIELD_ID);
        $this->is_site_foreign = $this->checkSecondaryRelationship(NABU_SITE_FIELD_ID);
        $this->is_site_target_foreign = $this->checkSecondaryRelationship(NABU_SITE_TARGET_FIELD_ID);
        $this->is_medioteca_foreign = $this->checkSecondaryRelationship(NABU_MEDIOTECA_FIELD_ID);
        $this->is_messaging_foreign = $this->checkSecondaryRelationship(NABU_MESSAGING_FIELD_ID);
        $this->is_messaging_service_foreign = $this->checkSecondaryRelationship(NABU_MESSAGING_SERVICE_FIELD_ID);
        $this->is_project_foreign = $this->checkSecondaryRelationship(NABU_PROJECT_FIELD_ID);
        $this->is_role_foreign = $this->checkSecondaryRelationship(NABU_ROLE_FIELD_ID);
    }

    /**
     * Check if table have and index related with other table and associate it.
     * @param string $field Field in $table to match.
     * @return bool Returns true if both tables are connected..
     */
    public function checkSecondaryRelationship($field)
    {
        return $this->table_descriptor->hasSecondaryConstraints() &&
            $this->table_descriptor->hasField($field) &&
            !$this->table_descriptor->hasPrimaryConstraintField($field) &&
            $this->table_descriptor->hasSecondaryConstraintWithFields(array($field))
        ;
    }

    /**
     * Check if the table is a translated table (have lang sub tables).
     * @return bool Returns true if is a translated table.
     */
    protected function checkForTranslatedTable() : bool
    {
        $main_table = $this->getStorageDescriptor();
        if (!nb_strEndsWith($this->table_name, NABU_LANG_TABLE_SUFFIX) &&
            $main_table->hasPrimaryConstraint() &&
            ($child_table = $this->db_syntax->describeStorage(
                $this->table_name . NABU_LANG_TABLE_SUFFIX, $this->schema_name)
            ) !== null &&
            $child_table->hasPrimaryConstraint() &&
            $child_table->getPrimaryConstraintSize() === $main_table->getPrimaryConstraintSize() + 1
        ) {
            $key_diff = array_diff(
                $child_table->getPrimaryConstraintFieldNames(),
                $main_table->getPrimaryConstraintFieldNames()
            );
            $this->is_translated = (count($key_diff) === 1 && array_shift($key_diff) === NABU_LANG_FIELD_ID);
        }

        return $this->is_translated;
    }

    /**
     * Check if the table is a translation of a main table (is a lang sub table).
     * @return bool Returns true if is a translation table.
     */
    protected function checkForTranslationTable() : bool
    {
        if (nb_strEndsWith($this->table_name, NABU_LANG_TABLE_SUFFIX)) {
            $table_descriptor = $this->getStorageDescriptor();
            $this->translated_table = substr($this->table_name, 0, strlen($this->table_name) - strlen(NABU_LANG_TABLE_SUFFIX));
            if ($table_descriptor->hasPrimaryConstraint() &&
                ($this->translated_desc = $this->db_syntax->describeStorage($this->translated_table, $this->schema_name)) &&
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
     * Check if the table is a master of translations table.
     * @return bool Returns true if it is master and false if not.
     */
    public function isTranslatedTable()
    {
        return $this->is_translated;
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
     * Check if the table have a hash field.
     * @return bool Returns true if table have a hash field and false if not.
     */
    public function isHashed()
    {
        return $this->is_hashed;
    }

    /**
     * Check if the table have a key field.
     * @return bool Returns true if table have a key field and false if not.
     */
    public function isKeyed()
    {
        return $this->is_keyed;
    }

    /**
     * Check if the table have a order field.
     * @return bool Returns true if table have a order field and false if not.
     */
    public function isOrdered()
    {
        return $this->is_ordered;
    }
}
