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
//
//    public function cachemanifest()
//    {
//        header('Content-Type: text/cache-manifest');
//        $this->render = false;
//        echo "CACHE MANIFEST";
//
//        $directories = \BusinessManagement\SmartBlocks::getPluginsDirectoriesName();
//
//        $dir = new RecursiveDirectoryIterator(".");
//
//// Iterate through all the files/folders in the current directory
//        foreach(new RecursiveIteratorIterator($dir) as $file) {
//            $info = pathinfo($file);
//
//            // If the object is a file
//            // and it's not called manifest.php (this file),
//            // and it's not a dotfile, add it to the list
//            if ($file->IsFile()
//                && $file != "./manifest.php"
//                    &amp;& substr($file->getFilename(), 0, 1) != ".")
//		{
//            // Replace spaces with %20 or it will break
//            echo str_replace(' ', '%20', $file) . "n";
//
//            // Add this file's hash to the $hashes string
//            $hashes .= md5_file($file);
//        }
//}
//
//// Hash the $hashes string and output
//        echo "# Hash: " . md5($hashes) . "n";
//
//    }

}