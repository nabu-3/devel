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
use nabu\sdk\builders\php\CNabuPHPConstantBuilder;
use nabu\sdk\builders\php\CNabuPHPConstructorBuilder;
use nabu\core\exceptions\ENabuCoreException;
use nabu\db\interfaces\INabuDBDescriptor;

/**
 * Class to create class list intances.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @version 3.0.0 Surface
 * @package \nabu\sdk\builders\nabu
 */
class CNabuPHPClassListBuilder extends CNabuPHPClassTableAbstractBuilder
{
    /**
     * Class name of each item.
     * @var string
     */
    private $item_class_name = null;
    /**
     * Entity name of each item.
     * @var string
     */
    private $item_entity_name = null;
    /**
     * Field name to act as ID of the list.
     * @var string
     */
    private $table_field_id = null;
    /**
     * Flag to determine if we have a secondary index by key.
     * @var bool
     */
    private $have_key_index = false;

    /**
     * The constructor checks if all parameters have valid values, and throws an exception if not.
     * @param CNabuAbstractBuilder $container Container builder object.
     * @param INabuDBDescriptor $table_desc Storage descriptor instance.
     * @param string $namespace Namespace of the class to be generated.
     * @param string $name Class name to be generated without namespace.
     * @param string $entity_name Entity name. This value is used for comment purposes.
     * @param string $item_class_name Class name of each item.
     * @param string $item_entity_name Entity name of each item.
     * @param bool $abstract Defines if the class is abstract or not.
     * @throws ENabuCoreException Throws an exception if some parameter is not valid.
     */
    public function __construct(
        CNabuAbstractBuilder $container,
        INabuDBDescriptor $table_desc,
        $namespace,
        $name,
        $entity_name,
        $item_class_name,
        $item_entity_name,
        $abstract = false
    ) {
        if (strlen($item_class_name) === 0) {
            throw new ENabuCoreException(
                    ENabuCoreException::ERROR_CONSTRUCTOR_PARAMETER_IS_EMPTY,
                    array('$item_class_name')
            );
        }

        if (strlen($item_entity_name) === 0) {
            throw new ENabuCoreException(
                    ENabuCoreException::ERROR_CONSTRUCTOR_PARAMETER_IS_EMPTY,
                    array('$item_entity_name')
            );
        }

        $this->item_class_name = $item_class_name;
        $this->item_entity_name = $item_entity_name;

        parent::__construct($container, $table_desc, $namespace, $name, $entity_name, $abstract);
    }

    /**
     * Check the Primary Key to determine if class can be built.
     * @return bool Returns true if the Primary Key is valid.
     */
    public function checkPrimaryKey()
    {
        if (($retval = $this->checkPrimaryConstraint())) {
            if ($this->is_translation) {
                $this->table_field_id = NABU_LANG_FIELD_ID;
            } else {
                $keys = $this->getStorageDescriptor()->getPrimaryConstraintFieldNames();
                $size = count($keys);
                if ($size > 0) {
                    $this->table_field_id = $keys[$size - 1];
                } else {
                    $this->table_field_id = null;
                    $this->is_translation = false;
                }
            }
        } else {
            $this->table_field_id = null;
            $this->is_translation = false;
        }

        return $retval;
    }

    public function checkSecondaryIndexes()
    {

    }

    /**
     * Prepares the class and define it in memory to be serialized after.
     * @param string $author_name Name of the author to place in class comments
     * @param string $author_email e-Mail of the author to place in class comments
     * @return bool Return true if the table was prepared or false if not.
     */
    public function prepare($author_name = false, $author_email = false)
    {
        if ($this->checkPrimaryKey()) {
            $this->getDocument()->addUse('\\nabu\\data\\CNabuDataObjectList');
            $this->setExtends('CNabuDataObjectList');

            $this->addComment("Class to manage a list of $this->item_entity_name instances.");
            if ($author_name !== null || $author_email !== null) {
                $this->addComment("@author"
                                 . (is_string($author_name) ? ' ' . $author_name : '')
                                 . (is_string($author_email) ? " <$author_email>" : '')
                );
            }
            $this->addComment("@version " . NABU_VERSION);
            $this->addComment("@package " . '\\' . $this->class_namespace);

            $this->prepareClassConstants();
            $this->prepareConstructor();
            $this->prepareCreateSecondaryIndexes();
            $this->prepareAcquireItem();

            $retval = true;
        } else {
            $retval = false;
        }

        return $retval;
    }

    /**
     * Prepare class constants.
     */
    private function prepareClassConstants()
    {
        $descriptor = $this->getStorageDescriptor();

        if ($descriptor->hasField($descriptor->getStorageName() . '_key')) {
            $fragment = new CNabuPHPConstantBuilder($this, 'INDEX_KEY', 'keys', 'string');
            $fragment->addComment('Index the list using the key field.');
            $this->addFragment($fragment);
        }
    }

    /**
     * Prepare the constructor of the class to be built.
     */
    private function prepareConstructor()
    {
        $fragment = new CNabuPHPConstructorBuilder($this);
        $fragment->addComment('Instantiates the class.');
        $fragment->addFragment("parent::__construct('$this->table_field_id');");

        $this->addFragment($fragment);
    }

    /**
     * Prepare method createSecondaryIndexes().
     */
    private function prepareCreateSecondaryIndexes()
    {
        $fragment = new CNabuPHPMethodBuilder(
            $this, 'createSecondaryIndexes', CNabuPHPMethodBuilder::METHOD_PROTECTED
        );
        $fragment->addComment('Creates alternate indexes for this list.');

        $descriptor = $this->getStorageDescriptor();
        $field = $descriptor->getStorageName() . '_key';

        if ($descriptor->hasField($field)) {
            $order = $descriptor->getStorageName() . '_order';
            if ($descriptor->hasField($order)) {
                $order = false;
            }
            $fragment->addFragment(array(
                "\$this->addIndex(",
                "    new CNabuDataObjectListIndex(\$this, '$field', "
                . ($order ? "'$order'" : 'null')
                . ", self::INDEX_KEY)",
                ");"
            ));
            $this->getDocument()->addUse('\nabu\data\CNabuDataObjectListIndex');
        }

        $this->addFragment($fragment);
    }

    /**
     * Prepare the method acquireItem() of the class to be built.
     */
    private function prepareAcquireItem()
    {
        $keyed_namespace = nb_strEndsWith($this->class_namespace, '\base')
                         ? preg_replace('/\\\\base$/', '', $this->class_namespace)
                         : $this->class_namespace
        ;
        $this->getDocument()->addUse('\\' . $keyed_namespace . '\\' . $this->item_class_name);
        $this->getDocument()->adduse('\nabu\core\CNabuEngine');

        $fragment = new CNabuPHPMethodBuilder($this, 'acquireItem');
        $fragment->addComment(
            "Acquires an instance of class $this->item_class_name from the database."
        );
        $fragment->addComment(
            '@return mixed Returns the unserialized instance if exists or false if not.'
        );
        $fragment->addParam(
            'key', null, false, false, 'string', 'Id or reference field in the instance to acquire.'
        );
        $fragment->addParam(
            'index', null, true, false, 'string', 'Secondary index to be used if needed.'
        );
        $fragment->addFragment(array(
            '$retval = false;',
            '',
            'if ($index === false && CNabuEngine::getEngine()->isMainDBAvailable()) {',
            "    \$item = new $this->item_class_name(\$key);",
            '    if ($item->isFetched()) {',
            '        $retval = $item;',
            '    }',
            '}',
            '',
            'return $retval;'
        ));

        $this->addFragment($fragment);
    }
}
