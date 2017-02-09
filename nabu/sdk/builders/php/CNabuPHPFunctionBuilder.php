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
 * @author Rafael Gutierrez <rgutierrez@wiscot.com>
 * @version 3.0.0 Surface
 */
class CNabuPHPFunctionBuilder extends CNabuPHPFragmentBuilder
{
    private $name;
    private $params = array();
    private $body = array();

    /**
     *
     * @param CNabuAbstractBuilder $container Container builder object
     * @param type $name
     */
    public function __construct($container, $name)
    {
        parent::__construct($container);

        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function addParam(
        $name,
        $type = null,
        $is_def = false,
        $def_value = false,
        $comment_type = false,
        $comment = null
    ) {
        $param = array('name' => $name);
        if ($type !== null) {
            $param['type'] = $type;
        }

        if ($is_def) {
            $param['default'] = $def_value;
        }

        if ($comment_type !== false) {
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

    public function getBody()
    {
        return $this->body;
    }

    public function addBody($lines)
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

    protected function getPrefix()
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

        $content .= ")\n$padding{\n";

        return $content;
    }

    protected function getFooter($padding = '')
    {
        return "$padding}\n";
    }
}
