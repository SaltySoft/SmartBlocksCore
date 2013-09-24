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
class Controller
{
    protected $_model;
    protected $_controller;
    protected $_action;
    protected $_template;


    protected $render = "html";
    protected $rendered = true;
    protected $render_layout = true;
    protected $message_view = false;
    protected $errors = array();
    protected $xml = "";
    protected $continue = true;
    protected $status = 200;
    protected $layout = "default";
    protected $view = "";
    protected $_plugin_name = "";
    protected $params = array();
    protected $input = "";


    function __construct($model, $controller, $action, $responseType)
    {
        $this->input = file_get_contents('php://input');
        $this->_controller = $controller;
        $this->_action = $action;
        $this->_model = $model;
        $this->render = $responseType;
        //if ($model != "" && class_exists($model))
        //    $this->$model = new $model;
        $this->_template = new Template($controller, $action);
        $this->set("title_for_layout", DEFAULT_TITLE);
        if (method_exists($this, "before_filter"))
        {
            call_user_func_array(array($this, "before_filter"), array());
        }
    }

    function set($name, $value)
    {
        $this->_template->set($name, $value);
    }

    function get_var_array()
    {
        return $this->_template->get_var_array();
    }

    function setPlugin($name)
    {
        $this->_plugin_name = $name;
    }

    function redirect($redirection)
    {
        /*
        if (is_array($redirection))
        {
            if (isset($redirection["controller"]) && isset($redirection["action"]));
            {
                header("Location:".rtrim(SERVER_ROOT, "/").DS.$redirection["controller"].DS.$redirection["action"]);
            }
        }
        else
        {
            header("Location:".rtrim(SERVER_ROOT, "/").$redirection);
        }
        */
        $this->rendered = false;
        Router::redirect($redirection);
        $this->continue = false;
    }

    function flash($str)
    {
        $_SESSION["flash"] = $str;
    }

    public function addFlash($label, $message)
    {
        if (!isset($_SESSION["flash_messages"]))
        {
            $_SESSION["flash_messages"] = array();
        }
        $_SESSION["flash_messages"][$label] = $message;
    }

    public function executeAction($action, $parameters)
    {
        if ($this->continue)
            call_user_func_array(array($this, $action), array($parameters));
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setLayout($layout)
    {
        $plugin_path = $this->_plugin_name != "" ? "Plugins" . DS . $this->_plugin . DS : "";
        if (file_exists(ROOT . DS . "App" . DS . "Views" . DS . "Layouts" . DS . $layout . ".html.twig") ||
            file_exists(ROOT . DS . $plugin_path . DS . "App" . DS . "Views" . DS . "Layouts" . DS . $layout . ".html.twig")
        )
        {
            $this->layout = $layout;
        }
        else
        {
            MuffinApplication::addError("The layout was not found.");
        }
    }

    /**
     * @param $view_name
     * This function allows you to select a view different from the default
     * view (which normally has the same name as the action).
     */
    public function setView($view_name)
    {
        $this->view = $view_name;
    }

    function __destruct()
    {

        $this->set("errors", $this->errors);
        $this->_template->render_layout = $this->render_layout;
        $this->_template->xml = $this->xml;
        $this->_template->message_view = $this->message_view;
        $this->_template->_layout = $this->layout;
        $this->_template->_view = $this->view;
        $this->_template->_plugin = $this->_plugin_name;
        if (MuffinApplication::getHttpResponseCode() != 200)
        {

        }
        if (User::logged_in())
        {
            $this->set("user_set", true);
            $this->set("user", User::current_user());


        }
        else
        {
            $this->set("user_set", false);
        }
        //WebSockets

        $this->set("_session_id", MuffinApplication::getSessionId());
        $this->set("_socket_serv", "http://" . $_SERVER["SERVER_NAME"] . ":" . (defined("NODE_PORT") ? NODE_PORT : "8899"));

        if ($this->rendered)
        {
            if ($this->render == "html")
                $this->_template->render();
            else if ($this->render == "xml")
                $this->_template->render_xml();
        }
        if (method_exists($this, "after_filter"))
        {
            call_user_func_array(array($this, "after_filter"), array());
        }
    }

    protected function requestMethod()
    {
        return $_SERVER["REQUEST_METHOD"];
    }

    public function setParams($params)
    {
        $this->params = $params;
    }


    function parse_raw_http_request(array &$a_data, $input)
    {

        // grab multipart boundary from content type header
        if (isset($_SERVER['CONTENT_TYPE']))
        {
            preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
            if (count($matches) > 1)
            {


                $boundary = $matches[1];

                // split content by boundary and get rid of last -- element
                $a_blocks = preg_split("/-+$boundary/", $input);
                array_pop($a_blocks);

                // loop data blocks
                foreach ($a_blocks as $id => $block)
                {
                    if (empty($block))
                        continue;

                    // you'll have to var_dump $block to understand this and maybe replace \n or \r with a visibile char

                    // parse uploaded files
                    if (strpos($block, 'application/octet-stream') !== FALSE)
                    {
                        // match "name", then everything after "stream" (optional) except for prepending newlines
                        preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
                    }
                    // parse all other fields
                    else
                    {
                        // match "name" and optional value in between newline sequences
                        preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
                    }
                    $a_data[$matches[1]] = $matches[2];
                }
            }
        }

    }

    /**
     * this method returns an array containing all input HTTP data, like POST or PUT.
     * Useful for REST web-services
     * @return The array containing all data
     */
    protected function getRequestData()
    {
        $input = $this->input;
        $decoded_data = array();
        $this->parse_raw_http_request($decoded_data, $input);

        $vars = is_array($decoded_data) && count($decoded_data) > 0 ? $decoded_data : json_decode($input, true);

        $return_array = is_array($vars) ? array_merge($vars, $_POST) : $_POST;

        $return_array = is_array($return_array) ? array_merge($return_array, $_GET) : $_GET;

        if (is_array($this->params))
        {
            $return_array = array_merge($return_array, $this->params);
        }


        return $return_array;
    }


    protected function return_json($response)
    {
        header("Content-Type: application/json");
        $this->render = false;
        echo json_encode($response);
    }

    protected function json_error($message, $status = 200)
    {
        if ($status == 200)
        {
            header("HTTP/1.0 200 OK");
        }
        else
        {
            if ($status == 404)
            {
                header("HTTP/1.0 404 Not Found");
            }

            if ($status == 401)
                header("HTTP/1.0 401 Unauthorized");
            if ($status == 406)
                header("HTTP/1.0 406 Not Acceptable");
        }
        $this->return_json(array("success" => false, "error" => true, "message" => $message));
    }

    protected function json_message($message)
    {
        $this->return_json(array("success" => true, "error" => false, "message" => $message));
    }


}
