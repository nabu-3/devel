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

namespace nabu\sdk\builders\zip\base;
use nabu\core\exceptions\ENabuCoreException;
use nabu\sdk\builders\CNabuAbstractBuilder;
use nabu\sdk\builders\CNabuAbstractFragmentBuilder;

/**
 * Abstract class to manage fragments for ZIP builder
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.8 Surface
 * @version 3.0.8 Surface
 * @package nabu\sdk\builders\zip\base
 */
abstract class CNabuZipFragment extends CNabuAbstractFragmentBuilder
{
    /** @var string $path Path to file. */
    private $path = null;
    /** @var string $internal_name Name of file. */
    private $internal_name = null;

    /**
     * @param CNabuAbstractBuilder $container Container builder object.
     * @param string $path Path inside Zip file of this file.
     * @param string $internal_name File name inside Zip file.
     */
    public function __construct(
        CNabuAbstractBuilder $container,
        string $path,
        string $internal_name
    ) {
        parent::__construct($container);

        $this->path = $path;
        $this->internal_name = $internal_name;
    }

    /**
     * Gets the internal path of file.
     * @return string Returns the internal path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Gets the internal file name.
     * @return string Returns the internal file name.
     */
    public function getInternalName(): string
    {
        return $this->internal_name;
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
}
