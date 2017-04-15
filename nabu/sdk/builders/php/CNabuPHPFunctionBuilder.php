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

namespace nabu\sdk\builders\php;

use \nabu\sdk\builders\CNabuAbstractBuilder;

/**
 * Class to create PHP functions. These functions can be included in classes
 * or traits.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.0 Surface
 * @version 3.0.12 Surface
 * @package nabu\sdk\builders\php
 */
class CNabuPHPFunctionBuilder extends CNabuPHPFragmentBuilder
{
    /** @var string $name Name of the function. */
    private $name;
    /** @var array $params Parameters of this function. */
    private $params = array();
    /** @var array $body Lines in the body of this function. */
    private $body = array();
    /** @var bool $have_return_type If true, the function have a return type. */
    private $have_return_type = false;
    /** @var string $return_type If $have_return_type, contains the type to be returned by this function. */
    private $return_type = null;

    /**
     * Constructor. Receives a full description of the declaration (header) of the function.
     * @param CNabuAbstractBuilder $container Container builder object
     * @param string $name Name of the function.
     * @param bool $have_return_type If true, a return type is allowed.
     * @param string $return_type Return type to place as return cast.
     */
    public function __construct(
        CNabuAbstractBuilder $container,
        string $name,
        bool $have_return_type = false,
        string $return_type = null
    ) {
        parent::__construct($container);

        $this->name = $name;
        $this->have_return_type = $have_return_type;
        $this->return_type = $return_type;
    }

    /**
     * Gets the name of the function.
     * @return string Returns the name.
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Gets the parameters array of the function.
     * @return array Returns defined parameters as array.
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Add a parameter to this function.
     * @param string $name Name of the parameter.
     * @param string $type Type of the parameter.
     * @param bool $is_def If true $def_value is treated as the default value of parameter.
     * @param mixed $def_value Default value of parameter if $is_def is true. Scalar and string values are interpreted
     * by type. Objects are unpredictable.
     * @param string $comment_type Type of the parameter in comment.
     * @param string $comment Comment of the parameter.
     * @return bool Returns true if the parameter is added or false if already exists.
     */
    public function addParam(
        string $name,
        string $type = null,
        bool $is_def = false,
        $def_value = null,
        string $comment_type = null,
        string $comment = null
    ) {
        $param = array('name' => $name);
        if ($type !== null) {
            $param['type'] = $type;
        }

        if ($is_def) {
            $param['default'] = ($type === 'bool' && $def_value === null ? false : $def_value);
        }

        if ($comment_type !== null) {
            $param['comment_type'] = $comment_type;
        }

        if ($comment !== null) {
            $param['comment'] = $comment;
        }

        $retval = false;

        if (!array_key_exists($name, $this->params)) {
            $this->params[$name] = $param;
            $retval = true;
        }

        return $retval;
    }

    /**
     * Gets the body of the function.
     * @return array Retuns the body as array.
     */
    public function getBody() : array
    {
        return $this->body;
    }

    /**
     * Adds a body fragment to the body.
     * @param mixed $lines A string or an array representing one or more lines of code in the body.
     * @return bool Returns true if lines are added.
     */
    public function addBody($lines) : bool
    {
        $retval = false;

        if (is_string($lines)) {
            $this->body[] = $lines;
            $retval = true;
        } elseif (is_array($lines)) {
            $this->body = array_merge($this->body, $lines);
            $retval = true;
        }

        return $retval;
    }

    /**
     * Gets the Prefix of a function.
     * @return string Retuns the prefix if needed or an empty string if not. */
    protected function getPrefix() : string
    {
        return '';
    }

    protected function getComments($padding = '')
    {
        $comments = '';
        $ret_comment = '';

        if (count($this->comments) > 0) {
            foreach ($this->comments as $line) {
                if (strpos($line, '@return ') === 0) {
                    $ret_comment = $this->truncateComment($padding, $line);
                } else {
                    $comments .= $this->truncateComment($padding, $line);
                }
            }
        }

        if (count($this->params) > 0) {
            foreach ($this->params as $param) {
                if (array_key_exists('comment_type', $param)) {
                    $type = $param['comment_type'];
                } else {
                    $type = 'type';
                }
                if (array_key_exists('comment', $param)) {
                    $comment = $param['comment'];
                } else {
                    $comment = '';
                }
                $comments .= $this->truncateComment($padding, "@param $type \$$param[name] $comment");
            }
        }

        return strlen($comments) === 0 ? '' : $padding . "/**\n" . $comments . $ret_comment . $padding . " */\n";
    }

    protected function getHeader($padding = '')
    {
        $content = $padding . $this->getPrefix() . "function $this->name(";

        if (count($this->params) > 0) {
            $params = '';
            foreach ($this->params as $param) {
                if (array_key_exists('default', $param)) {
                    $def_value = $this->valueToString($param['default']);
                }
                $params .=
                        (strlen($params) > 0 ? ', ' : '')
                        . (array_key_exists('type', $param) ? $param['type'] . ' ' : '')
                        . '$' . $param['name']
                        . (array_key_exists('default', $param) ? ' = ' . $def_value : '')
                ;
                unset($def_value);
            }
            $content .= $params;
        }

        $content .= ')'
                 . ($this->have_return_type && is_string($this->return_type) && strlen($this->return_type) > 0
                    ? ' : ' . $this->return_type
                    : ''
                   )
                 . "\n$padding{\n";

        return $content;
    }

    protected function getFooter($padding = '')
    {
        return "$padding}\n";
    }
}
