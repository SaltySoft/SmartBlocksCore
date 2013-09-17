<?php
/**
 * Date: 12/03/2013
 * Time: 12:33
 * This is the model class called File
 */

class FilesController extends Controller
{
    public function security_check()
    {

    }

    public function interface_security_check()
    {

    }

    private function send_notifs($folder, $update_folder = 0)
    {
        if ($update_folder == 0)
            $update_folder = $folder->getId();

        if ($folder->getParentFolder() != null)
            $this->send_notifs($folder->getParentFolder(), $update_folder);

        NodeDiplomat::sendMessage($folder->getOwner()->getSessionId(), array("app" => "k_fs", "status" => "changed_directory", "folder_id" => $update_folder == 0 ? $folder->getId() : $update_folder));
        foreach ($folder->getUsersAllowed() as $user)
        {
            NodeDiplomat::sendMessage($user->getSessionId(), array("app" => "k_fs", "status" => "changed_directory", "folder_id" => $update_folder == 0 ? $folder->getId() : $update_folder));
        }
    }

    /**
     * Lists all jobs
     * Parameters :
     * - page : if set, paged response
     * - page_size : if set, fixes number of elements per page (if page is set)
     * - filter : if set, filters jobs by name with given string
     * By default, all jobs are returned
     */
    public function index()
    {
        $em = Model::getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select("f")
            ->from("File", "f");

        $data = $this->getRequestData();
        if (!(isset($data["shared"]) && $data["shared"]))
        {
            $qb->leftJoin("f.parent_folder", "p")
                ->leftJoin("p.users_allowed", "aluser")
                ->andWhere("(f.owner = :user OR aluser = :user OR p.owner = :user)")
                ->setParameter("user", User::current_user());
        }


        if (isset($data["folder_id"]))
        {
            $parent_folder = File::find($data["folder_id"]);
            if (is_object($parent_folder))
            {
                $qb->andWhere("f.parent_folder = :parent_folder")
                    ->setParameter("parent_folder", $parent_folder->getId());
            }
            else
            {
                $qb->andWhere("f.parent_folder is NULL");
            }
        }

        if (isset($data["shared"]) && $data["shared"])
        {
            $qb->join("f.users_allowed", "f_user")
                ->andWhere("f_user = :current_user")
                ->setParameter("current_user", \User::current_user());
        }


        $files = $qb->getQuery()->getResult();

        $response = array();
        foreach ($files as $file)
        {
            $response[] = $file->toArray();
        }
        $this->render = false;
        header("Content-Type: application/json");
        echo json_encode($response);
    }


    public function show($params = array())
    {
        header("Content-Type: application/json");
        $this->render = false;

        $file = File::find($params["id"]);

        if (is_object($file))
        {
            echo json_encode($file->toArray(true));
        }
        else
        {
            $file = File::getHome();
//            var_dump($file->getSubfiles()->toArray());
            echo json_encode($file->toArray(true));
        }
    }

    public function create()
    {
        $this->security_check();
        header("Content-Type: application/json");
        $this->render = false;

        $file = new File();
        $data = $this->getRequestData();
        $file->setName($data["name"]);
        if ($file->getName() != "")
        {
            $file->setPath(md5(microtime()));

            if (isset($data["parent_folder"]) && isset($data["parent_folder"]["id"]))
            {
                $folder = File::find($data["parent_folder"]["id"]);
                if (is_object($folder) && $folder->isFolder())
                    $file->setParentFolder($folder);
            }
            else if (isset($data["parent_folder"]))
            {
                $folder = File::find($data["parent_folder"]);
                if (is_object($folder) && $folder->isFolder())
                    $file->setParentFolder($folder);
            }


            if (isset($data["owner"]))
            {
                $owner = User::find($data["owner"]["id"]);
                if (is_object($owner))
                {
                    $file->setOwner($owner);
                }
            }
            else
            {
                $file->setOwner(User::current_user());
            }

            if (isset($data["is_folder"]))
            {
                $file->setAsFolder($data["is_folder"]);
            }


            if (isset($_FILES["file"]))
            {
                $ext = explode(".", $_FILES["file"]["name"]);
                $ext = isset($ext[0]) ? "." . $ext[count($ext) - 1] : "";

                $path = ROOT . DS . "Data" . DS . "User_files" . DS;
                $file->setPath($file->getPath() . PATHINFO_EXTENSION . $ext);
                move_uploaded_file($_FILES["file"]["tmp_name"], $path . $file->getPath());
            }
            if (isset($data["data"]))
            {
                $path = ROOT . DS . "Data" . DS . "User_files" . DS;
                $exploded_name = explode(".", $file->getName());
                if (isset($data["extension"]))
                {
                    $ext = "." . $data["extension"];
                }
                else
                {
                    $ext = count($exploded_name) > 1 ? "." . $exploded_name[count($exploded_name) - 1] : "";
                }
                $file->setPath($file->getPath() . $ext);
                $data = $data["data"];
                $array = explode(",", $data);
                if (isset($array[1]))
                {
                    $data = $array[1];
                }
                if (base64_encode(base64_decode($data)) === $data)
                {
                    $data = base64_decode($data);
                }
                file_put_contents($path . $file->getPath(), $data);
            }
            $file->save();
            if (is_object($file->getParentFolder()))
                $this->send_notifs($file->getParentFolder());
            echo json_encode($file->toArray());
        }
    }

