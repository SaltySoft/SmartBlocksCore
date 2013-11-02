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
require_once("Classes/Error.php");
require_once("JsServer/NodeDiplomat.php");
class MuffinApplication
{
    private static $errors = array();
    private static $started = false;
    private static $http_code = 200;
    private static $plugins = array();
    private static $plugins_loaded = false;

    public static function getPlugins()
    {
        if (!self::$plugins_loaded)
            self::loadPlugins();
        return self::$plugins;
    }

    private
    static $http_headers = array(
        200 => "200 OK",
        201 => "201 Created",
        202 => "202 Accepted",
        203 => "203 Non-Authoritative Information",
        204 => "204 No Content",
        205 => "205 Reset Content",
        206 => "206 Partial Content",
        207 => "207 Multi-Status",
        208 => "208 Already Reported",
        226 => "226 IM Used",
        300 => "300 Multiple Choices",
        301 => "301 Moved Permanently",
        302 => "302 Found",
        303 => "303 See Other",
        304 => "304 Not Modified",
        305 => "305 Use Proxy",
        307 => "307 Temporary Redirect",
        400 => "400 Bad Request",
        401 => "401 Unauthorized",
        402 => "402 Payment Required",
        403 => "403 Forbidden",
        404 => "404 Not Found",
        405 => "405 Method Not Allowed",
        406 => "406 Not Acceptable",
        407 => "407 Proxy Authentication Required",
        408 => "408 Request Timeout",
        409 => "409 Conflict",
        410 => "410 Gone",
        411 => "411 Length Required",
        412 => "412 Precondition Failed",
        413 => "413 Request Entity Too Large",
        414 => "414 Request-URI Too Long",
        415 => "415 Unsupported Media Type",
        416 => "416 Requested Range Not Satisfiable",
        417 => "417 Expectation Failed",
        418 => "418 I'm a teapot",
        500 => "500 Internal Server Error",
        501 => "501 Not Implemented",
        502 => "502 Bad Gateway",
        503 => "503 Service Unavailable",
        504 => "504 Gateway Timeout",
        505 => "505 HTTP Version Not Supported",
        506 => "506 Variant Also Negotiates",
        507 => "507 Insufficient Storage",
        508 => "508 Loop Detected",
        509 => "509 Bandwidth Limit Exceeded",
        510 => "510 Not Extended",
    );

    private
    static $error_types = array(
        E_ERROR => "Fatal error",
        E_WARNING => "Warning",
        E_PARSE => "Parse error",
        E_NOTICE => "Notice",
    );

    public static function handleShutDown()
    {
        $error = error_get_last();
        if (count(MuffinApplication::getErrors()) > 0) {
            MuffinApplication::setHttpResponseCode(500);
            header("HTTP/1.0 " . MuffinApplication::getHttpResponseCode());
            if (ENV == 2) {
                //Don't display anything when in prod.
                ob_end_clean();
                if ((int)method_exists("PagesController", "e500")) {

                    $dispatch = new PagesController("", "Pages", "e500", "html");
                    $dispatch->executeAction("e500", array());

                } else {
                    echo "Server says : 500 Server internal error.<br />Oven failed, muffins are overcooked.";
                    flush();
                    exit;
                }
            } else {

            }
        }
    }


    public static function captureNormal($number, $message, $file, $line)
    {
        if (!isset(self::$error_types[$number])) {
            self::$error_types[$number] = "Undefined";
        }
        self::addError("<b>" . addslashes(self::$error_types[$number] . "</b>: " . $message) . " in <b>" . $file . "</b> at line " . $line, self::$error_types[$number]);
        error_log(self::$error_types[$number] . ": " . $message . " in " . $file . " at " . $line);

        return true;
    }

    private static function loadPlugins()
    {
        self::$plugins_loaded = true;
        if ($handle = opendir(ROOT.DS.'Plugins')) {
            /* loop through directory. */
            while (false !== ($dir = readdir($handle))) {

                if (is_dir(ROOT.DS.'Plugins'.DS.$dir) && $dir != "." && $dir != "..")
                {
                    self::$plugins[] = $dir;
                }
            }
            closedir($handle);
        }
    }

    public static function start()
    {

        if (!self::$started) {
            self::loadPlugins();
            ini_set("log_errors", 1);
            ini_set("error_log", ROOT . DS . "App" . DS . "Logs" . DS . "errors.log");
          //  register_shutdown_function(array("MuffinApplication", 'handleShutDown'));
          //  set_error_handler(array('MuffinApplication', 'captureNormal'));

            self::$started = true;
            require_once (ROOT . DS . "Lib" . DS . "shared.php");
            require_once (ROOT . DS . "Config" . DS . "routes.php");

            foreach (self::$plugins as $plugin)
            {
                if (file_exists(ROOT . DS . "Plugins". DS . $plugin . DS . "Config" . DS . "routes.php"))
                    require_once (ROOT . DS . "Plugins". DS . $plugin . DS . "Config" . DS . "routes.php");
            }

            require_once (ROOT . DS . "Lib" . DS . "doctrine_bootstrap.php");
            session_start();
            if (!isset($_COOKIE["locale"]) && !isset($_SESSION["locale"])) {
                Intl::setLocale(DEFAULT_LOCALE);
            } else {
                if (isset($_SESSION["locale"])) {
                    Intl::setLocale($_SESSION["locale"]);
                } else if (isset($_COOKIE["locale"])) {
                    Intl::setLocale($_COOKIE["locale"]);
                }
            }
            setReporting();
            removeMagicQuotes();
            unregisterGlobals();
            if (User::logged_in()) {
                $current_user = User::current_user();
                if (method_exists($current_user, "updateActivity")) {
                    $current_user->updateActivity();
                }
            } else {
                $methods = get_class_methods("User");
                try {

                    if (in_array("setSessionId", $methods)) {
                        if (isset($_SESSION["_session_id"])) {
                            $users = User::where(array("session_id" => $_SESSION["_session_id"]));

                            if (count($users) > 0) {
                                foreach ($users as $user) {
                                    $user->setSessionId(null);
                                    Model::getEntityManager()->persist($user);
                                }
                                Model::getEntityManager()->flush();
                            }
                        }
                    }
                } catch (Exception $e) {
                    self::addError($e->getMessage());
                }
            }

            ob_start();
            callHook();


        } else {
            self::addError("The application can only be started once.");
        }

    }

    public static function getErrors()
    {
        return self::$errors;
    }

    public static function addError($message, $type = "")
    {
        $error = new Error();
        $error->setMessage($message);
        if ($type != "") {
            $error->setType($type);
        }
        self::$errors[] = $error;
    }

    public static function setHttpResponseCode($code)
    {
        if (isset(self::$http_headers[$code])) {
            self::$http_code = $code;
        } else {
            self::$http_code = 500;
        }
    }

    public static function getHttpResponseCode()
    {
        return self::$http_headers[self::$http_code];
    }


    /**
     * @return string
     */
    public static function getSessionId()
    {
//        if (isset($_SESSION["_session_id"])) {
//            return $_SESSION["_session_id"];
//        } else {
//            $key = md5(microtime() . rand());
//            $_SESSION["_session_id"] = $key;
//        }
        return session_id();
    }

    public static function unsetSessionId()
    {
        unset($_SESSION["_session_id"]);
    }
}
