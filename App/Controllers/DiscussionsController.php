<?php
/**
 * Date: 10/03/2013
 * Time: 16:19
 * This is the model class called Discussion
 */

class DiscussionsController extends Controller
{
    public function security_check()
    {

        if (!User::logged_in())
        {
            $this->redirect("/Discussions/error");
        }

    }

    public function error()
    {
        $this->render = false;
        header("Content-Type: application/json");
        echo json_encode(array("status" => "error"));
    }

    public function interface_security_check()
    {

    }


    private function tokenize($string)
    {
        $string = preg_replace("/[^a-zA-Z0-9]/", "_", strtolower($string));
        return $string;
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
        $this->security_check();
        $em = Model::getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select("d")
            ->from("Discussion", "d");

        if (isset($_GET["page"]))
        {
            $page = (isset($_GET["page"]) ? $_GET["page"] : 1);
            $page_size = (isset($_GET["page_size"]) ? $_GET["page_size"] : 10);
            $qb->setFirstResult(($page - 1) * $page_size)
                ->setMaxResults($page_size);
        }


        if (isset($_GET["filter"]) && $_GET["filter"] != "")
        {
            $qb->andWhere("d.name LIKE :name")
                ->setParameter("name", '%' . mysql_real_escape_string($_GET["filter"]) . '%');
        }

        if (isset($_GET["user_id"]))
        {
            if (isset($_GET["user2"]))
            {


                $user2 = \User::find($_GET["user2"]);
                $qb->join("d.participants", "p")
                    ->andWhere("p = :user2 OR p.id = :user_id")
                    ->setParameter("user_id", $_GET["user_id"])
                    ->setParameter("user2", $user2);
            }
            else
            {
                $qb->join("d.participants", "p")
                    ->andWhere("p.id = :user_id")
                    ->setParameter("user_id", $_GET["user_id"]);
            }
        }


        $discussion = array();
        if (isset($_GET["user_id"]) || User::current_user()->is_admin())
        {
            $discussions = $qb->getQuery()->getResult();
        }


        $response = array();

        foreach ($discussions as $discussion)
        {
            $response[] = $discussion->toArray();
        }
        $this->render = false;
        header("Content-Type: application/json");
        echo json_encode($response);
    }


    public function show($params = array())
    {
        header("Content-Type: application/json");
        $this->render = false;

        $discussion = Discussion::find($params["id"]);

        if (is_object($discussion))
        {
            echo json_encode($discussion->toArray(1));
        }
        else
        {
            echo json_encode(array("error"));
        }
    }

    public function create()
    {
        $this->security_check();
        header("Content-Type: application/json");
        $this->render = false;

        $discussion = new Discussion();
        $data = $this->getRequestData();
        $discussion->setCreator(User::current_user());

        foreach ($data["participants"] as $part_array)
        {
            $user = User::find($part_array["id"]);
            if (is_object($user))
            {
                $discussion->addParticipant($user);
            }
        }

        $discussion->setName(isset($data["name"]) ? $data["name"] : "Discussion");

        $discussion->save();

        foreach ($discussion->getParticipants() as $user)
        {
            NodeDiplomat::sendMessage($user->getSessionId(), array("app" => "k_chat", "status" => "new_discussion"));
        }


        echo json_encode($discussion->toArray());

    }

    public function update($params = array())
    {
        $this->security_check();
        header("Content-Type: application/json");
        $this->render = false;

        $discussion = Discussion::find($params["id"]);
        $data = $this->getRequestData();
        $discussion->setName($data["name"]);
        if (User::current_user() == $discussion->getCreator() || User::is_admin())
            $discussion->save();

        if (is_object($discussion))
        {
            echo json_encode($discussion->toArray());
        }
        else
        {
            echo json_encode(array("error"));
        }
    }

    public function unsubscribe($params = array())
    {
        $data = $this->getRequestData();
        $discussion = Discussion::find($data["discussion_id"]);
        $user = User::find($data["user_id"]);
        header("Content-Type: application/json");
        $this->render = false;
        if ($user == User::current_user())
        {
            $discussion->removeParticipant($user);
            $discussion->save();
            echo json_encode(array(
                "status" => "success",
                "message" => "Successfully unsubscribed"
            ));

        }
        else
        {
            echo json_encode(array(
                "status" => "error",
                "message" => "You are not this user"
            ));
        }
    }

    public function unnotify($params = array())
    {
        $this->render = false;
        header("Content-Type: application/json");

        if (User::logged_in())
        {
            $data = $this->getRequestData();
            $discussion = Discussion::find($data["discussion_id"]);
            if (is_object($discussion))
            {
                $discussion->removeNotification(User::current_user());
                $discussion->save();
            }
            echo json_encode(array("status" => "success", "message" => "Successfully removed notification"));
        }
        else
        {
            echo json_encode(array("status" => "error", "message" => "Couldn't remove notification"));
        }

    }

    public function destroy($params = array())
    {
        $this->security_check();
        header("Content-Type: application/json");
        $this->render = false;

        $discussion = Discussion::find($params["id"]);
        if (User::current_user() == $discussion->getCreator() || User::is_admin())
        {


            $participants = $discussion->getParticipants();
            $session_ids = array();
            foreach ($participants as $user)
            {
                $session_ids[] = $user->getSessionId();

            }
            foreach ($discussion->getMessages() as $message)
            {
                $message->delete();
            }

            $discussion->delete();

            foreach ($session_ids as $session_id)
            {
                NodeDiplomat::sendMessage($session_id, array("app" => "k_chat", "status" => "deleted_discussion"));
            }

            echo json_encode(array("status" => "success", "message" => "Discussion successfully deleted"));
        }
        else
        {
            echo json_encode(array("status" => "error", "message" => "The discusssion could not be deleted"));
        }
    }
}

