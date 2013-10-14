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

include ("Lexer.php");
include ("ModelGenerator.php");
include ("ControllerGenerator.php");


class Parser
{
    private $lexer;
    private $tree;
    private static $node_pid = null;

    public function __construct()
    {
        if (self::$node_pid == null && file_exists("nodeserver.pid"))
        {
            $pid = rtrim(file_get_contents("nodeserver.pid"), "\n");
            self::$node_pid = $pid;
        }
    }

    public function parse($string)
    {
        $this->lexer = new Lexer($string);
        $token = $this->lexer->getToken();
        switch ($token)
        {
            case "create-model":
                $this->parseModel();
                break;
            case "create-controller":
                $this->parseController();
                break;
            case "doctrine":
                $string = $this->lexer->getRest();
                if (strpos($string, ';') === false &&
                    strpos($string, '&') === false &&
                    strpos($string, '|') === false
                )
                {
                    $execution_result = array();
                    exec("php ".ROOT.DS."Lib".DS."console_classes".DS."doctrine.php " . $string, $execution_result);
                    foreach ($execution_result as $line)
                    {
                        echo $line . "\n";
                    }
                }
                break;
            case "twigclear":
                $this->clearTwig();
                break;
            case "nodejs":
//                $execution_result = array();
//                exec('cd '.ROOT.DS.Lib.DS.'JsServer; > '.ROOT.DS."App".DS."Logs".DS.'nodelog nohup  node index.js &', $execution_result);
//                foreach ($execution_result as $line)
//                {
//                    echo $line . "\n";
//                }
                $this->parseNode();
                break;
//            case "update":
//
//                break;
            default:
                if ($token != "exit")
                {
                    echo $token . " is not a recognized command.\n";
                }
                break;
        }
    }

    public function clearTwig()
    {
        $dirPath = "../../Tmp/TwigCmp";
        if (is_dir($dirPath))
        {
            self::deleteDir($dirPath);
            echo "Twig cache was cleared\n";
        }
        else
        {
            echo "Twig cache is not filled\n";
        }
    }

    public static function deleteDir($dirPath)
    {
        if (!is_dir($dirPath))
        {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/')
        {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file)
        {
            if (is_dir($file))
            {
                self::deleteDir($file);
            }
            else
            {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    public function parseModel()
    {
        $model = new ModelGenerator();
        $token = $this->lexer->getToken();
        $force = false;
        if ($token == "")
        {
            echo "Usage : create-model [--force] name fieldname:type...\n";
            return;
        }
        if ($token == "--force")
        {
            $force = true;
            $token = $this->lexer->getToken();
            if ($token == "")
            {
                echo "Usage : create-model [--force] name fieldname:type...\n";
                return;
            }
        }

        $model->setName($token);
        while ($this->lexer->canParse())
        {
            $token = $this->lexer->getToken();
            if ($token != "")
            {
                $token = explode(":", $token);
                $model->addField($token[0], $token[1]);
            }
        }
        $model->write($force);
    }

    public function parseController()
    {
        $controller = new ControllerGenerator();
        $token = $this->lexer->getToken();
        $force = false;
        if ($token == "")
        {
            echo "Usage : create-controller name action...\n";
            return;
        }

        if ($token == "--force")
        {
            $force = true;
            $token = $this->lexer->getToken();
            if ($token == "")
            {
                echo "Usage : create-controller name action...\n";
                return;
            }
        }

        $controller->setName($token);
        while ($this->lexer->canParse())
        {
            $token = $this->lexer->getToken();
            if ($token != "")
            {
                $controller->addAction($token);
            }
        }
        $controller->write($force);
    }

    public function parseNode()
    {
        $token = $this->lexer->getToken();
        if ($token == "")
        {
            echo "Usage : nodejs start [port] | stop...\n";
            return;
        }
        if ($token == "start")
        {
            $port = 8899;
            $numport = $port;
            if ($this->lexer->canParse())
            {
                $token = $this->lexer->getToken();
                if (is_numeric($token))
                {
                    $port = escapeshellarg($token);
                    $numport = $token;
                }


            }
            $execution_result = array();
            $_SERVER["SERVER_NAME"] = exec('hostname -f');


            $conn = @fsockopen("127.0.0.1", $numport);
            if (!$conn)
            {
                self::$node_pid = exec('cd ' . ROOT . DS . "Lib" . DS . 'JsServer; > ' . ROOT . DS . "App" . DS . "Logs" . DS . 'nodelog nohup  node index.js ' . $port . '& echo $!', $execution_result);

                $myFile = "nodeserver.pid";
                $fh = fopen($myFile, 'w');
                fwrite($fh, self::$node_pid . "\n");
                fclose($fh);



                echo "Node server daemon created with pid " . self::$node_pid . " on " . ($numport == 8899 ? 'default' : "") . " port " . $port . "\n";
            }
            else
            {
                fclose($conn);
                echo "The server could not be launched. Maybe the port " . $port . " is already used\n";
            }
        }

        if ($token == "stop")
        {
            //$this->node_pid = null;
            echo exec("kill " . self::$node_pid);
            echo "Stopped node server daemon (pid : " . self::$node_pid . ")\n";
            self::$node_pid = null;
            if (file_exists("nodeserver.pid"))
            {
                unlink("nodeserver.pid");
            }


            return;
        }

        if ($token == "stopall")
        {
            //$this->node_pid = null;
            echo exec("killall node");
            echo "Stopped all running node server daemon for this app.\n";
            self::$node_pid = null;
            if (file_exists("nodeserver.pid"))
            {
                unlink("nodeserver.pid");
            }


            return;
        }

        if ($token == "status")
        {
            if (self::$node_pid != null)
            {
                echo "A server has been launched from this app : pid=" . self::$node_pid . "\n";
            }
            else
            {
                echo "No server is running from this app.\n";
            }

        }

    }
}
