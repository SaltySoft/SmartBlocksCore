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
class Router
{
    /**
     * @var array Contains default routes for REST actions. Completed by the routes.php file.
     */
    public static $routes = array(
        array(
            "url" => array(
                "controller" => "[a-zA-Z]+",
                "id" => "[0-9]+"
            ),
            "controller" => "",
            "action" => "show",
        ),
        array(
            "url" => array(
                "controller" => "[a-zA-Z]+",
                "id" => "[0-9]+"
            ),
            "controller" => "",
            "method" => "DELETE",
            "action" => "destroy",
        ),
        array(
            "url" => array(
                "controller" => "[a-zA-Z]+",
                "id" => "[0-9]+"
            ),
            "controller" => "",
            "method" => "PUT",
            "action" => "update",
        ),

        array(
            "url" => array(
                "controller" => "[a-zA-Z]+",
                "action" => "show",
                "id" => "[0-9]+"
            ),
            "controller" => "",
            "action" => "show",
        ),
        array(
            "url" => array(
                "controller" => "[a-zA-Z]+",
                "action" => "edit",
                "id" => "[0-9]+"
            ),
            "controller" => "",
            "action" => "edit",
        ),
        array(
            "url" => array(
                "controller" => "[a-zA-Z]+",
                "action" => "new"
            ),
            "controller" => "",
            "action" => "new",
        ),
        array(
            "url" => array(
                "controller" => "[a-zA-Z]+",
                "action" => "create"
            ),
            "controller" => "",
            "action" => "create",
        ),
        array(
            "url" => array(
                "controller" => "[a-zA-Z]+",
                "action" => "update",
                "id" => "[0-9]+"
            ),
            "controller" => "",
            "action" => "update",
        ),
        array(
            "url" => array(
                "controller" => "[a-zA-Z]+",
                "action" => "destroy",
                "id" => "[0-9]+"
            ),
            "controller" => "",
            "action" => "destroy",
        ),
        array(
            "url" => array(
                "controller" => "[a-zA-Z]+",
                "action" => "[a-zA-Z]+"
            ),
            "controller" => "",
            "action" => "",
        ),
        array(
            "url" => array(
                "controller" => "[a-zA-Z]+",
            ),
            "controller" => "",
            "action" => "index",
        ),
        array(
            "url" => array(
                "controller" => "[a-zA-Z]+",
            ),
            "method" => "POST",
            "controller" => "",
            "action" => "create",
        ),


    );

    static $home_root = array(
        "controller" => "Pages",
        "action" => "home"
    );

    /**
     * Temporary function to add routes (to be used in Config/routes.php)
     * @static
     * @param $regexp_array
     */
    static function connect_array($regexp_array)
    {
        //self::$routes[] = $regexp_array;
        array_unshift(self::$routes, $regexp_array);
    }

    /*
    /**
     * param => parameter to be taken [n] => "param"
     * :key => parameter to be taken as "key" => "/[a-zA-Z]+/"
     * #key => parameter to be taken as "key" => "/[0-9]+/"
     * @static
     * @param $array must be of the form array ("url" => "/nice/url/:with/#params")
     */
    static function connect($array)
    {
        $route = array();
        if ($array["url"] == "root")
        {
            self::$home_root["controller"] = $array["controller"];
            self::$home_root["action"] = $array["action"];
            return;
        }
        $url_array = explode("/", $array["url"]);
        while (count($url_array) > 0 && $url_array[0] == "")
            array_shift($url_array);
        $route["url"] = array();
        foreach ($url_array as $url_part)
        {
            $columnpos = strpos($url_part, ":");
            if (!($columnpos === false) && $columnpos == 0)
            {
                $url_part = ltrim($url_part, ":");
                $route["url"][$url_part] = "[a-zA-Z\32-\151]+";
            }
            else
            {
                $columnpos = strpos($url_part, "#");
                if (!($columnpos === false) && $columnpos == 0)
                {
                    $url_part = ltrim($url_part, "#");
                    $route["url"][$url_part] = "[0-9]+";
                }
                else
                {
                    $route["url"][] = $url_part;
                }
            }
        }
        if (isset($array["controller"]))
            $route["controller"] = $array["controller"];
        if (isset($array["action"]))
            $route["action"] = $array["action"];
        if (isset($array["method"]))
            $route["method"] = $array["method"];
        self::connect_array($route);
    }


