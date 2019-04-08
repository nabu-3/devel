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

namespace nabu\sdk\builders\nabu;
use nabu\core\exceptions\ENabuCoreException;
use nabu\db\interfaces\INabuDBConnector;
use nabu\sdk\builders\CNabuAbstractBuilder;
use nabu\sdk\builders\nabu\base\CNabuPHPClassTableAbstractBuilder;
use nabu\sdk\builders\php\CNabuPHPMethodBuilder;
use nabu\sdk\builders\php\CNabuPHPConstructorBuilder;

/**
 * This class is specialized in create a class skeleton code file from a table that exists in the database to translate
 * their contents from/to XML. Created class is fulfilled with a large set of methods to use it as "Nabu Style" classes.
 * Normally, after create a class we recommend to subclass it to extend functionalities, allowing to you to recreate
 * the base class each time that your table changes or when nabu-3 will increase the set of methods.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.12 Surface
 * @version 3.0.12 Surface
 * @package \nabu\sdk\builders\nabu
 */
class CNabuPHPClassTableXMLBuilder extends CNabuPHPClassTableAbstractBuilder
{
    /** @var string $element XML Element name. */
    private $element = null;
    /** @var array $element_attributes XML Element attributes to be transported automatically. */
    private $element_attributes = null;
    /** @var array $element_childs XML Element childs to be transported automatically. */
    private $element_childs = null;
    /** @var string $class_data_namespace Namespace of Class that represents the managed data entity. */
    private $class_data_namespace = null;
    /** @var string $class_data_name Class descending of CNabuDataObject that represents the managed data entity. */
    private $class_data_name = null;
    /** @var string $since_version Since version for comments. */
    private $since_version = null;

    /**
     * The constructor checks if all parameters have valid values, and throws an exception if not.
     * @param CNabuAbstractBuilder $container Container builder object.
     * @param INabuDBConnector $connector Database connector to acquire table.
     * @param string $class_namespace Namespace of the class to be generated.
     * @param string $class_name Class name to be generated without namespace.
     * @param string $entity_name Entity name. This value is used for comment purposes.
     * @param string $element_name XML Element name.
     * @param array $element_attributes Attributes to place directly in getAttributes() generated method.
     * @param array $element_childs Childs to place directly in getChilds() generated method.
     * @param string $class_data_namespace Namespace of the instance data to be managed.
     * @param string $class_data_name Class name descending from CNabuDataObject of the instance data to be managed.
     * @param string $table_name Table name to be extracted.
     * @param string $schema_name Scheme name of the table.
     * @param bool $abstract Defines if the class is abstract or not.
     * @param string $since_version Since version for comments.
     * @throws ENabuCoreException Throws an exception if some parameter is not valid.
     */
    public function __construct(
        CNabuAbstractBuilder $container,
        INabuDBConnector $connector,
        string $class_namespace,
        string $class_name,
        string $entity_name,
        string $element_name,
        array $element_attributes = null,
        array $element_childs = null,
        string $class_data_namespace,
        string $class_data_name,
        string $table_name,
        string $schema_name = null,
        bool $abstract = false,
        string $since_version = null
    ) {
        if (strlen($class_name) === 0) {
            throw new ENabuCoreException(
                ENabuCoreException::ERROR_CONSTRUCTOR_PARAMETER_IS_EMPTY,
                array('$name')
            );
        }

        if (strlen($element_name) === 0) {
            throw new ENabuCoreException(
                ENabuCoreException::ERROR_CONSTRUCTOR_PARAMETER_IS_EMPTY,
                array('$element_name')
            );
        }

        if (strlen($class_data_name) === 0) {
            throw new ENabuCoreException(
                ENabuCoreException::ERROR_CONSTRUCTOR_PARAMETER_IS_EMPTY,
                array('$class_data_name')
            );
        }

        if (strlen($table_name) === 0) {
            throw new ENabuCoreException(
                    ENabuCoreException::ERROR_CONSTRUCTOR_PARAMETER_IS_EMPTY,
                    array('$table')
            );
        }

        $this->class_data_namespace = $class_data_namespace;
        $this->class_data_name = $class_data_name;

        $this->element = $element_name;
        $this->element_attributes = $element_attributes;
        $this->element_childs = $element_childs;

        $this->since_version = $since_version;

        parent::__construct(
            $container, $connector, $class_namespace, $class_name, $entity_name,
            $table_name, $schema_name, $abstract
        );
    }

