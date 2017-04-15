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

namespace nabu\sdk\builders\json;

use \nabu\sdk\builders\CNabuAbstractBuilder;

/**
 * Main builder for JSON files
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @version 3.0.0 Surface
 */
class CNabuJSONBuilder extends CNabuAbstractBuilder
{
    /**
     * Associative array with a multilevel structure representing JSON document
     * @var array
     */
    private $json = null;
    
    /**
     * Create a new instance. If $json is passed the instance represents JSON document contained in $json.
     * @param array $json
     */
    public function __construct($json = null)
    {
        parent::__construct();
        
        $this->json = $json;
    }

    /**
     * Overrides parent method to return JSON encoded as string
     * @param string $padding This parameter is ignored in this implementation
     * @return string Returns a string representing the encode JSON or false if error
     */
    protected function getContent($padding = '')
    {
        return json_encode($this->json, JSON_PRETTY_PRINT);
    }
    
    /**
     * Overrides parent method to return an empty string
     * @param type $padding This parameter is ignored in this implementation
     * @return string Returns an empty ('') string
     */
    protected function getComments($padding = '')
    {
        return '';
    }

    /**
     * Overrides parent method to return an empty string
     * @param type $padding This parameter is ignored in this implementation
     * @return string Returns an empty ('') string
     */
    protected function getDescriptor()
    {
        return '';
    }

    /**
     * Overrides parent method to return an empty string
     * @param type $padding This parameter is ignored in this implementation
     * @return string Returns an empty ('') string
     */
    protected function getFooter($padding = '')
    {
        return '';
    }

    /**
     * Overrides parent method to return an empty string
     * @param type $padding This parameter is ignored in this implementation
     * @return string Returns an empty ('') string
     */
    protected function getHeader($padding = '')
    {
        return '';
    }

    /**
     * Overrides parent method to return an empty string
     * @param type $padding This parameter is ignored in this implementation
     * @return string Returns an empty ('') string
     */
    protected function getLicense($padding = '')
    {
        return '';
    }
}
