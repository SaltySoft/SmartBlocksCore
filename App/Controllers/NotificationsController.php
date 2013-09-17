<?php
/**
 * Author: Antoine Jackson a.j.william26@gmail.com
 */

class NotificationsController extends Controller
{
    private function security_check()
    {
        if (!\User::logged_in())
        {
            $this->redirect("/");
        }
    }

    private function return_json($response)
    {
        header("Content-Type: application/json");
        $this->render = false;
        echo json_encode($response);
    }

    private function json_error($message)
    {
        $this->return_json(array("success" => false, "error" => true, "message" => $message));
    }

    private function json_message($message)
    {
        $this->return_json(array("success" => true, "error" => false, "message" => $message));
    }


    public function index($params = array())
    {
        $this->security_check();

        $em = \Model::getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select("n")
            ->from("\\Notification", "n")
            ->where("n.user = :user")
            ->setParameter("user", \User::current_user());

        $results = $qb->getQuery()->getResult();

        $response = array();

        foreach ($results as $notification)
        {
            $response[] = $notification->toArray();
        }

        $this->return_json($response);
    }

    public function show($params = array())
    {
        $this->security_check();

        $notification = Notification::find($params["id"]);
        if (is_object($notification))
        {
            $this->return_json($notification->toArray());
        }
        else
        {
            $this->json_error("The notification was not found");
        }
    }

    public function create($params = array())
    {
        $this->security_check();
        $data = $this->getRequestData();

        $notification = new Notification();
        $notification->setContent(isset($data["content"]) ? $data["content"] : "");
        $notification->setLink(isset($data["link"]) ? $data["link"] : "javascript:void(0);");
        $user = \User::find($data["user"]["id"]);
        $notification->setUser($user);
        $notification->save();

        $this->return_json($notification->toArray());
    }

    public function update($params = array())
    {
        $this->security_check();

        $data = $this->getRequestData();

        $notif = Notification::find($data["id"]);
        if (is_object($notif))
        {
            $notif->setContent($data["content"]);
            $notif->setLink($data["link"]);
            $user = \User::find($data["user"]["id"]);
            $notif->setUser($user);
            $notif->setSeen($data["seen"]);
            $notif->save();
            $this->return_json($notif->toArray());
        }
        else
        {
            $this->json_error("The notification was not found");
        }
    }

    public function destroy($params = array())
    {
        $this->security_check();

        $notif = Notification::find($params["id"]);
        if (is_object($notif))
        {
            if ($notif->getUser() == \User::current_user())
            {
                $notif->delete();
                $this->json_message("Notification successfully deleted");
            }
            else
            {
                $this->json_error("You don't have the rights to delete this notification");
            }
        }
        else
        {
            $this->json_error("This notification was not found");
        }
    }
}