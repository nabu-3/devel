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

namespace nabu\sdk\builders\text;

use \nabu\sdk\builders\CNabuAbstractBuilder;

/**
 * Main builder for text files
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.0 Surface
 * @version 3.0.8 Surface
 */
class CNabuTextBuilder extends CNabuAbstractBuilder
{
    /**
     * Create a new instance. If $text is passed the instance acquires the text as content.
     * @param array $text
     */
    public function __construct($text = null)
    {
        parent::__construct();

        if (strlen($text) > 0) {
            $this->addFragment($text);
        }
    }

    /**
     * Overrides parent method to return an empty string
     * @param string $padding This parameter is ignored in this implementation
     * @return string Returns an empty ('') string
     */
    protected function getComments(string $padding = '') : string
    {
        return '';
    }

    /**
     * Overrides parent method to return an empty string
     * @return string Returns an empty ('') string
     */
    protected function getDescriptor() : string
    {
        return '';
    }

    /**
     * Overrides parent method to return an empty string
     * @param string $padding This parameter is ignored in this implementation
     * @return string Returns an empty ('') string
     */
    protected function getFooter(string $padding = '') : string
    {
        return '';
    }

    /**
     * Overrides parent method to return an empty string
     * @param string $padding This parameter is ignored in this implementation
     * @return string Returns an empty ('') string
     */
    protected function getHeader(string $padding = '') : string
    {
        return '';
    }

    /**
     * Overrides parent method to return an empty string
     * @param string $padding This parameter is ignored in this implementation
     * @return string Returns an empty ('') string
     */
    protected function getLicense(string $padding = '') : string
    {
        return '';
    }
}
