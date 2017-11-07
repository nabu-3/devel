<?php

/*  Copyright 2009-2011 Rafael Gutierrez Martinez
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
use \nabu\sdk\builders\php\CNabuPHPMethodBuilder;

/**
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @version 3.0.0 Surface
 */
class CNabuPHPConstructorBuilder extends CNabuPHPMethodBuilder
{
    /**
     * Constructs the instance.
     * @param CNabuAbstractBuilder $container Container builder object
     * @param int $scope Scope of the constructor. By default public.
     * @param boolean $static If true the method is static.
     * @param boolean $abstract If true the method is abstract.
     */
    public function __construct(
        $container,
        $scope = CNabuPHPMethodBuilder::METHOD_PUBLIC,
        $static = false,
        $abstract = false
    ) {
        parent::__construct($container, '__construct', $scope, $static, $abstract);
    }
}
