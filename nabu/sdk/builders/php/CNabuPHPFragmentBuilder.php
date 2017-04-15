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

namespace nabu\sdk\builders\php;

use \nabu\sdk\builders\CNabuAbstractFragmentBuilder;
use \nabu\sdk\builders\php\traits\TNabuPHPCommentsBuilder;

/**
 * Abstract base class to create PHP fragment classes
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @version 3.0.0 Surface
 */
abstract class CNabuPHPFragmentBuilder extends CNabuAbstractFragmentBuilder
{
    use TNabuPHPCommentsBuilder;

    protected function getContent($padding = '')
    {
        return parent::getContent($padding . '    ');
    }

    /**
     * Converts a value to string representation.
     * @param mixed $value Value to be converted.
     * @return string Returns a string representing the value converted.
     */
    protected function valueToString($value)
    {
        if ($value === null) {
            $def_value = 'null';
        } elseif ($value === false) {
            $def_value = 'false';
        } elseif ($value === true) {
            $def_value = 'true';
        } elseif (is_string($value)) {
            $def_value = '"' . str_replace('"', '\\"', $value) . '"';
        } elseif (is_numeric($value)) {
            $def_value = '' . $value;
        }

        return $def_value;
    }


}
