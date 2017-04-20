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

namespace nabu\sdk\readers\zip;
use ZipArchive;
use nabu\core\exceptions\ENabuCoreException;
use nabu\sdk\readers\CNabuAbstractReader;
use nabu\sdk\readers\interfaces\INabuReaderWalker;

/**
 * This class read a zip file and get each file inside him.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.8 Surface
 * @version 3.0.8 Surface
 */
class CNabuZipReader extends CNabuAbstractReader
{
    /** @var ZipArchive $zip ZIP class to manage opened files. */
    private $zip = null;

    public function importFromFile(string $filename, INabuReaderWalker $walker = null): int
    {
        $numFiles = 0;
        $this->zip = new ZipArchive();

        if ($this->zip->open($filename)) {
            switch ($walker->getWalkerMode()) {
                case INabuReaderWalker::MODE_SEQUENTIAL:
                    $numFiles = $this->processSequential($walker);
                    break;
                case INabuReaderWalker::MODE_DIRECT;
                    $numFiles = $walker->processSource($this);
                    break;
                default:
                    echo "Error in Walker Mode [" . $reader->getWalkerMode() . "]\n";
            }
            $this->zip->close();
            $this->zip = null;
        } else {
            echo "Error opening file $filename\n";
        }

        return $numFiles;
    }

    /**
     * Process sequentially all files in the ZIP.
     * @param INabuReaderWalker $walker Sequential Walker to be used in each file in the ZIP.
     * @return int Returns the number of files processed.
     */
    private function processSequential(INabuReaderWalker $walker) : int
    {
        for ($i = 0; $i < $this->zip->numFiles; $i++) {
            $walker->processFragment($this->zip->getFromIndex($i));
        }

        return $this->zip->numFiles;
    }

    public function seekFragment($pointer)
    {
        if (is_numeric($pointer) && $pointer >= 0 && $pointer < $this->zip->numFiles) {
            return $this->zip->getFromIndex($pointer);
        } elseif (is_string($pointer) && strlen($pointer) > 0) {
            return $this->zip->getFromName($pointer);
        } else {
            throw new ENabuCoreException(
                ENabuCoreException::ERROR_UNEXPECTED_PARAM_VALUE,
                array('$pointer', var_export($pointer, true))
            );
        }
    }
}
