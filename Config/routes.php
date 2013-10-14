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


/**
 * This is the routes file.
 * Routes must respect this format:
 * for a url of the form /stuff/word/:param_name1/#number...
 * like would be /precise/words
 * Router::connect(array(
 *     "url" => "/stuff/word/:param_name1/#number") // the structure of the address
 *     "controller" => "controller_name"
 *     "action" => "action_name"
 * ));
 *
 */
Router::connect(array("url" => "root", "controller" => "Pages", "action" => "home"));
Router::connect(array("url" => "/set_locale/:locale", "controller" => "Pages", "action" => "setLocale"));

Router::connect(array("url" => "/Files/get_file/#id", "controller" => "Files", "action" => "get_file"));

Router::connect(array("url" => "Users/Connect/:username/:password", "controller" => "Users", "action" => "connect"));
Router::connect(array("url" => "/landing", "controller" => "Pages", "action" => "landing_page"));