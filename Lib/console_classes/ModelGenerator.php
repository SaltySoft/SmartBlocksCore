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

class ModelGenerator
{
    private $name = "name";
    private $fields = array();


    public function write($force = false)
    {
        $file = "../../App/Models/" . ucfirst($this->name) . ".php";
        $file_exists = file_exists($file);

        $data = "<?php\n/**\n";
        $data .= " * Date: " . date("d/m/Y") . "\n";
        $data .= " * Time: " . date("H:i") . "\n";
        $data .= " * This is the model class called " . ucfirst($this->name) . "\n";
        $data .= " */\n\n";
        $data .= "/**\n";
        $data .= " * @Entity @Table(name=\"" . strtolower($this->name) . "s\")\n";
        $data .= " */\n";
        $data .= "class " . ucfirst($this->name) . " extends Model\n";
        $data .= "{\n";
        $data .= "    /**\n";
        $data .= "     * @Id @GeneratedValue(strategy=\"AUTO\") @Column(type=\"integer\")\n";
        $data .= "     */\n";
        $data .= '    public $id;' . "\n";
        foreach ($this->fields as $field)
        {
            $data .= "    \n";
            $data .= "    /**\n";
            $data .= "     * @Column(type=\"" . $field["type"] . "\")\n";
            $data .= "     */\n";
            $data .= '    private $' . $field["name"] . ';' . "\n";
        }
        $data .= "    \n";
        $data .= "}\n\n";
        if (!$file_exists || $force)
        {
            $handle = fopen($file, 'w') or die('Cannot open file:  ' . $file);
            fwrite($handle, $data);
            fclose($handle);
            echo "Model generated in " . $file."\n";
            echo "Run doctrine orm:schema:update --force to update the database accordingly\n";
        }
        else
        {
            echo "This model already exists.\n";
            echo "Run this command with --force to overwrite it.\n";
        }


    }

    public function addField($name, $type)
    {
        $this->fields[] = array("name" => $name, "type" => $type);
    }


    public function getFields()
    {
        return $this->fields;
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
