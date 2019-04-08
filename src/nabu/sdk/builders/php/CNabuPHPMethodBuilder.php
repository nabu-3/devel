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

namespace nabu\sdk\builders\php;

use \nabu\sdk\builders\CNabuAbstractBuilder;

/**
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @version 3.0.0 Surface
 */
class CNabuPHPMethodBuilder extends CNabuPHPFunctionBuilder
{
    /** @var string Constant to represent private methods. */
    const METHOD_PRIVATE = 'private';
    /** @var string Constant to represent protected methods. */
    const METHOD_PROTECTED = 'protected';
    /** @var string Constant to represent public methods. */
    const METHOD_PUBLIC = 'public';

    /** @var string|bool $scope Scope of this method instance (private, protected or public). */
    private $scope = false;
    /** @var bool $abstract If true, this instance represents an abstract method. */
    private $abstract = false;
    /** @var bool $static If true, this instance represents an static method. */
    private $static = false;
    /** @var bool $final If true, this instnace represents a final method. */
    private $final = false;

    /**
     * Constructor
     * @param CNabuAbstractBuilder $container Container builder object
     * @param string $name Name of the method.
     * @param string $scope Scope of the method as defined in constants
     * @param bool $static If true the method is static.
     * @param bool $abstract If true the method is abstract.
     * @param bool $final If true, the method is a final method.
     * @param bool $have_return_type If true, a return type is allowed.
     * @param string $return_type Return type to place as return cast.
     */
    public function __construct(
        CNabuAbstractBuilder $container,
        string $name,
        string $scope = CNabuPHPMethodBuilder::METHOD_PUBLIC,
        bool $static = false,
        bool $abstract = false,
        bool $final = false,
        bool $have_return_type = false,
        string $return_type = null
    ) {
        parent::__construct($container, $name, $have_return_type, $return_type);

        $this->scope = $scope;
        $this->abstract = $abstract;
        $this->static = $static;
        $this->final = $final;
    }

    protected function getPrefix() : string
    {
        return
                  ($this->final !== false ? 'final ' : '')
                . ($this->scope !== false ? $this->scope . ' ' : '')
                . ($this->static !== false ? 'static ' : '')
                . ($this->abstract !== false ? 'abstract ' : '')
        ;
    }
}