    public function update($params = array())
    {
        $this->security_check();
        header("Content-Type: application/json");
        $this->render = false;

        $file = File::find($params["id"]);
        $data = $this->getRequestData();

        if (is_object($file) && ($file->getOwner() == User::current_user() || User::is_admin()))
        {
            $file->setName($data["name"]);
            if (isset($data["parent_folder"]) && isset($data["parent_folder"]["id"]))
            {
                $folder = null;
                $folder = File::find($data["parent_folder"]["id"]);
                if (is_object($folder))
                {
                    $file->setParentFolder($folder);
                    $this->send_notifs($folder);
                }
            }
            $old_users = $file->getUsersAllowed()->toArray();
            $file->getUsersAllowed()->clear();
            foreach ($data["users_allowed"] as $user_array)
            {
                $user = \User::find($user_array["id"]);
                if (is_object($user))
                {
                    $file->addAllowedUser($user);

                }
            }

            $file->save();
            foreach ($old_users as $user)
            {
                NodeDiplomat::sendMessage($user->getSessionId(), array("app" => "k_fs", "status" => "sharing_update"));
            }
            foreach ($file->getUsersAllowed() as $user)
            {
                NodeDiplomat::sendMessage($user->getSessionId(), array("app" => "k_fs", "status" => "sharing_update"));
            }
            echo json_encode($file->toArray());
        }
        else
            echo json_encode(array("error"));
    }


    public function destroy($params = array())
    {
        $this->security_check();
        header("Content-type: application/json");
        $this->render = false;

        $file = File::find($params["id"]);

        if (is_object($file))
        {
            $folder = $file->getParentFolder();
            if (is_object($folder))
            {
                $this->send_notifs($folder);
            }

            if (file_exists(ROOT . DS . "Data" . DS . "User_files" . DS . $file->getPath()))
            {
                unlink(ROOT . DS . "Data" . DS . "User_files" . DS . $file->getPath());
            }

            $file->delete();
            echo json_encode(array("message" => "File successfully deleted"));
        }
        else
            echo json_encode(array("message" => "An error occured"));
    }

    public function file_creation_form($params = array())
    {

    }


    // Read a file and display its content chunk by chunk
    function readfile_chunked($filename, $retbytes = TRUE)
    {
        $buffer = "";
        $cnt = 0;
        // $handle = fopen($filename, "rb");
        $handle = fopen($filename, "rb");
        if ($handle === false)
        {
            return false;
        }
        while (!feof($handle))
        {
            $buffer = fread($handle, CHUNK_SIZE);
            echo $buffer;
            ob_flush();
            flush();
            if ($retbytes)
            {
                $cnt += strlen($buffer);
            }
        }
        $status = fclose($handle);
        if ($retbytes && $status)
        {
            return $cnt; // return num. bytes delivered like readfile() does.
        }
        return $status;
    }

    public function get_file($params = array())
    {
        $file = File::find($params["id"]);
        $this->render = false;
        header("Content-Type: application/force-download");

        if (is_object($file))
        {
            $ext = explode(".", $file->getPath());
            $ext = isset($ext[0]) ? "." . $ext[count($ext) - 1] : "";

            header('Content-Disposition: attachment; filename="' . $file->getName() . $ext . '"');
            echo file_get_contents(ROOT . DS . "Data" . DS . "User_files" . DS . $file->getPath());
        }
    }

    public function get_raw_file($params = array())
    {
        $data = $this->getRequestData();
        $file = File::find($data["id"]);
        $this->render = false;
        header("Content-Type: = " . $file->getType());
        if (is_object($file))
        {
            echo file_get_contents(ROOT . DS . "Data" . DS . "User_files" . DS . $file->getPath());
        }
    }
}

