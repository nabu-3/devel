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

namespace nabu\sdk\builders;

use \nabu\sdk\builders\CNabuAbstractFragmentBuilder;
use \nabu\sdk\builders\interfaces\INabuBuilder;
use \nabu\core\CNabuObject;

/**
 * This is the base abstract class to create code builders.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @version 3.0.0 Surface
 */
abstract class CNabuAbstractBuilder extends CNabuObject implements INabuBuilder
{
    /**
     * Container instance that owns this instance.
     * @var CNabuAbstractBuilder
     */
    private $container = null;
    
    /**
     * Entire code generated
     * @var string
     */
    protected $code;
    /**
     * Array list of lines of comments.
     * @var array
     */
    protected $comments = array();
    /**
     * Array of fragments to create the content section.
     * @var array
     */
    protected $fragments = array();

    /**
     * Abstract method to create the descriptor in the first line of a file
     * @return string Return the descriptor string
     */
    abstract protected function getDescriptor();
    /**
     * Abstract method to create the license text.
     * @param string $padding Sequence of characters to place before each line
     * of the license.
     * @return string Return the license string.
     */
    abstract protected function getLicense($padding = '');
    /**
     * Create the comments section.
     * @param string $padding Sequence of characters to place before each line
     * of the comments.
     * @return string Return the comments string.
     */
    abstract protected function getComments($padding = '');
    /**
     * Abstract method to create the header section.
     * @param string $padding Sequence of characters to place before each line
     * of the header.
     * @return string Return the header string.
     */
    abstract protected function getHeader($padding = '');
    /**
     * Abstract method to create the footer section.
     * @param string $padding Sequence of characters to place before each line
     * of the footer.
     * @return string Return the footer string.
     */
    abstract protected function getFooter($padding = '');

    public function __construct(CNabuAbstractBuilder $container = null)
    {
        parent::__construct();

        $this->container = $container;
    }

    /**
     * Gets the Container instance.
     * @return CNabuAbstractBuilder Returns the assigned Container if any or null elsewhere.
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Gets the Document instance. This instance is the root instance in containers tree.
     * @return CNabuAbstractBuilder Returns the Document.
     */
    public function getDocument()
    {
        return ($this->container === null ? $this : $this->container->getDocument());
    }

    /**
     * Create the content section concatenating each fragment.
     * @param string $padding Sequence of characters to place before each line
     * of the content.
     * @return string Return the content string.
     */
    protected function getContent($padding = '')
    {
        $content = '';

        if (count($this->fragments) > 0) {
            foreach ($this->fragments as $fragment) {
                if (is_string($fragment)) {
                    $content .= $padding . $fragment . "\n";
                } elseif ($fragment instanceof CNabuAbstractFragmentBuilder) {
                    $content .= $fragment->create($padding) . "\n";
                }
            }
        }

        return $content;
    }

    /**
     * Add a fragment or an array with a set of fragments.
     * Each fragment can be of type string or an instance of class CNabuPHPFragmentBuilder or a descendant of him.
     * @param mixed $fragment Fragments to be added
     */
    public function addFragment($fragment)
    {
        if (is_array($fragment)) {
            $this->fragments = array_merge($this->fragments, $fragment);
        } else {
            $this->fragments[] = $fragment;
        }
    }

    /**
     * Add a comment line to the instance.
     * @param string $comment
     * @return bool Returns true always.
     */
    public function addComment($comment)
    {
        if (is_string($comment)) {
            $this->comments[] = $comment;
        }

        return true;
    }

    /**
     * Create the code represented by this instance.
     * @param string $padding Sequence of characters to place before each line
     * of code.
     * @return string Return the code as string.
     */
    public function create($padding = '')
    {
        $this->code = $this->getDescriptor($padding)
                    . $this->getLicense($padding)
                    . $this->getComments($padding)
                    . $this->getHeader($padding)
                    . $this->getContent($padding)
                    . $this->getFooter($padding)
        ;

        return $this->code;
    }

    /**
     * Return the code represented by this instance.
     * If CNabuAbstractBuilder::create is not called first, then return
     * an empty string.
     * @return string Return the code represented by this instance.
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Exports the code to a file.
     * @param string $filename File name and path to create export file.
     */
    public function exportToFile($filename)
    {
        if (($p = strrpos($filename, DIRECTORY_SEPARATOR)) >= 0) {
            $path = substr($filename, 0, $p);
            $name = substr($filename, $p + 1);

            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }

        if (($handler = fopen($filename, 'w'))) {
            fwrite($handler, $this->code);
            fclose($handler);
        }
    }
}
