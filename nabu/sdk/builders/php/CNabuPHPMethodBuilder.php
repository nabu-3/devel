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

/**
 * @author Rafael Gutierrez <rgutierrez@wiscot.com>
 * @version 3.0.0 Surface
 */
class CNabuPHPMethodBuilder extends CNabuPHPFunctionBuilder
{
    const FUNCTION_PRIVATE = 'private';
    const FUNCTION_PROTECTED = 'protected';
    const FUNCTION_PUBLIC = 'public';
    
    private $scope = false;
    private $abstract = false;
    private $static = false;
    
    /**
     * Constructor
     * @param CNabuAbstractBuilder $container Container builder object
     * @param string $name Name of the method.
     * @param string $scope Scope of the method as defined in constants
     * @param type $static If true the method is static.
     * @param boolean $abstract If true the method is abstract.
     */
    public function __construct(
        $container,
        $name,
        $scope = CNabuPHPMethodBuilder::FUNCTION_PUBLIC,
        $static = false,
        $abstract = false
    ) {
        parent::__construct($container, $name);
        
        $this->scope = $scope;
        $this->abstract = $abstract;
        $this->static = $static;
    }
    
    protected function getPrefix()
    {
        return
                  ($this->scope !== false ? $this->scope . ' ' : '')
                . ($this->static !== false ? 'static ' : '')
                . ($this->abstract !== false ? 'abstract ' : '')
        ;
    }
}
