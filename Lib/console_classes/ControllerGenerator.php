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

class ControllerGenerator
{
    private $name = "name";
    private $actions = array();


    public function write($force = false)
    {
        $file = "../../App/Controllers/" . ucfirst($this->name) . "sController.php";
        $file_exists = file_exists($file);
        $data = "<?php\n/**\n";
        $data .= " * Date: " . date("d/m/Y") . "\n";
        $data .= " * Time: " . date("H:i") . "\n";
        $data .= " * This is the model class called " . ucfirst($this->name) . "\n";
        $data .= " */\n\n";
        $data .= "class " . ucfirst($this->name) . "sController extends Controller\n";
        $data .= "{\n";
        foreach ($this->actions as $action)
        {
            $data .= "    \n";
            $data .= "    /**\n";
            $data .= "     *\n";
            $data .= "     */\n";
            $data .= '    public function ' . $action . '($params = array())' . "\n";
            $data .= "     {\n";
            $data .= "        \n";
            $data .= "     }\n";
        }
        $data .= "    \n";
        $data .= "}\n\n";
        if (!$file_exists || $force)
        {
            $handle = fopen($file, 'w') or die('Cannot open file:  ' . $file);
            fwrite($handle, $data);
            fclose($handle);
            echo "Controller generated in " . $file . "\n";

            if (!file_exists("../../App/Views/" . ucfirst($this->name) . "s"))
            {
                mkdir("../../App/Views/" . ucfirst($this->name) . "s");
            }
            foreach ($this->actions as $action)
            {
                $file = "../../App/Views/" . ucfirst($this->name) . "s/" . $action . ".html.twig";
                $handle = fopen($file, 'c') or die('Cannot open file:  ' . $file);
                fclose($handle);
            }
        }
        else
        {
            echo "This controller already exists.\n";
            echo "Run this command with --force to overwrite it.\n";
        }


    }

    public function addAction($name)
    {
        $this->actions[] = $name;
    }


    public function getActions()
    {
        return $this->actions;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}
