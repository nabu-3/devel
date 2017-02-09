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

use \nabu\sdk\builders\json\CNabuJSONBuilder;
use \nabu\sdk\builders\php\CNabuPHPBuilder;
use \nabu\sdk\builders\nabu\CNabuPHPClassTableBuilder;
use nabu\sdk\builders\nabu\CNabuPHPClassListBuilder;
use nabu\core\exceptions\ENabuException;

/**
 * @author Rafael Gutierrez <rgutierrez@wiscot.com>
 * @version 3.0.0 Surface
 */

function tableNameToEntity($table_name)
{
    $parts = explode('_', $table_name);
    $entity_name = '';

    foreach ($parts as $part) {
        if ($part === 'nb' && strlen($entity_name) === 0) {
            //$class_name = 'CNabu';
        } elseif (strlen($part) > 0) {
            $entity_name .= strtoupper(substr($part, 0, 1)) . (strlen($part) > 1 ? strtolower(substr($part, 1)) : '');
        }
    }

    return $entity_name;
}

function tableNameToClass($table_name)
{
    $parts = explode('_', $table_name);
    $class_name = '';

    foreach ($parts as $part) {
        if ($part === 'nb' && strlen($class_name) === 0) {
            $class_name = 'CNabu';
        } elseif (strlen($part) > 0) {
            $class_name .= strtoupper(substr($part, 0, 1)) . (strlen($part) > 1 ? strtolower(substr($part, 1)) : '');
        }
    }

    if (!nb_strStartsWith($class_name, 'C')) {
        $class_name = 'C' . $class_name;
    }

    return $class_name;
}

function createFilename($path, $ns, $name)
{
    return $path . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $ns) . DIRECTORY_SEPARATOR . $name;
}

/**
 *
 * @param string $table_schema
 * @param string $table_name
 * @param string $class_ns
 * @param string $class_name
 * @param string $entity_name
 * @param string $class_filename
 * @param bool $abstract
 * @param array $dictionary
 * @return CNabuPHPClassTableBuilder
 */
function createTableClassFile(
    $table_schema,
    $table_name,
    $class_ns,
    $class_name,
    $entity_name,
    $class_filename,
    $abstract = false,
    $dictionary = false
) {
    global $mysql_connector, $creator_name, $creator_email;

    echo "    ... Creating class $class_ns\\$class_name ...";

    $file_builder = new CNabuPHPBuilder($class_ns);

    $class_builder = new CNabuPHPClassTableBuilder(
        $file_builder, $mysql_connector, $class_ns, $class_name,
        $entity_name, $table_name, $table_schema, $abstract
    );

    if ($dictionary !== false && count($dictionary) > 0) {
        $class_builder->setDictionary($dictionary);
    }

    //$class_builder->dumpStatus('            ');
    try {
        $class_builder->prepare($creator_name, $creator_email);
        $file_builder->addFragment($class_builder);
        $file_builder->create();
        $file_builder->exportToFile($class_filename . '.php');

        $class_builder->getStorageDescriptor()->exportToFile($class_filename . '.json');
        echo "        OK\n";
    } catch (ENabuException $ex) {
        echo '        ERROR ' . $ex->getMessage() . "\n";
    }

    return $class_builder;
}

function createListClassFile(
    $table_descriptor,
    $class_ns,
    $class_name,
    $entity_name,
    $item_name,
    $item_entity_name,
    $class_filename,
    $abstract = false,
    $dictionary = false
) {
    global $creator_name, $creator_email;

    echo "    ... Creating class $class_ns\\$class_name ...";

    $file_builder = new CNabuPHPBuilder($class_ns);

    $class_builder = new CNabuPHPClassListBuilder(
        $file_builder, $table_descriptor, $class_ns, $class_name,
        $entity_name, $item_name, $item_entity_name, $abstract
    );

    try {
        $class_builder->prepare($creator_name, $creator_email);
        $file_builder->addFragment($class_builder);
        $file_builder->create();
        $file_builder->exportToFile($class_filename . '.php');
        echo "OK\n";
    } catch (ENabuException $ex) {
        echo 'ERROR ' . $ex->getMessage() . "\n";
    }

    return $class_builder;
}

function createEntity($table_name, $class_ns, $class_name, $entity_name, $abstract = false, $dictionary = false)
{
    global $table_schema, $class_path;

    if (nb_strEndsWith($class_name, 'Base')) {
        $class_name = substr($class_name, 0, strlen($class_name) - 4);
        $class_suffix = 'Base';
    } else {
        $class_suffix = '';
    }

    $class_filename = createFilename($class_path, $class_ns, $class_name . $class_suffix);
    $table_class = createTableClassFile(
        $table_schema, $table_name, $class_ns, $class_name . $class_suffix,
        $entity_name, $class_filename, $abstract, $dictionary
    );

    $class_filename = createFilename($class_path, $class_ns, $class_name . 'List' . $class_suffix);
    createListClassFile(
        $table_class->getStorageDescriptor(),
        $class_ns, $class_name . 'List' . $class_suffix,
        $entity_name . ' List', $class_name, $entity_name,
        $class_filename, $abstract, $dictionary
    );

    if ($table_class->isTranslatedTable()) {
        $class_filename = createFilename($class_path, $class_ns, $class_name . 'Language' . $class_suffix);
        $table_class = createTableClassFile(
            $table_schema, $table_name . '_lang', $class_ns, $class_name . 'Language' . $class_suffix,
            $entity_name . ' Language', $class_filename, $abstract, $dictionary
        );

        $class_filename = createFilename($class_path, $class_ns, $class_name . 'LanguageList' . $class_suffix);
        createListClassFile(
            $table_class->getStorageDescriptor(),
            $class_ns, $class_name . 'LanguageList' . $class_suffix,
            $entity_name . ' Language List', $class_name, $entity_name . ' Language',
            $class_filename, $abstract, $dictionary
        );
    }
}
