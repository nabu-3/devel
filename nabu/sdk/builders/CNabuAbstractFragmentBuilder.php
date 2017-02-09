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

namespace nabu\sdk\builders;

use \nabu\core\exceptions\ENabuCoreException;

/**
 * Abstract class to implement fragments of code.
 * @author Rafael Gutierrez <rgutierrez@wiscot.com>
 * @version 3.0.0 Surface
 */
abstract class CNabuAbstractFragmentBuilder extends CNabuAbstractBuilder
{
    /**
     * Overrides default constructor to gain the $container that owns this instance
     * @param CNabuAbstractBuilder $container
     * @throws ENabuCoreException
     */
    public function __construct(CNabuAbstractBuilder $container)
    {
        parent::__construct($container);
    }

    /**
     * Returns and empty string to invalidate the getDescriptor method
     * inherited from the base class.
     * @return string Return the descriptor string
     */
    protected function getDescriptor()
    {
        return '';
    }

    /**
     * Returns and empty string to invalidate the getLicense method inherited
     * from the base class.
     * @param string $padding Sequence of characters to place before each line
     * of the license.
     * @return string Return the license string.
     */
    protected function getLicense($padding = '')
    {
        return '';
    }
}
