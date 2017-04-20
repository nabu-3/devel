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

namespace nabu\sdk\readers\interfaces;
use nabu\sdk\readers\CNabuAbstractReader;

/**
 * Interface to define Nabu Reader Fragments to read files automatically.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.8 Surface
 * @version 3.0.8 Surface
 */
interface INabuReaderWalker
{
    /** @var int MODE_SEQUENTIAL The Walker is sequential and processes fragments in order. */
    const MODE_SEQUENTIAL   = 0x0001;

    /** @var int MODE_DIRECT The Walker requests files to the Reader. */
    const MODE_DIRECT       = 0x0002;

    /**
     * Gets the Walker mode (sequential or direct). Allowed values are INabuReaderWalker::MODE_SEQUENTIAL and
     * INabuReaderWalker::MODE_DIRECT.
     * @return int Returns the Mode of the walker.
     */
    public function getWalkerMode() : int;

    /**
     * Process the source in a Reader if Walker Mode is direct.
     * @param CNabuAbstractReader $reader Reader to seek files.
     * @return int Returns the number of files processed.
     */
    public function processSource(CNabuAbstractReader $reader) : int;
}
