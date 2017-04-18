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
use nabu\sdk\builders\CNabuAbstractBuilder;
use nabu\sdk\builders\zip\base\CNabuZipFragment;

/**
 * Stream descriptor for ZIP builder
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.8 Surface
 * @version 3.0.8 Surface
 */
class CNabuZipStream extends CNabuZipFragment
{
    /** @var CNabuAbstractBuilder $stream Nabu Builder treated as a stream. */
    private $stream = null;

    /**
     * @param CNabuAbstractBuilder $container Container builder object.
     * @param string $path Path inside Zip file of this file.
     * @param string $internal_name File name inside Zip file.
     * @param CNabuAbstractBuilder $stream Stream to create a file inside the Zip with.
     */
    public function __construct(
        CNabuAbstractBuilder $container,
        string $path,
        string $internal_name,
        CNabuAbstractBuilder $stream
    ) {
        parent::__construct($container, $path, $internal_name);

        $this->stream = $stream;
    }

    /**
     * Gets the stream.
     * @return CNabuAbstractBuilder Returns the stream object.
     */
    public function getStream(): CNabuAbstractBuilder
    {
        return $this->stream;
    }
}