    private static function construct_url($route, $params)
    {
        $url = rtrim(SERVER_ROOT, "/");
        foreach ($route["url"] as $key => $url_part)
        {
            if (is_numeric($key))
                $url .= "/" . $url_part;
            else
                $url .= "/" . $params[$key];
        }
        return $url;
    }

    /**
     * TODO this function is not currently done
     * Returns the url corresponding to a set of parameters, a controller and an action
     * The array must be of the following form
     * array(
     *      "controller" => "cont_name",
     *      "action" => "act_name",
     *      "param1" => value,
     *      "param2" => value
     * )
     * @static
     * @param $array
     */
    static function get_url($array)
    {
        $params = $array;
        $param_names = array_keys($params);
        $param_names = array_keys($params);
        $found_route = false;
        $associated_route = null;
        $i = 0;
        foreach (self::$routes as $route)
        {
            if (!$found_route)
            {
                $keys = array_keys($route["url"]);
                $continue = true;
                foreach ($keys as $k => $key)
                {
                    if (!preg_match("/" . $route["controller"] . "/", $array["controller"]))
                    {
                        $continue = false;
                    }
                    if (!preg_match("/" . $route["action"] . "/", $array["action"]))
                        $continue = false;
                    if (preg_match("/[0-9]+/", $key) || $key == "controller" || $key == "action")
                    {
                        unset($keys[$k]);
                    }
                }
                if ($continue)
                {
                    $count = count(array_intersect($keys, $param_names));
                    $found_route = ($count == count($keys) && $count == count($param_names) - 2);
                }
                if ($found_route)
                {
                    $associated_route = $route;
                }
                $i++;
            }
        }
        $params["controller"] = $params["controller"];
        $params["action"] = $params["action"];
        $url = "javascript:void()";
        if ($associated_route != null)
            $url = self::construct_url($associated_route, $params);
        return $url;
    }


    /**
     * Get the parameters array from a string URL.
     * @static
     * @param $url string URL
     * @return array Array of parameters
     */
    static function parse($url)
    {
        $url = rtrim($url, "/");
        $url_array = explode("/", $url);
        //Removed for rest method update
        if (count($url_array) <= 1)
        {
            //   $url_array[] = "";
        }
        $found_path = false;
        $result = array();

        $size = count($url_array);
        $i = 0;

        $plugins = MuffinApplication::getPlugins();
        if (isset($url_array[0]))
        {
            foreach ($plugins as $plugin)
            {
                if ($plugin == $url_array[0])
                {
                    $namespace = $plugin;
                    array_shift($url_array);
                    if (count($url_array) <= 1)
                    {
                        //Removed for rest method update
                        //  $url_array[] = "";
                    }
                }
            }
        }

        $result = array();
        foreach (self::$routes as $route)
        {
            if (($found_path == false || (isset($route["method"]) && $_SERVER["REQUEST_METHOD"] == $route["method"])) && !(isset($route["method"]) && $_SERVER["REQUEST_METHOD"] != $route["method"]))
            {
                $i = 0;

                if (count($route["url"]) == count($url_array)) //check if same parameters count
                {

                    foreach ($route["url"] as $key => $param) //foreach param in the route, check if corresponds
                    {


                        if (preg_match("/^" . $param . "$/", $url_array[$i]) || ($i == count($url_array) - 1 && preg_match("/" . $param . "/", $url_array[$i])))
                        {
                            $result[$key] = $url_array[$i];


                            $i++;
                            if ($i == count($route["url"]) && $i == count($url_array)) //all params matched -> return the result array
                            {

                                $found_path = true;
                                if (!isset($result["controller"]))
                                    $result["controller"] = $route["controller"];


                                if (isset($route["action"]) && $route["action"] != "")
                                {
                                    $result["action"] = $route["action"];
                                }

                                break;
                            }


                        }
                        else
                        {
                            $i = 0;
                            break;
                        }
                    }
                }
            }
        }

        if (isset($namespace))
        {
            $result["namespace"] = $namespace;
        }
        if (!isset($result["action"]))
        {
            $result["action"] = "";
        }
        if (!isset($result["controller"]))
        {
            $result["controller"] = "";
        }
        $actionTypeArray = explode(".", $result["action"]);
        $result["responseType"] = "html";
        if (count($actionTypeArray) > 1)
        {
            $result["responseType"] = $actionTypeArray[1];
        }
        $result["action"] = $actionTypeArray[0];
        $result["size"] = $size;
        return $result;
    }


