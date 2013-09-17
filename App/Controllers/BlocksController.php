<?php
/**
 * Date: 11/03/2013
 * Time: 21:05
 * This is the model class called Block
 */

require_once(ROOT . DS . "App" . DS . "BusinessManagement" . DS . "SmartBlocks.php");
class BlocksController extends Controller
{
    private function security_check($user = null)
    {
        if (!User::logged_in() || !(User::current_user()->is_admin() || User::current_user() == $user))
        {
            $this->redirect("/Users/user_error");
        }
    }

    private function interface_security_check($user = null)
    {
        if (!User::logged_in() || !(User::current_user()->is_admin() || User::current_user() == $user))
        {
            $this->redirect("/");
        }
    }

    public function configure()
    {
        //security_check(User::current_user());
        if (!User::logged_in() || !User::is_admin())
            $this->redirect("/");
        \BusinessManagement\SmartBlocks::loadAllBlocksAndApps();
        $this->render = false;
    }

    /**
     * Web Service :
     * Get all the Applications Blocks and their respective Applications in an array.
     */
    public function index()
    {
        $response = array();
        $kernel = ApplicationBlock::where(array("token" => "kernel"));
        $blocks[] = $kernel[0];

        foreach (ApplicationBlock::all() as $block)
        {
            if ($block->getToken() != "kernel")
                $blocks[] = $block;
        }
        foreach ($blocks as $block)
        {
            $response[] = $block->toArray();
        }
        $this->render = false;
        header("Content-Type: application/json");
        echo json_encode($response);
    }

    public function show($params = array())
    {
        $block = ApplicationBlock::find($params['id']);
        $this->render = false;
        header("Content-Type: application/json");
        echo json_encode($block->toArray());
    }

    public function create()
    {
    }

    public function update($params = array())
    {
    }

    public function destroy($params = array())
    {
    }
}