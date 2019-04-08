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

namespace nabu\sdk\readers\interfaces;

/**
 * Interface to define Nabu readers to read files automatically.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.8 Surface
 * @version 3.0.8 Surface
 */
interface INabuReader
{
    /**
     * Import the file and parse contents.
     * @param string $filename File name to import.
     * @param INabuReaderWalker $walker Walker to process file contents.
     * @return bool Returns true if the file is well imported.
     */
    public function importFromFile(string $filename, INabuReaderWalker $walker = null) : int;

    /**
     * Seek a fragment of file by their pointer.
     * @param mixed $pointer Pointer to the fragment. The type of pointer varies in each implementation.
     * @return mixed Returns the fragment seeked if exists of false if no.
     */
    public function seekFragment($pointer);
}
