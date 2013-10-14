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
 * Globals for positioning
 */
define("DS", DIRECTORY_SEPARATOR);
define("ROOT", dirname(dirname(__FILE__)));


/**
 * The DEV constant defines the environment
 * 0 - development environment
 * 1 - test environment (specific database)
 * all other values - production environment
 */
define("ENV", 0);

///** This is a hack to be able to get static files from any url */
define("SERVER_ROOT", rtrim(str_replace("Public/index.php", "", $_SERVER["SCRIPT_NAME"]), "/"));

/** This will be the title of your pages if not specified in the actions. */
define("DEFAULT_TITLE", "My website");

/**
 * This is the default locale of the website
 */
define("DEFAULT_LOCALE", "fr-FR");

define("CACHE_PREFIX", "smartblocks");
define("NODE_PORT", "10001");
define("CHUNK_SIZE", 1024*1024);

define("FRONT_END_CONFIG", ROOT . DS . "Config" . DS . "front-end-config.json");

date_default_timezone_set("Europe/Paris");