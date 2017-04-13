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

namespace nabu\sdk\builders\xml;
use SimpleXMLElement;
use \nabu\sdk\builders\CNabuAbstractBuilder;
use nabu\xml\CNabuXMLObject;

/**
 * Main builder for text files
 * @author Rafael Gutierrez <rgutierrez@wiscot.com>
 * @since 3.0.12 Surface
 * @version 3.0.12 Surface
 */
class CNabuXMLBuilder extends CNabuAbstractBuilder
{
    /**
     * Create a new instance. If $text is passed the instance acquires the text as content.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Overrides parent method to return an empty string
     * @param string $padding This parameter is ignored in this implementation
     * @return string Returns an empty ('') string
     */
    protected function getComments($padding = '')
    {
        return '';/* "<!-- Comment to test XML Builder -->\n"; */
    }

    /**
     * Overrides parent method to return an empty string
     * @return string Returns an empty ('') string
     */
    protected function getDescriptor()
    {
        return '';/* "<?xml version=\"1.0\"?>\n"; */
    }

    /**
     * Overrides parent method to return an empty string
     * @param string $padding This parameter is ignored in this implementation
     * @return string Returns an empty ('') string
     */
    protected function getFooter($padding = '')
    {
        return '';
    }

    /**
     * Overrides parent method to return an empty string
     * @param string $padding This parameter is ignored in this implementation
     * @return string Returns an empty ('') string
     */
    protected function getHeader($padding = '')
    {
        return '';
    }

    /**
     * Overrides parent method to return an empty string
     * @param string $padding This parameter is ignored in this implementation
     * @return string Returns an empty ('') string
     */
    protected function getLicense($padding = '')
    {
        return '';
    }

    protected function getContent($padding = '')
    {
        $value = '';

        if ($this->getDocument() === $this &&
            count($this->fragments) === 1 &&
            $this->fragments[0] instanceof CNabuXMLObject
        ) {
            $root = new SimpleXMLElement('<nabuPackage/>', LIBXML_PARSEHUGE);
            $this->fragments[0]->build($root);
            $value = $root->asXML();
        }

        return $value;
    }
}