    /**
     * Prepares the class and define it in memory to be serialized after.
     * @param string $author_name Name of the author to place in class comments
     * @param string $author_email e-Mail of the author to place in class comments
     * @return bool Return true if the XML was prepared or false if not.
     */
    public function prepare(string $author_name = null, string $author_email = null) : bool
    {
        $this->prepareClassComments($author_name, $author_email);
        $this->prepareClassDeclaration();
        $this->prepareConstructor();

        if (!$this->is_translation) {
            $this->prepareGetTagName();
        }

        if ($this->is_translated) {
            $this->prepareCreateXMLTranslationsObject();
        }

        $this->prepareLocateDataObject();

        if (!$this->is_translation) {
            $this->prepareGetAttributes();
            $this->prepareSetAttributes();
        } else {
            $this->prepareGetAttributesForTranslation();
            $this->prepareSetAttributesForTranslation();
        }

        $this->prepareGetChilds();
        $this->prepareSetChilds();

        return true;
    }

    /**
     * Prepare the class header comments.
     * @param string $author_name Name of the author to place in class comments
     * @param string $author_email e-Mail of the author to place in class comments
     */
    private function prepareClassComments(string $author_name = null, string $author_email = null)
    {
        $this->addComment(
                "Class to manage the $this->entity_name as a XML branch."
        );

        if ($author_name !== null || $author_email !== null) {
            $this->addComment(
                trim("@author"
                     . (is_string($author_name) ? ' ' . $author_name : '')
                     . (is_string($author_email) ? " <$author_email>" : '')
                )
            );
        }

        if ($this->since_version !== null && strlen($this->since_version) > 0) {
            $this->addComment("@since $this->since_version");
        }

        $this->addComment('@version ' . NABU_VERSION);
        $this->addComment("@package \\$this->class_namespace");
    }

    /**
     * Prepare the class declaration.
     */
    private function prepareClassDeclaration()
    {
        $this->isHashed();

        if ($this->checkForTranslatedTable()) {
            $this->getDocument()->addUse('\nabu\xml\lang\CNabuXMLTranslated');
            $this->setExtends('CNabuXMLTranslated');
        } elseif ($this->checkForTranslationTable()) {
            $this->getDocument()->addUse('\nabu\xml\lang\CNabuXMLTranslation');
            $this->setExtends('CNabuXMLTranslation');
        } else {
            $this->getDocument()->addUse('\nabu\xml\CNabuXMLDataObject');
            $this->setExtends('CNabuXMLDataObject');
        }
    }

    /**
     * Prepares the constructor of class.
     */
    private function prepareConstructor()
    {
        $table_descriptor = $this->getStorageDescriptor();

        $fragment = new CNabuPHPConstructorBuilder($this);
        $fragment->addComment(
                "Instantiates the class. Receives as parameter a qualified $this->class_data_name class.");
        $fragment->addParam(
            $this->table_name, $this->class_data_name, true, null, $this->class_data_name,
            '$this->entity_name instance to be managed as XML'
        );
        $this->getDocument()->addUse('\\' . $this->class_data_namespace . '\\' . $this->class_data_name);

        $fragment->addFragment("parent::__construct(\$$this->table_name);");

        $this->addFragment($fragment);
    }

    /**
     * Prepares the getTagName method.
     */
    private function prepareGetTagName()
    {
        $fragment = new CNabuPHPMethodBuilder(
            $this, 'getTagName', CNabuPHPMethodBuilder::METHOD_PROTECTED, true, false, false, true, 'string'
        );
        $fragment->addComment("Static method to get the Tag name of this XML Element.");
        $fragment->addComment("@return string Return the Tag name.");

        $fragment->addFragment("return '$this->element';");

        $this->addFragment($fragment);
    }

    /**
     * Prepares the locateDataObjcet method.
     */
    private function prepareLocateDataObject()
    {
        $fragment = new CNabuPHPMethodBuilder(
            $this, 'locateDataObject', CNabuPHPMethodBuilder::METHOD_PROTECTED,
            false, false, false, true, 'bool'
        );
        $fragment->addComment("Locate a Data Object.");
        $fragment->addComment("@return bool Returns true if the Data Object found or false if not.");

        $fragment->addParam(
            'element', 'SimpleXMLElement', false, null, 'SimpleXMLElement', 'Element to locate the Data Object.'
        );
        $fragment->addParam(
            'data_parent', 'CNabuDataObject', true, null, 'CNabuDataObject', 'Data Parent object.'
        );

        $fragment->addFragment(array(
            '$retval = false;',
            '',
            "if (isset(\$element['GUID'])) {",
            "    \$guid = (string)\$element['GUID'];",
            "    if (!(\$this->nb_data_object instanceof $this->class_data_name)) {",
            "        \$this->nb_data_object = $this->class_data_name::findByHash(\$guid);",
            '    } else {',
            '        $this->nb_data_object = null;',
            '    }',
            '',
            "    if (!(\$this->nb_data_object instanceof $this->class_data_name)) {",
            "        \$this->nb_data_object = new $this->class_data_name();",
            "        \$this->nb_data_object->setHash(\$guid);",
            '    }',
            '    $retval = true;',
            '}',
            '',
            'return $retval;'
        ));

        $this->getDocument()->addUse('\nabu\data\CNabuDataObject');
        $this->getDocument()->addUse("\\$this->class_data_namespace\\$this->class_data_name");

        $this->addFragment($fragment);
    }

