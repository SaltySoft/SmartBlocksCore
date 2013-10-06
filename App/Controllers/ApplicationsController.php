<?php
/**
 * Created by Antoine Jackson
 * User: Antoine Jackson
 * Date: 9/18/13
 * Time: 3:06 AM
 */

require_once(ROOT . DS . "App" . DS . "BusinessManagement" . DS . "SmartBlocks.php");
class ApplicationsController extends \Controller
{
    public function index()
    {
        $response = array();
        $kernel_info = \BusinessManagement\SmartBlocks::loadBlockInformation("Kernel", true);
        if (isset($kernel_info["apps"]) && is_array($kernel_info["apps"]))
        {
            foreach ($kernel_info["apps"] as $app_array)
            {
                $response[] = $app_array;
            }
        }

        $directories = \BusinessManagement\SmartBlocks::getPluginsDirectoriesName();


        foreach ($directories as $directory)
        {
            $block_info = \BusinessManagement\SmartBlocks::loadBlockInformation($directory);
            if (!isset($block_info["restricted_to"]) || (\User::logged_in() && \User::current_user()->hasRight($block_info["restricted_to"])))
            {
                if (isset($block_info["apps"]) && is_array($block_info["apps"]))
                {
                    foreach ($block_info["apps"] as $app_array)
                    {
                        if (isset($app_array["restricted_to"]))
                        {
                            if (is_object(\User::current_user()))
                            {
                                if (\User::current_user()->hasRight($app_array["restricted_to"]))
                                {
                                    $response[] = $app_array;
                                }
                            }
                        }
                        else
                        {
                            $response[] = $app_array;
                        }

                    }
                }
            }

        }

        $this->return_json($response);
    }
}