<?php
/**
 * Date: 11/03/2013
 * Time: 21:05
 * This is the model class called Block
 */

require_once(ROOT . DS . "App" . DS . "BusinessManagement" . DS . "SmartBlocks.php");
class BlocksController extends Controller
{
    /**
     * Web Service :
     * Get all the Applications Blocks and their respective Applications in an array.
     */
    public function index()
    {
        $response = array();
        $kernel_info = \BusinessManagement\SmartBlocks::loadBlockInformation("Kernel", true);
        $response[] = $kernel_info;

        $directories = \BusinessManagement\SmartBlocks::getPluginsDirectoriesName();

        foreach ($directories as $directory)
        {
            $block_info = \BusinessManagement\SmartBlocks::loadBlockInformation($directory);
            $response[] = $block_info;
        }

        $this->return_json($response);
    }

}