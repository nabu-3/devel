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

namespace devel\builders\php\traits;

/**
 * This trait implements methods related with the creation of comments in PHP.
 * Classes using this trait automates the use of comments.
 * @author Rafael Gutierrez <rgutierrez@wiscot.com>
 * @version 3.0.0 Surface
 */
trait TNabuPHPCommentsBuilder
{
    /**
     * Create the comments section.
     * @param string $padding Sequence of characters to place before each line
     * of the comments.
     * @return string Return the comments string.
     */
    protected function getComments($padding = '')
    {
        $comments = '';
        
        if (count($this->comments) > 0) {
            $comments = $padding . "/**\n";
            foreach ($this->comments as $line) {
                $comments .= $this->truncateComment($padding, $line);
            }
            $comments .= $padding . " */\n";
        }
        
        return $comments;
    }
    
    protected function truncateComment($padding, $content)
    {
        $comment = '';
        
        $line = $padding . ' * ' . $content;
        
        do {
            if (strlen($line) > 120) {
                $p1 = strrpos(substr($line, 0, 120), ' ');
                if ($p1 === false) {
                    $comment .= substr($line, 0, 120) . "\n";
                    $line = $padding . ' * ' . substr($line, 120);
                } else {
                    $comment .= substr($line, 0, $p1) . "\n";
                    $line = $padding . ' * ' . substr($line, $p1 + 1);
                }
            } else {
                $comment .= $line . "\n";
                $line = '';
            }
        } while (strlen($line) > 0);
        
        return $comment;
    }
}