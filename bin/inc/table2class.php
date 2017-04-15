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

/**
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @version 3.0.0 Surface
 */

use nabu\core\CNabuEngine;

require_once 'common.php';
require_once 'cli.php';
require_once 'db2class.php';

$nb_engine = CNabuEngine::getEngine();
$mysql_connector = $nb_engine->getMainDB();

if (!$mysql_connector->isConnected()) {
    die ("Database not found or not connected\n");
}

$creator_name = nbCLICheckOption('cn', 'creator-name', ':', null, false);
if ($creator_name === null) {
    $creator_name = nbCLIInput('Creator name', false);
}

$creator_email = nbCLICheckOption('cm', 'creator-email', ':', null, false);
if ($creator_email === null) {
    $creator_email = nbCLIInput('Creator email', false);
}

$table_schema = nbCLICheckOption('s', 'schema', ':', null, false);
if ($table_schema === null) {
    $table_schema = nbCLIInput('Schema', false);
}

$table_name = nbCLICheckOption('t', 'table', ':', null, false);
if ($table_name === null) {
    $table_name = nbCLIInput('Table name', false);
}

$class_ns = nbCLICheckOption('ns', 'namespace', ':', null, false);
if ($class_ns === null) {
    $class_ns = nbCLIInput('Namespace', true);
}

$entity_name = nbCLICheckOption('en', 'entity-name', ':', null, false);
if ($entity_name === null) {
    $entity_name = nbCLIInput('Entity name', tableNameToEntity($table_name));
}

$class_name = nbCLICheckOption('c', 'class', ':', null, false);
if ($class_name === null) {
    $class_name = nbCLIInput('Class name', tableNameToClass($table_name));
}

$class_path = nbCLICheckOption('t', 'path', ':', null, false);
if ($class_path === null) {
    $class_path = nbCLIInput('Base path: ', false);
}

$class_filename = createFilename($class_path, $class_ns, $class_name);

echo "\nWe go to create a new class file from a table descriptor.\n";
echo "Before start we go to validate all fields.\n";
echo "\nYour credentials:\n";
echo "\n    $creator_name <$creator_email>\n";
echo "\nYou want to create the class:\n";
echo "\n    $class_ns\\$class_name\n";
echo "\nStored in:\n";
echo "\n    $class_filename.php\n";
echo "\nFrom table:\n";
echo "\n    " . $table_schema . '.' . $table_name . "\n\n";

nbCLICheckContinue();

createClassFile($table_schema, $table_name, $class_ns, $class_name, $entity_name, $class_filename);
