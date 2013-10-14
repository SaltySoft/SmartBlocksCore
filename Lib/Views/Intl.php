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

class Intl
{
    static private $language;
    static private $translations = array();
    static private $translations_default = array();


    static public function getLocale()
    {
        return str_replace("_", "-", self::$language);
    }

    static public function getDefaultLocale()
    {
        return str_replace("_", "-", DEFAULT_LOCALE);
    }

    static protected function setLanguage($language)
    {
        self::$language = str_replace("_", "-", $language);
    }

    /**
     * @static
     * @throws Exception
     *
     * This function initiates the internationalization class for each request.
     * May be slow, could be subject to later caching.
     */
    static function init()
    {
        if (file_exists(ROOT . DS . "App" . DS . "Intl" . DS . "dictionary_" . self::getLocale() . ".xml"))
        {
            $dictionary = simplexml_load_file(ROOT . DS . "App" . DS . "Intl" . DS . "dictionary_" . self::getLocale() . ".xml");
            $keywords = $dictionary->keyword;
            foreach ($keywords as $k)
            {
                Intl::$translations[(string)$k->attributes()->value] = (string)$k;
            }
        }
        else
        {
            //throw new Exception("This language is not available.");
        }
        if (file_exists(ROOT . DS . "App" . DS . "Intl" . DS . "dictionary_" . self::getDefaultLocale() . ".xml"))
        {
            $dictionary = simplexml_load_file(ROOT . DS . "App" . DS . "Intl" . DS . "dictionary_" . self::getDefaultLocale() . ".xml");
            $keywords = $dictionary->keyword;
            foreach ($keywords as $k)
            {
                Intl::$translations_default[(string)$k->attributes()->value] = (string)$k;
            }
        }
        else
        {
            //throw new Exception("This language is not available.");
        }
    }

    static function setLocale($locale = "")
    {
        if ($locale == "")
        {
            $locale = DEFAULT_LOCALE;
        }
        setcookie("locale", $locale, time() + 3600 * 24);
        $_SESSION["locale"] = $locale;
        self::setLanguage($locale);
    }

    static function translate($string)
    {
        if (isset(Intl::$translations[$string]))
        {
            return Intl::$translations[$string];
        }
        else if (isset(Intl::$translations_default[$string]))
        {
            if (ENV == 0)
            {
                return "<b>[</b><i>" . $string . "</i><b>]</b>";
            }
            echo Intl::$translations_default[$string];
        }
        else
        {
            if (ENV == 0)
            {
                return "<b>[</b><i>" . $string . "</i><b>]</b>";
            }
        }
    }
}
