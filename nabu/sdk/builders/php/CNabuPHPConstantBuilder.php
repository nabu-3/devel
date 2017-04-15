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
use nabu\sdk\builders\CNabuAbstractBuilder;
use nabu\sdk\builders\php\CNabuPHPFragmentBuilder;

/**
 * Class to create PHP constants. These constants can be included in classes or traits.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @version 3.0.0 Surface
 */
class CNabuPHPConstantBuilder extends CNabuPHPFragmentBuilder
{
    /**
     * Constant type value
     * @var string
     */
    private $type;
    /**
     * Constant name
     * @var string
     */
    private $name;
    /**
     * Value of the constant
     * @var string
     */
    private $value;

    public function __construct(CNabuAbstractBuilder $container, $name, $value, $type = false)
    {
        parent::__construct($container);

        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
    }

    protected function getHeader($padding = '')
    {
        return '';
    }

    protected function getFooter($padding = '')
    {
        return '';
    }

    public function create($padding = '')
    {
        $this->addComment('@var' . ($this->type !== false ? ' ' . $this->type : ''));
        return parent::create($padding);
    }

    protected function getContent($padding = '')
    {
        return $padding . 'const ' . $this->name . ' = ' . $this->valueToString($this->value) . ";\n";
    }
}
