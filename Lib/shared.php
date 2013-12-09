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
 * checks environment. Deactivate errors display in production.
 */

require_once (ROOT . DS . "Lib" . DS . "MuffinApplication.php");
function setReporting()
{
    if (ENV == 0)
    {
        error_reporting(E_ALL);
        ini_set("display_errors", "On");
    }
    else
    {
        error_reporting(E_ALL);
        ini_set("display_errors", "Off");
        ini_set("log_errors", "On");
        ini_set("error_log", ROOT . DS . "App" . DS . "Logs" . DS . "errors.log");
    }
}

function stripSlashesDeep($value)
{
    $value = is_array($value) ? array_map("stripSlashesDeep", $value) : stripslashes($value);
    return $value;
}

function removeMagicQuotes()
{
    if (get_magic_quotes_gpc())
    {
        $_GET = stripSlashesDeep($_GET);
        $_POST = stripSlashesDeep($_POST);
        $_COOKIE = stripSlashesDeep($_COOKIE);
    }
}

function unregisterGlobals()
{
    $array = array();
    if (ini_get("register_globals"))
    {
        //$array = array("_SESSION", "_POST", "_GET", "_COOKIE", "_REQUEST", "_SERVER", "_ENV", "_FILES");
        $array = array("_SESSION", "_POST", "_GET", "_COOKIE", "_REQUEST", "_SERVER", "_ENV", "_FILES");
    }
    foreach ($array as $value)
    {
        foreach ($GLOBALS[$value] as $key => $var)
        {
            if ($var === $GLOBALS[$key])
            {
                //  unset ($GLOBALS[$key]);
            }
        }
    }
}

/**
 * Real first work of the url analysis
 * Takes the url, gets the controller, the action and the array of parameters
 */
function callHook()
{
    global $url;

    $urlArray = array();
    $urlArray = explode("/", $url);

    if (count($urlArray) >= 1)
    {

        $controller = $urlArray[0];

        array_shift($urlArray);
        if (count($urlArray) >= 0)
        {
            //Revision 0.5 for webservices

            /*
                        $action = $urlArray[0];
                        echo $action;
                        array_shift($urlArray);
                        $queryString = $urlArray;

                        $controllerName = $controller;
                        $controller = ucwords($controller);
                        $options = array();
                        $options["controller"] = $controller;
                        $options["action"] = $action;
            */

            $options = Router::parse($url);
            //print_r($options);
            Router::load_page($options);

        }
    }
    else
    {

        $options = array();
        if ($url == "")
        {

            $url = "home";
        }

        $options["action"] = $url;
        $opt2 = Router::parse($url);
        $opt2["action"] = "home";
        if (count($opt2) > 0)
            Router::load_page($opt2);
        else
            Router::load_page($options);
    }
}

/**
 * Autoloader for classes not seen by doctrine autoloader
 * @param $className the name of the wanted class
 */
function system_autoload($className)
{
    $plugins = MuffinApplication::getPlugins();
    if (file_exists(ROOT . DS . "App" . DS . "Controllers" . DS . $className . ".php"))
    {
        require_once(ROOT . DS . "App" . DS . "Controllers" . DS . $className . ".php");
    }
    else if (file_exists(ROOT . DS . "Lib" . DS . "Models" . DS . $className . ".php"))
    {
        require_once(ROOT . DS . "Lib" . DS . "Models" . DS . $className . ".php");
    }
    else if (file_exists(ROOT . DS . "Lib" . DS . "Controllers" . DS . $className . ".php"))
    {
        require_once(ROOT . DS . "Lib" . DS . "Controllers" . DS . $className . ".php");
    }
    else if (file_exists(ROOT . DS . "App" . DS . "Models" . DS . $className . ".php"))
    {
        require_once(ROOT . DS . "App" . DS . "Models" . DS . $className . ".php");
    }
    else if (file_exists(ROOT . DS . "Lib" . DS . "Views" . DS . $className . ".php"))
    {
        require_once(ROOT . DS . "Lib" . DS . "Views" . DS . $className . ".php");
    }
    else if (file_exists(ROOT . DS . "Lib" . DS . "Views" . DS . "Helpers" . DS . $className . ".php"))
    {
        require_once(ROOT . DS . "Lib" . DS . "Views" . DS . "Helpers" . DS . $className . ".php");
    }
    else if (file_exists(ROOT . DS . "Lib" . DS . "Routing" . DS . $className . ".php"))
    {
        require_once(ROOT . DS . "Lib" . DS . "Routing" . DS . $className . ".php");
    }
    else if (file_exists(ROOT . DS . "App" . DS . "BusinessManagement" . DS . $className . ".php"))
    {
        require_once(ROOT . DS . "App" . DS . "BusinessManagement" . DS . $className . ".php");
    }
    else if (file_exists(ROOT . DS . "App" . DS . "Exceptions" . DS . $className . ".php"))
    {
        require_once(ROOT . DS . "App" . DS . "Exceptions" . DS . $className . ".php");
    }
    else if (file_exists(ROOT . DS . "App" . DS . "Business" . DS . $className . ".php"))
    {
        require_once(ROOT . DS . "App" . DS . "Business" . DS . $className . ".php");
    }
    else
    {

        foreach ($plugins as $plugin)
        {

            $classNameArray = explode("\\", $className);
            $className = $classNameArray[count($classNameArray) - 1];
            if (file_exists(ROOT . DS . "Plugins" . DS . $plugin . DS . "App" . DS . "Models" . DS . $className . ".php"))
            {
                require_once(ROOT . DS . "Plugins" . DS . $plugin . DS . "App" . DS . "Models" . DS . $className . ".php");
            }
            if (file_exists(ROOT . DS . "Plugins" . DS . $plugin . DS . "App" . DS . "Controllers" . DS . $className . ".php"))
            {
                require_once(ROOT . DS . "Plugins" . DS . $plugin . DS . "App" . DS . "Controllers" . DS . $className . ".php");
            }
            if (file_exists(ROOT . DS . "Plugins" . DS . $plugin . DS . "App" . DS . "BusinessManagement" . DS . $className . ".php"))
            {
                require_once(ROOT . DS . "Plugins" . DS . $plugin . DS . "App" . DS . "BusinessManagement" . DS . $className . ".php");
            }
            if (file_exists(ROOT . DS . "Plugins" . DS . $plugin . DS . "App" . DS . "Business" . DS . $className . ".php"))
            {
                require_once(ROOT . DS . "Plugins" . DS . $plugin . DS . "App" . DS . "Business" . DS . $className . ".php");
            }
        }
    }

}

spl_autoload_register("system_autoload");

