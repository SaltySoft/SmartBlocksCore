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

class HtmlHelper extends Helper
{

    public static $current_plugin = "";

    static function  importCss($name)
    {
        return '<link rel="stylesheet" href="'.SERVER_ROOT.'/css/'.$name.'.css" type="text/css">'."\n";
    }

    static function importJs($name)
    {
        echo '<script type="text/javascript" src="'.SERVER_ROOT.'/javascript/'.$name.'.js"></script>'."\n";
    }

    static function link($text, $options)
    {

        if (is_array($options))
            return '<a href="'.Router::get_url($options).'">'.$text.'</a>';
        else
        {
            $start = (count($options) > 1 && $options[0] == '/') ? SERVER_ROOT : "";
            return '<a href="'.$start.($options).'">'.$text.'</a>';
        }
    }

    static function snippet($file, $variables = array())
    {
        $loader = new Twig_Loader_String();
        $twig = new Twig_Environment($loader);
        extract($variables);
        if(file_exists(ROOT.DS."App".DS."Views".DS."Snippets".DS.$file.".php"))
        {
            include(ROOT.DS."App".DS."Views".DS."Snippets".DS.$file.".php");
        }
    }

    public static function resource_path($string)
    {
        return "/" . self::$current_plugin . "/" . $string;
    }
}