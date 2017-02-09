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

use \nabu\sdk\builders\CNabuAbstractBuilder;
use \nabu\sdk\builders\php\traits\TNabuPHPCommentsBuilder;

/**
 * Main builder for all PHP files
 * @author Rafael Gutierrez <rgutierrez@wiscot.com>
 * @version 3.0.0 Surface
 */
class CNabuPHPBuilder extends CNabuAbstractBuilder
{
    use TNabuPHPCommentsBuilder;
    
    /**
     * Namespace of the file. If not required can be empty.
     * @param string $ns
     */
    protected $ns;
    /**
     * Array with all used class / interfaces / traits.
     * @var array
     */
    protected $use_list = array();

    /**
     * Creates an instance of this class
     * @param string $ns Namespace for this PHP content
     */
    public function __construct($ns = false)
    {
        parent::__construct();
        
        $this->ns = $ns;
    }
    
    /**
     * Get the namespace assigned for this instance
     * @return string Returns the namespace
     */
    public function getNS()
    {
        return $this->ns;
    }
    
    /**
     * Overrides parent method to return the open paragraph of PHP
     * @return string Returns the descriptor init sequence
     */
    protected function getDescriptor()
    {
        return "<?php\n";
    }
    
    /**
     * Overrides parent method to return the file license as a PHP comment
     * @param string $padding Padding to place at the beginning of each line
     * @return string Returns the license as a PHP comment string
     */
    protected function getLicense($padding = '')
    {
        return "$padding/* ===========================================================================\n"
             . "$padding * File generated automatically by Nabu-3.\n"
             . "$padding * You can modify this file if you need to add more functionalities.\n"
             . "$padding * ---------------------------------------------------------------------------\n"
             . "$padding * Created: " . date('Y/m/d H:i:s e') . "\n"
             . "$padding * ===========================================================================\n"
             . nb_apacheLicense($padding . ' * ')
             . "$padding */\n\n";
    }
    
    /**
     * Overrides parent method to return the header of the file
     * @param type $padding Padding to place at the beginning of each line
     * @return string Returns the header as a string
     */
    protected function getHeader($padding = '')
    {
        $content = (strlen($this->ns) > 0 ? $padding . "namespace $this->ns;\n\n" : '');
        
        if (count($this->use_list) > 0) {
            sort($this->use_list, SORT_STRING | SORT_FLAG_CASE);
            foreach ($this->use_list as $used) {
                $content .= $padding . "use $used;\n";
            }
            $content .= "\n";
        }
        
        return $content;
    }
    
    /**
     * Overrides the parent method to return an empty string as footer ('')
     * @param type $padding
     * @return string Returns an empty ('') string
     */
    protected function getFooter($padding = '')
    {
        return '';
    }
    
    /**
     * Gets the list of PHP use clauses included in this entity
     * @return array Returns an array with a list of PHP use clauses
     */
    public function getUses()
    {
        return $this->use_list;
    }
    
    /**
     * Add a new use clause to this instance. If use clause already exists then the action is ignored.
     * @param type $used Object path and name to be used
     * @return boolean Returns true if $used is added or false if already exists
     */
    public function addUse($used)
    {
        $retval = false;
        if (array_search($used, $this->use_list) === false) {
            $this->use_list[] = $used;
            $retval = true;
        }
        
        return $retval;
    }
}
