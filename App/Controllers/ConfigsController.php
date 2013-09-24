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
            $config_array["server_name"] = $_SERVER["SERVER_NAME"];
            $config_array["session_id"] = session_id();
            $this->return_json($config_array);
        }
        else
        {
            throw new \Exception("The config file is missing");
        }
    }

    public function send_test()
    {
        $this->render = false;
        \NodeDiplomat::sendMessage(session_id(), array(
            "test" => "blabla",
            "huhu" => "testest"
        ));
    }
}