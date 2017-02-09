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

namespace nabu\sdk\builders\interfaces;

/**
 * Interface to define Nabu builders to create code automatically.
 * @author Rafael Gutierrez <rgutierrez@wiscot.com>
 * @version 3.0.0 Surface
 */
interface INabuBuilder
{
    /**
     * Create the code represented by this instance.
     * @param string $padding Sequence of characters to place before each line
     * of code.
     * @return string Return the code as string.
     */
    public function create($padding = '');

    /**
     * Return the code represented by this instance.
     * If CNabuAbstractBuilder::create is not called first, then return
     * an empty string.
     * @return string Return the code represented by this instance.
     */
    public function getCode();

    /**
     * Exports generated code to a file.
     * @param string $filename File name including full path.
     */
    public function exportToFile($filename);
}
