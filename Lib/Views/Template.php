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
class Template
{
    protected $variables = array();
    protected $_controller;
    protected $_action;
    protected $_header_content = "";
    public $_layout = "default";
    public $render_layout = true;
    public $render = true;
    public $xml = "";
    public $message_view = false;
    public $_view = "";
    public $_plugin = "";


    function __construct($controller, $action)
    {
        $this->_controller = $controller;
        $this->_action = $action;
        Intl::init();

    }

    /**
     * Sets a variable that will be usable in the view.
     * @param $name Name of the variable
     * @param $value Value of the variable
     */
    function set($name, $value)
    {
        $this->variables[$name] = $value;
    }

    public function get($name)
    {
        return (isset($this->variables[$name]) ? $this->variables[$name] : null);
    }

    function get_var_array()
    {
        return $this->variables;
    }

    function staticCall($class, $function, $args = array())
    {
        if (class_exists($class) && method_exists($class, $function))
            return call_user_func_array(array($class, $function), $args);
        return null;
    }

    /**
     * Gets the view and renders it in the default layout
     */
    function render()
    {
        HtmlHelper::$current_plugin = $this->_plugin;
        $plugin_path = $this->_plugin != "" ? "Plugins" . DS . $this->_plugin . DS : "";
        $loader = new Twig_Loader_Filesystem(ROOT . DS . 'App' . DS . 'Views');
        $loader->prependPath(ROOT . DS . $plugin_path . 'App' . DS . 'Views');

        $twig_config = array('autoescape' => false);
        if (ENV != 0 && ENV != 1) // Not in dev or tests
        {
            $twig_config["cache"] = ROOT . DS . "Tmp" . DS . "TwigCmp";
        }

        $twig = new Twig_Environment($loader, $twig_config);
        extract($this->variables);
        $twig->addFunction('staticCall', new Twig_Function_Function('staticCall'));
        if (isset($_SESSION["flash"]) && $_SESSION["flash"] != "") {
            $this->set("flash", $_SESSION["flash"]);
            $this->variables["_flash_"] = $_SESSION["flash"];
            $this->variables["HtmlHelper"] = new HtmlHelper();
        }

        if (isset($_SESSION["flash_messages"]) && count($_SESSION["flash_messages"]) > 0) {
            $this->set("flash_messages", $_SESSION["flash_messages"]);
            $this->variables["_flash_messages_"] = $_SESSION["flash_messages"];
        }
        $twig->addGlobal("HtmlHelper", new HtmlHelper());
        $twig->addFilter("tr", new Twig_Filter_Function("Intl::translate"));
        $twig->addFilter("resource", new Twig_Filter_Function("HtmlHelper::resource_path"));

        if ($this->message_view) {
            $this->variables["_content_for_layout_"] = $twig->render("Layouts" . DS . "message" . ".php", $this->variables);
        } else if ($this->render) {


            if ($this->_view != "") //Checks if view was specified in controller. (v. 0.11)
            {
                $this->variables["_content_for_layout_"] = $twig->render($this->_view, $this->variables);
            } else if (file_exists(ROOT . DS . $plugin_path . "App" . DS . "Views" . DS . $this->_controller . DS . $this->_action . ".php") ||
                file_exists(ROOT . DS . "App" . DS . "Views" . DS . $this->_controller . DS . $this->_action . ".php")
            ) {
                $this->variables["_content_for_layout_"] = $twig->render($this->_controller . DS . $this->_action . ".php", $this->variables);
            } else if (file_exists(ROOT . DS . $plugin_path . "App" . DS . "Views" . DS . $this->_controller . DS . $this->_action . ".html.twig") ||
                file_exists(ROOT . DS . "App" . DS . "Views" . DS . $this->_controller . DS . $this->_action . ".html.twig")
            ) {
                $this->variables["_content_for_layout_"] = $twig->render($this->_controller . DS . $this->_action . ".html.twig", $this->variables);
            } else {
                MuffinApplication::addError("The view " . $this->_action . " was not found");
            }
            if ($this->render_layout) {
                if (ENV == 0 && count(MuffinApplication::getErrors()) > 0) {
                    $this->variables["muffin_errors"] = MuffinApplication::getErrors();
                }

                if (file_exists(ROOT . DS . "App" . DS . "Views" . DS . "Layouts" . DS . $this->_layout . ".html.twig") ||
                    file_exists(ROOT . DS . $plugin_path . DS . "App" . DS . "Views" . DS . "Layouts" . DS . $this->_layout . ".html.twig")
                ) {
                    echo $twig->render("Layouts" . DS . $this->_layout . ".html.twig", $this->variables);
                }

            } else {
                echo $this->variables["_content_for_layout_"];
            }
        }
        $_SESSION["flash"] = "";
        $_SESSION["flash_messages"] = array();


    }

    function render_xml()
    {
        $loader = new Twig_Loader_Filesystem(ROOT . DS . 'App' . DS . 'Views');
        $twig = new Twig_Environment($loader, array("autoescape" => false));
        $twig->addFunction('staticCall', new Twig_Function_Function('staticCall'));
        if (isset($_SESSION["flash"]) && $_SESSION["flash"] != "") {
            $this->set("flash", $_SESSION["flash"]);
            $this->variables["_flash_"] = $_SESSION["flash"];
        }
        $twig->addGlobal("HtmlHelper", new HtmlHelper());
        extract($this->variables);
        if ($this->render) {
            header("content-type: text/xml");
            echo '<?xml version="1.0"?>' . "\n";
            echo "<root>\n";
            echo $this->xml;
            echo "</root>\n";
        }
        $_SESSION["flash"] = "";
    }
}
