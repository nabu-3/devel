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

namespace nabu\sdk\builders\zip;
use ZipArchive;
use nabu\core\exceptions\ENabuCoreException;
use nabu\sdk\builders\CNabuAbstractBuilder;

/**
 * Main builder for ZIP files
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.8 Surface
 * @version 3.0.8 Surface
 */
class CNabuZipBuilder extends CNabuAbstractBuilder
{
    protected function getDescriptor(): string
    {
        throw new ENabuCoreException(ENabuCoreException::ERROR_FEATURE_NOT_IMPLEMENTED);
    }

    protected function getLicense(string $padding = ''): string
    {
        throw new ENabuCoreException(ENabuCoreException::ERROR_FEATURE_NOT_IMPLEMENTED);
    }

    protected function getComments(string $padding = ''): string
    {
        throw new ENabuCoreException(ENabuCoreException::ERROR_FEATURE_NOT_IMPLEMENTED);
    }

    protected function getHeader(string $padding = ''): string
    {
        throw new ENabuCoreException(ENabuCoreException::ERROR_FEATURE_NOT_IMPLEMENTED);
    }

    protected function getFooter(string $padding = ''): string
    {
        throw new ENabuCoreException(ENabuCoreException::ERROR_FEATURE_NOT_IMPLEMENTED);
    }

    public function create(string $padding = ''): string
    {
        throw new ENabuCoreException(ENabuCoreException::ERROR_FEATURE_NOT_IMPLEMENTED);
    }

    public function exportToFile(string $filename)
    {
        $zip = new ZipArchive();

        if ($zip->open($filename, ZipArchive::CREATE)) {
            if (count($this->fragments) > 0) {
                foreach ($this->fragments as $file) {
                    $path = $file->getPath();
                    $internal = $file->getInternalName();
                    $partname = $path . (strlen($path) > 0 ? DIRECTORY_SEPARATOR : '') . $internal;
                    $zip->addEmptyDir($path);
                    if ($file instanceof CNabuZipFile) {
                        if (is_string($external = $file->getExternalName()) &&
                            is_file($external) &&
                            file_exists($external)
                        ) {
                            $zip->addFile($source, $partname);
                        }
                    } elseif ($file instanceof CNabuZipStream) {
                        $stream = $file->getStream();
                        $zip->addFromString($partname, $stream->getCode());
                    }
                }
            }
            $zip->close();
        }
    }
}
