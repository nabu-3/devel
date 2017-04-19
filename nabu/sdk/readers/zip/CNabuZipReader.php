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

namespace nabu\sdk\readers;
use ZipArchive;
use nabu\sdk\CNabuAbstractReader;

/**
 * This class read a zip file and get each file inside him.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.8 Surface
 * @version 3.0.8 Surface
 */
class CNabuZipReader extends CNabuAbstractReader
{
    public function importFromFile(string $filename): bool
    {
        $zip = new ZipArchive();

        if ($zip->open($filename)) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                echo print_r($zip->statIndex($i), true);
            }
            $zip->close();
        }
    }
}
