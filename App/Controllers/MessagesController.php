<?php
/**
 * Date: 10/03/2013
 * Time: 16:19
 * This is the model class called Message
 */

class MessagesController extends Controller
{
    public function security_check()
    {


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
        $em = Model::getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select("m")
            ->from("Message", "m");

        if (isset($_GET["page"]))
        {
            $page = (isset($_GET["page"]) ? $_GET["page"] : 1);
            $page_size = (isset($_GET["page_size"]) ? $_GET["page_size"] : 10);
            $qb->setFirstResult(($page - 1) * $page_size)
                ->setMaxResults($page_size);
        }


        if (isset($_GET["filter"]) && $_GET["filter"] != "")
        {
            $qb->andWhere("m.name LIKE :name")
                ->setParameter("name", '%' . mysql_real_escape_string($_GET["filter"]) . '%');
        }

        $messages = $qb->getQuery()->getResult();

        $response = array();

        foreach ($messages as $message)
        {
            $response[] = $message->toArray();
        }
        $this->render = false;
        header("Content-Type: application/json");
        echo json_encode($response);
    }


    public function show($params = array())
    {
        header("Content-Type: application/json");
        $this->render = false;

        $message = Message::find($params["id"]);

        if (is_object($message))
        {
            echo json_encode($message->toArray());
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

        $message = new Message();
        $data = $this->getRequestData();
        $message->setContent(isset($data["content"]) ? $data["content"] : "");
        $message->setDate(time());
        $message->setSender(User::current_user());
        if (isset($data["discussion_id"]))
        {
            $discussion = Discussion::find($data["discussion_id"]);
            if (is_object($discussion))
            {

                foreach ($discussion->getParticipants() as $user)
                {
                    if ($user != User::current_user())
                    {
                        $discussion->addNotification($user);
                    }
                    NodeDiplomat::sendMessage($user->getSessionId(), array("app" => "k_chat", "status" => "new_message", "sender" => User::current_user()->toArray(), "discussion" => $discussion->toArray()));
                }

                $message->setDiscussion($discussion);


                $message->save();
            }
        }
        echo json_encode($message->toArray());

    }

    public function update($params = array())
    {
        $this->security_check();
        header("Content-Type: application/json");
        $this->render = false;

        $discussion = Discussion::find($params["id"]);
        $data = $this->getRequestData();
        $discussion->setName($data["name"]);
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

    public function destroy($params = array())
    {
        $this->security_check();
        header("Content-Type: application/json");
        $this->render = false;

        $discussion = Discussion::find($params["id"]);
        $discussion->delete();


        echo json_encode(array("message" => "Discussion successfully deleted"));

    }
}

