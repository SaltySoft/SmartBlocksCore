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

    public function style_list()
    {
        $response = array();
        $kernel_info = \BusinessManagement\SmartBlocks::loadBlockInformation("Kernel", true);
        $response[] = '/Apps/Common/Css/main.less';

        $directories = \BusinessManagement\SmartBlocks::getPluginsDirectoriesName();

        foreach ($directories as $directory)
        {
            if (file_exists(ROOT . DS . "Plugins" . DS . $directory . DS . "Public" . DS . "main.less"))
            {
                $response[] = '/' .$directory . '/main.less';
            }
        }

        $this->render = false;
        header("Content-Type: text/css");

        foreach ($response as $r)
        {
            echo '@import "' . $r . '";'."\n";
        }
    }

    public function manifest()
    {
        header('Content-Type: text/cache-manifest');
        $this->render = false;
        echo "CACHE MANIFEST\n";
        $hashes = "";
        $directories = \BusinessManagement\SmartBlocks::getPluginsDirectoriesName();

        foreach ($directories as $dirr)
        {
            $dir = new RecursiveDirectoryIterator(ROOT . DS . "Plugins" . DS . $dirr . DS . "Public");
            foreach (new RecursiveIteratorIterator($dir) as $file)
            {
                $info = pathinfo($file);

                if ($file->IsFile()
                    && $file != "./manifest.php" && substr($file->getFilename(), 0, 1) != "."
                    && (
                        strpos($file->getFilename(), '.js') !== FALSE ||
                            strpos($file->getFilename(), '.html') !== FALSE ||
                            strpos($file->getFilename(), '.less') !== FALSE ||
                            strpos($file->getFilename(), '.css') !== FALSE ||
                            strpos($file->getFilename(), '.jpeg') !== FALSE ||
                            strpos($file->getFilename(), '.png') !== FALSE ||
                            strpos($file->getFilename(), '.gif') !== FALSE ||
                            strpos($file->getFilename(), '.wav') !== FALSE ||
                            strpos($file->getFilename(), '.ttf') !== FALSE
                    )
                )
                {
                    $file_name = str_replace(ROOT . DS . "Plugins" . DS . $dirr . DS . "Public", "/" . $dirr, $file);
                    $file_name = str_replace(DS, "/", $file_name);
                    echo str_replace(' ', '%20', $file_name) . "\n";

                    $hashes .= md5_file($file);
                }
            }
        }
        $dir = new RecursiveDirectoryIterator(ROOT . DS . "Public");
        foreach (new RecursiveIteratorIterator($dir) as $file)
        {
            $info = pathinfo($file);

            if ($file->IsFile()
                && $file != "./manifest.php" && substr($file->getFilename(), 0, 1) != "."
                && (
                    strpos($file->getFilename(), '.js') !== FALSE ||
                        strpos($file->getFilename(), '.html') !== FALSE ||
                        strpos($file->getFilename(), '.less') !== FALSE ||
                        strpos($file->getFilename(), '.css') !== FALSE ||
                        strpos($file->getFilename(), '.jpeg') !== FALSE ||
                        strpos($file->getFilename(), '.png') !== FALSE ||
                        strpos($file->getFilename(), '.gif') !== FALSE ||
                        strpos($file->getFilename(), '.wav') !== FALSE ||
                        strpos($file->getFilename(), '.ttf') !== FALSE
                )
            )
            {
                $file_name = str_replace(ROOT . DS . "Public", "", $file);
                $file_name = str_replace(DS, "/", $file_name);
                echo str_replace(' ', '%20', $file_name) . "\n";

                $hashes .= md5_file($file);
            }
        }

        echo "NETWORK:\n*\n";

        echo "# Hash: " . md5($hashes) . "\n";


    }

}