    public static function redirect($redirection)
    {
        if (is_array($redirection))
        {
            if (isset($redirection["controller"]) && isset($redirection["action"])) ;
            {
                header("Location:" . rtrim(SERVER_ROOT, "/") . DS . $redirection["controller"] . DS . $redirection["action"]);
            }
        }
        else
        {
            header_remove();
            header("Location:" . rtrim(SERVER_ROOT, "/") . $redirection);
            flush();
            exit;
        }
    }

    /**
     * Shows the asked page or shows a 404 if the page isn't found
     * @param $parameters array calculated from the parse function above
     */
    public static function load_page($parameters)
    {
        if (($parameters["controller"] == "" && $parameters["action"] == "") && $parameters["size"] <= 2)
        {
            $parameters["controller"] = self::$home_root["controller"];
            $parameters["action"] = self::$home_root["action"];
        }
        $controllerName = $parameters["controller"];
        unset($parameters["controller"]);
        $controller = $controllerName . "Controller";
        $action = $parameters["action"];
        unset($parameters["action"]);
        if (!isset($model))
            $model = rtrim($controllerName, "s");
        if ($model == "Page")
        {
            $model = "";
        }
        $rendered = false;
        if (file_exists(ROOT . DS . "App" . DS . "Controllers" . DS . $controller . ".php"))
        {

            if ((int)method_exists($controller, $action))
            {
                $dispatch = new $controller($model, $controllerName, $action, $parameters["responseType"]);
                $dispatch->setParams($parameters);
                $dispatch->executeAction($action, $parameters);
                $rendered = true;
            }
            else
            {
                //self::redirect(array());
            }
        }
        else
        {

            $plugins = MuffinApplication::getPlugins();
            $controllerArray = explode(DS, $controller);
            foreach ($plugins as $plugin)
            {

                if (file_exists(ROOT . DS . "Plugins" . DS . $plugin . DS . "App" . DS . "Controllers" . DS . $controllerArray[count($controllerArray) - 1] . ".php"))
                {

                    $controller_nam = $parameters["namespace"] . "\\" . $controller;

                    if ((int)method_exists($controller_nam, $action))
                    {

                        $dispatch = new $controller_nam($model, $controllerName, $action, $parameters["responseType"]);
                        $dispatch->setPlugin($plugin);
                        $dispatch->executeAction($action, $parameters);
                        $rendered = true;
                    }
                    else
                    {

                        //self::redirect(array());
                    }
                }
            }


        }
        if (!$rendered)
        {
            MuffinApplication::setHttpResponseCode(404);
            header("HTTP/1.0 " . MuffinApplication::getHttpResponseCode());
            if ((int)method_exists("PagesController", "e404"))
            {
                $dispatch = new PagesController("", "Pages", "e404", $parameters["responseType"]);
                $dispatch->setStatus(404);
                $rendered = true;
            }
            else
            {
                echo "Server says : 404 Not found.<br />The muffin you're looking for may have been eaten.";
            }
        }
    }
}
