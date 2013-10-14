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

/*
require_once (ROOT . DS . "Lib" . DS . "shared.php");
require_once (ROOT . DS . "Config" . DS . "routes.php");
require_once (ROOT . DS . "Lib" . DS . "doctrine_bootstrap.php");


session_start();
Locale::setDefault(DEFAULT_LOCALE);
if (!isset($_COOKIE["locale"]) && !isset($_SESSION["locale"]))
{
    Intl::setLocale("en-US");
}
else
{
    if (isset($_SESSION["locale"]))
    {
        Intl::setLocale($_SESSION["locale"]);
    }
}


setReporting();
removeMagicQuotes();
unregisterGlobals();
callHook();
*/


require_once("MuffinApplication.php");
MuffinApplication::start();
