<?php
/**
 * Created by Antoine Jackson
 * User: Antoine Jackson
 * Date: 9/20/13
 * Time: 11:08 PM
 */

class ConfigsController extends \Controller
{
    public function front_end_config()
    {
        $config_path = FRONT_END_CONFIG;
        if (file_exists($config_path))
        {
            $config_array = json_decode(file_get_contents($config_path), true);
            $this->return_json($config_array);
        }
        else
        {
            throw new \Exception("The config file is missing");
        }
    }
}