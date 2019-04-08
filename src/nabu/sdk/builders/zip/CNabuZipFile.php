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

namespace nabu\sdk\builders\zip;
use nabu\sdk\builders\CNabuAbstractBuilder;
use nabu\sdk\builders\zip\base\CNabuZipFragment;

/**
 * File descriptor for ZIP builder
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.8 Surface
 * @version 3.0.8 Surface
 */
class CNabuZipFile extends CNabuZipFragment
{
    /** @var string $external_name Name of original file. */
    private $external_name = null;

    /**
     * @param CNabuAbstractBuilder $container Container builder object.
     * @param string $path Path inside Zip file of this file.
     * @param string $internal_name File name inside Zip file.
     * @param string $external_name Name of file outside the Zip with her original path included.
     */
    public function __construct(
        CNabuAbstractBuilder $container,
        string $path,
        string $internal_name,
        string $external_name
    ) {
        parent::__construct($container, $path, $internal_name);

        $this->external_name = $external_name;
    }

    /**
     * Gets the external file name with her full path.
     * @return string Returns the external file name.
     */
    public function getExternalName(): string
    {
        return $this->external_name;
    }
}
