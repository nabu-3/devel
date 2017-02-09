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
use \nabu\sdk\builders\php\CNabuPHPFragmentBuilder;
use \nabu\sdk\builders\php\CNabuPHPMethodBuilder;

/**
 * Class to create class database instances.
 * @author Rafael Gutierrez <rgutierrez@wiscot.com>
 * @version 3.0.0 Surface
 * @package \nabu\sdk\builders\php
 */
class CNabuPHPClassBuilder extends CNabuPHPFragmentBuilder
{
    /**
     * Name of the class
     * @var string
     */
    protected $name;
    /**
     * Class extended
     * @var string
     */
    protected $extends;
    /**
     * Array with all implemented interfaces
     * @var array
     */
    protected $interface_list = array();
    /**
     * If true the class is abstract
     * @var boolean
     */
    protected $abstract = false;
    /**
     * Array with all traits used to define the class
     * @var array
     */
    protected $use_list = array();
    /**
     * Array with all methods defined in the class
     * @var array
     */
    protected $method_list = array();

    /**
     * Creates an instance of this class.
     * @param CNabuAbstractBuilder $container Container builder object
     * @param string $name Name of the class
     * @param boolean $abstract Defines if the class is abstract or not
     */
    public function __construct(CNabuAbstractBuilder $container, $name, $abstract = false)
    {
        parent::__construct($container);

        $this->name = $name;
        $this->abstract = $abstract;
    }

    /**
     * Gets the class name represented by this instance
     * @return string Returns the name of the class represented by this instance
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Overrides the parent method to return the class starting declaration.
     * The string built is of type "class <name> extends <parent class> implements <list of interfaces> {"
     * @param type $padding Padding to place at the start of all lines
     * @return string Returns a string with the starting declaration of class
     */
    protected function getHeader($padding = '')
    {
        $output = $padding . ($this->abstract ? 'abstract ' : '') . "class $this->name"
                . (strlen($this->extends) ? " extends $this->extends" : '')
                . (count($this->interface_list) > 0 ? " implements " . implode(', ', $this->interface_list) : '')
                . "\n"
                . $padding . "{\n";

        if (count($this->use_list) > 0) {
            sort($this->use_list, SORT_STRING | SORT_FLAG_CASE);
            foreach ($this->use_list as $item) {
                $output .= $padding . "    use $item;\n";
            }
            $output .= "\n";
        }

        return $output;
    }

    /**
     * Overrides the parent method to remove leading spaces when not necessary.
     * @param type $padding Padding to place at the start of all lines
     * @return string Returns a string with the content of class
     */
    protected function getContent($padding = '')
    {
        return preg_replace('/\\s+$/', "\n", parent::getContent($padding));
    }

    /**
     * Overrides the parent method to rethrn the class ending declaration.
     * The string built is the close key and a carriage return.
     * @param type $padding Padding to place at the start of all lines
     * @return string Returns the close key of class declaration
     */
    protected function getFooter($padding = '')
    {
        return $padding . "}";
    }

    /**
     * Gets the parent (extended) class
     * @return string Returns the extended class if defined of null if not
     */
    public function getExtends()
    {
        return $this->extends;
    }

    /**
     * Set the name (and path if required) of the extended class
     * @param string $extends Name of extended class
     */
    public function setExtends($extends)
    {
        $this->extends = $extends;
    }

    /**
     * Gets the list of interfaces implemented.
     * @return array Returns an array with the list of implemented interfaces
     * or an empty array if no interfaces are defined
     */
    public function getInterfaces()
    {
        return $this->interface_list;
    }

    /**
     * Add an interface to the class declaration. If interface already exists then the action is ignored
     * @param string $interface Name of interface to be added
     * @return boolean Return true if $interface is added of false if it already exists
     */
    public function addInterface($interface)
    {
        $retval = false;

        if (!array_search($interface, $this->interface_list)) {
            $this->interface_list[] = $interface;
            $retval = true;
        }

        return $retval;
    }

    /**
     * Gets the list of traits used.
     * @return array Returns an array with the list of used traits
     * or an empty array if no traits are defined
     */
    public function getUses()
    {
        return $this->use_list;
    }

    /**
     * Add a trait to the class declaration. If trait already exists then the action is ignored
     * @param type $use Name of trait to be added
     * @return boolean Return true if $use is added or false if it already exists
     */
    public function addUse($use)
    {
        $retval = false;

        if (!array_search($use, $this->interface_list)) {
            $this->use_list[] = $use;
            $retval = true;
        }

        return $retval;
    }

    /**
     * Gets the list of all implemented methods. Each method is of class CNabuPHPMethodBuilder or descendant.
     * @return array Returns the collection of methods implemented in this class.
     */
    public function getMethods()
    {
        return $this->method_list;
    }

    /**
     * Add a method to the class
     * @param \nabu\core\builders\php\CNabuPHPMethodBuilder $method Method instance to be added
     * @return boolean Returns true if the method is added or false if already exists
     */
    public function addMethod(CNabuPHPMethodBuilder $method)
    {
        $retval = false;
        $name = $method->getName();

        if (!array_key_exists($name, $this->method_list)) {
            $this->method_list[] = $method;
            $retval = true;
        }

        return $retval;
    }
}