    /**
     * Prepares the createXMLTranslationsObject method.
     */
    private function prepareCreateXMLTranslationsObject()
    {
        $fragment = new CNabuPHPMethodBuilder(
            $this, 'createXMLTranslationsObject', CNabuPHPMethodBuilder::METHOD_PROTECTED, false, false, false,
            true, 'CNabuXMLTranslationsList'
        );
        $fragment->addComment("Creates the XML instance to manage the translations list of this Element.");
        $fragment->addComment('@return CNabuXMLTranslationsList Returns the created instance.');

        $class_list = nb_strEndsWith($this->class_name, 'Base')
                    ? preg_replace('/Base$/', 'LanguageList', $this->class_name)
                    : $this->class_name . 'LanguageList'
        ;

        $class_list_ns = nb_strEndsWith($this->class_namespace, '\base')
                       ? preg_replace('/\\\\base$/', '', $this->class_namespace)
                       : $this->class_namespace
        ;
        $fragment->addFragment("return new $class_list(\$this->nb_data_object->getTranslations());");

        $this->getDocument()->addUse("\\$class_list_ns\\$class_list");
        $this->getDocument()->addUse('\nabu\xml\lang\CNabuXMLTranslationsList');

        $this->addFragment($fragment);
    }

    /**
     * Prepares the getAttributes method in case that is not a translation data entity.
     */
    private function prepareGetAttributes()
    {
        $fragment = new CNabuPHPMethodBuilder($this, 'getAttributes', CNabuPHPMethodBuilder::METHOD_PROTECTED);
        $fragment->addComment("Get default attributes of $this->entity_name from XML Element.");
        $fragment->addParam(
            'element', 'SimpleXMLElement', false, null, 'SimpleXMLElement',
            'XML Element to get attributes'
        );

        if ($this->is_hashed || is_array($this->element_attributes) && count($this->element_attributes) > 0) {
            $fragment->addFragment('$this->getAttributesFromList($element, array(');
            $count = count($this->element_attributes);
            /*if ($this->is_hashed) {
                $fragment->addFragment("    '{$this->table_name}_hash' => 'GUID'" . (--$count > 0 ? ',' : ''));
            }*/
            foreach ($this->element_attributes as $field => $attr) {
                $fragment->addFragment("    '$field' => '$attr'" . (--$count > 0 ? ',' : ''));
            }
            $fragment->addFragment('), false);');
        }

        $this->getDocument()->addUse('\SimpleXMLElement');

        $this->addFragment($fragment);
    }

    /**
     * Prepares the setAttributes method in case that is not a translation data entity.
     */
    private function prepareSetAttributes()
    {
        $fragment = new CNabuPHPMethodBuilder($this, 'setAttributes', CNabuPHPMethodBuilder::METHOD_PROTECTED);
        $fragment->addComment("Set default attributes of $this->entity_name in XML Element.");
        $fragment->addParam(
            'element', 'SimpleXMLElement', false, null, 'SimpleXMLElement',
            'XML Element to set attributes'
        );

        if ($this->is_hashed) {
            $fragment->addFragment(
                "\$element->addAttribute('GUID', \$this->nb_data_object->grantHash(true));"
            );
        }

        if (is_array($this->element_attributes) && count($this->element_attributes) > 0) {
            $fragment->addFragment('$this->putAttributesFromList($element, array(');
            $count = count($this->element_attributes);
            foreach ($this->element_attributes as $field => $attr) {
                $fragment->addFragment("    '$field' => '$attr'" . (--$count > 0 ? ',' : ''));
            }
            $fragment->addFragment('), false);');
        }

        $this->getDocument()->addUse('\SimpleXMLElement');

        $this->addFragment($fragment);
    }

