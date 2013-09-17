<?php
/**
 * Copyright (C) 2013 Antoine Jackson
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE
 * OR OTHER DEALINGS IN THE SOFTWARE.
 */

class Lexer
{

    private $data;
    private $current;
    private $canParse = true;
    private $current_state = array();


    public function __construct($string)
    {
        $this->data = explode(" ", $string);
        $this->current_state = $this->data;
        $this->current = 0;
    }

    public function canParse()
    {
        return $this->canParse;
    }

    public function getRest()
    {
        return implode(" ", $this->current_state);
    }

    public function getToken()
    {
        $token = "";
        if (count($this->data) > $this->current)
        {
            array_shift($this->current_state);
            $token = $this->data[$this->current++];
        }
        else
        {
            $this->canParse = false;
        }
        return $token;
    }
}