    /**
     * Prepares the getAttributes method in case that is not a translation data entity.
     */
    private function prepareGetAttributesForTranslation()
    {
        $fragment = new CNabuPHPMethodBuilder($this, 'getAttributes', CNabuPHPMethodBuilder::METHOD_PROTECTED);
        $fragment->addComment("Get default attributes of $this->entity_name from XML Element.");
        $fragment->addParam(
            'element', 'SimpleXMLElement', false, null, 'SimpleXMLElement',
            'XML Element to get attributes'
        );

        if (is_array($this->element_attributes) && count($this->element_attributes) > 0) {
            $fragment->addFragment('$this->getAttributesFromList($element, array(');
            $count = count($this->element_attributes);
            foreach ($this->element_attributes as $field => $attr) {
                $fragment->addFragment("    '$field' => '$attr'" . (--$count > 0 ? ',' : ''));
            }
            $fragment->addFragment('), false);');
        }

        $this->getDocument()->addUse('\SimpleXMLElement');

        $this->addFragment($fragment);
    }

    /**
     * Prepares the setAttributes method in case that is a translation data entity.
     */
    private function prepareSetAttributesForTranslation()
    {
        $fragment = new CNabuPHPMethodBuilder($this, 'setAttributes', CNabuPHPMethodBuilder::METHOD_PROTECTED);
        $fragment->addComment("Set default attributes of $this->entity_name XML Element.");
        $fragment->addParam(
            'element', 'SimpleXMLElement', false, null, 'SimpleXMLElement',
            'XML Element to set attributes'
        );

        $fragment->addFragment(array(
            '$nb_parent = $this->nb_data_object->getTranslatedObject();',
            'if ($nb_parent !== null) {',
            '    $nb_language = $nb_parent->getLanguage($this->nb_data_object->getLanguageId());',
            '    $element->addAttribute(\'lang\', $nb_language->grantHash(true));'
        ));

        if (is_array($this->element_attributes) && count($this->element_attributes) > 0) {
            $fragment->addFragment('    $this->putAttributesFromList($element, array(');
            $count = count($this->element_attributes);
            foreach ($this->element_attributes as $field => $attr) {
                $fragment->addFragment("        '$field' => '$attr'" . (--$count > 0 ? ',' : ''));
            }
            $fragment->addFragment('    ), false);');
        }

        $fragment->addFragment('}');

        $this->getDocument()->addUse('\SimpleXMLElement');

        $this->addFragment($fragment);
    }

    /**
     * Prepares the getChilds method.
     */
    private function prepareGetChilds()
    {
        $fragment = new CNabuPHPMethodBuilder($this, 'getChilds', CNabuPHPMethodBuilder::METHOD_PROTECTED);
        $fragment->addComment("Get default childs of $this->entity_name from XML Element as Element > CDATA structure.");
        $fragment->addParam(
            'element', 'SimpleXMLElement', false, null, 'SimpleXMLElement',
            'XML Element to get childs'
        );

        if ($this->is_translated) {
            $fragment->addFragment(array(
                'parent::getChilds($element);'
            ));
        }

        if (is_array($this->element_childs) && count($this->element_childs) > 0) {
            if ($this->is_translated) {
                $fragment->addFragment('');
            }
            $fragment->addFragment('$this->getChildsAsCDATAFromList($element, array(');
            $count = count($this->element_childs);
            foreach ($this->element_childs as $field => $child) {
                $fragment->addFragment("    '$field' => '$child'" . (--$count > 0 ? ',' : ''));
            }
            $fragment->addFragment('), false);');
        }

        $this->getDocument()->addUse('\SimpleXMLElement');

        $this->addFragment($fragment);
    }

    /**
     * Prepares the setChilds method.
     */
    private function prepareSetChilds()
    {
        $fragment = new CNabuPHPMethodBuilder($this, 'setChilds', CNabuPHPMethodBuilder::METHOD_PROTECTED);
        $fragment->addComment("Set default childs of $this->entity_name XML Element as Element > CDATA structure.");
        $fragment->addParam(
            'element', 'SimpleXMLElement', false, null, 'SimpleXMLElement',
            'XML Element to set childs'
        );

        if ($this->is_translated) {
            $fragment->addFragment(array(
                'parent::setChilds($element);'
            ));
        }

        if (is_array($this->element_childs) && count($this->element_childs) > 0) {
            if ($this->is_translated) {
                $fragment->addFragment('');
            }
            $fragment->addFragment('$this->putChildsAsCDATAFromList($element, array(');
            $count = count($this->element_childs);
            foreach ($this->element_childs as $field => $child) {
                $fragment->addFragment("    '$field' => '$child'" . (--$count > 0 ? ',' : ''));
            }
            $fragment->addFragment('), false);');
        }

        $this->getDocument()->addUse('\SimpleXMLElement');

        $this->addFragment($fragment);
    }
}
