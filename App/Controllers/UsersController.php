<?php
/**
 * Copyright (C) 2013 Antoine Jackson
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE
 * OR OTHER DEALINGS IN THE SOFTWARE.
 */

class UsersController extends Controller
{
    private function security_check($user = null)
    {
        if (!User::logged_in() || !User::current_user()->is_admin() && User::current_user() != $user)
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

    public function user_error($params = array())
    {
        $this->render = false;
        header("Content-Type: application/json");
        $response = array(
            "message" => "There was an error"
        );
        echo json_encode($response);
    }

    public function index()
    {
        $page = (isset($_GET["page"]) ? $_GET["page"] : 1);
        $page_size = (isset($_GET["page_size"]) ? $_GET["page_size"] : 10);

        $em = Model::getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select("u")
            ->from("User", "u")
            ->setFirstResult(($page - 1) * $page_size)
            ->setMaxResults($page_size);

        if (isset($_GET["filter"]) && $_GET["filter"] != "")
        {
            $qb->andWhere("u.name LIKE :username OR u.email LIKE :email")
                ->setParameter("username", '%' . $_GET["filter"] . '%')
                ->setParameter("email", '%' . $_GET["filter"] . '%');
        }

        $users = $qb->getQuery()->getResult();

        $response = array();

        foreach ($users as $user)
        {
            $response[] = $user->toArray();
        }
        $this->render = false;
        header("Content-Type: application/json");
        echo json_encode($response);

    }

    function login($params = array())
    {

        if (isset($_POST["name"]) && isset($_POST["password"]))
        {
            $users = User::where(array("name" => $_POST["name"], "hash" => sha1($_POST["password"])));
            if (count($users) > 0)
            {
                $user = $users[0];
                $user->login();
            }
        }

        if (isset($_POST["redirect"]))
        {
            $this->redirect($_POST["redirect"]);
        }
        else
        {
            $this->redirect("/");
        }

    }

    function logout($params = array())
    {
        $user = User::current_user();
        if ($user != null)
        {
            $user->logout();
        }
        $this->redirect("/");
    }

    function login_form()
    {
        $this->setLayout("login_layout");
    }

    public function show($params = array())
    {
        $this->render = false;
//        header("Content-Type: application/json");

        $user = User::find($params["id"]);
        if (is_object($user))
        {
            $response = $user->toArray();
        }
        else
        {
            $response = array();
        }

        echo json_encode($response);
    }

    function create($params = array())
    {
//        $users = User::where(array("admin" => 1));
//        if (count($users) > 0)
//        {
//            $this->security_check();
//        }
        $user = new User();
        $data = $this->getRequestData();
        $em = Model::getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select("COUNT(u)")
            ->from("User", "u")
            ->where("u.name = :username")
            ->setParameter("username", $data["name"]);
        $count = $qb->getQuery()->getSingleScalarResult();

        if ($count == 0)
        {
            $user->setName($data["name"]);
            $user->setMail(isset($data["mail"]) ? $data["mail"] : "none");
            $user->setFirstname(isset($data["firstname"]) ? $data["firstname"] : "");
            $user->setLastname(isset($data["lastname"]) ? $data["lastname"] : "");
            $users = User::where(array("admin" => 1));
            if (count($users) == 0)
            {
                $user->setAdmin();
            }
            else
            {
                $user->setNormal();
            }

            $user->setHash($data["password"]);
            $user->save();
            $response = $user->toArray();
        }
        else
        {
            $response = array("message" => "failure");
        }

        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $this->render = false;
            header("Content-Type: application/json");
            echo json_encode($response);
        } else {
            $this->flash("User created");
            $this->redirect("/");
        }
    }


    function add($params = array())
    {

    }

    /**
     * This webservice waits for the following information :
     * firstname - first name of the user to update
     * lastname - last name of the user to update
     */
    function update($params = array())
    {

        $this->render = false;
        header("Content-Type: application/json");
        $user = User::find($params["id"]);
        $this->security_check($user);
        if (is_object($user))
        {
            $data = $this->getRequestData();
            //Direct data update
            $user->setName(isset($data["username"]) ? $data["username"] : $user->getName());
            $user->setFirstname(isset($data["firstname"]) ? $data["firstname"] : $user->getFirstname());
            $user->setLastname(isset($data["lastname"]) ? $data["lastname"] : $user->getLastname());
            //Jobs update
            $user->getJobs()->clear();
            foreach ($data["jobs"] as $job_array)
            {
                $job = Job::find($job_array["id"]);

                if (is_object($job) && !$user->getJobs()->contains($job))
                {
                    $user->addJob($job);
                }
            }
            //Groups update
            $user->getGroups()->clear();
            foreach ($data["groups"] as $group_array)
            {
                $group = Group::find($group_array["id"]);

                if (is_object($group) && !$user->getGroups()->contains($group))
                {
                    $user->addGroup($group);
                }
            }

            if (isset($data["contacts"])) {
                $user->getContacts()->clear();
                foreach ($data["contacts"] as $contact_array)
                {
                    $contact = User::find($contact_array["id"]);
                    if (is_object($contact) && !$user->getContacts()->contains($contact))
                    {
                        $user->getContacts()->add($contact);
                    }
                }
            }
            $user->setLastUpdated(time());
            //Saving data to db
            $user->save();
            $response = $user->toArray();
            echo json_encode($response);
        }
        else
        {
//            $this->redirect("/Users/user_error");
        }
    }

    public function destroy($params = array())
    {
        $this->render = false;
        header("Content-Type: application/json");
        $this->security_check();
        $user = User::find($params["id"]);
        if (is_object($user))
        {
            $user->delete();
            echo json_encode(array("message" => "success"));
        }
        else
        {
            echo json_encode(array("message" => "failure"));
        }
    }

    /**
     * Adds a job to the given user
     * GET/POST request
     */
    public function add_job($params = array())
    {
        $this->security_check();
        $this->render = false;
        header("Content-Type: application/json");

        $data = $this->getRequestData();

        $user = User::find($data["user_id"]);
        $job = Job::find($data["job_id"]);

        if (is_object($user) && is_object($job))
        {
            $user->addJob($job);
            $user->save();
            echo json_encode(array(
                "status" => "success"
            ));
        }
        else
        {
            echo json_encode(array(
                "status" => "error"
            ));
        }
    }

    public function current_user()
    {
        $this->render = false;
        header('Content-Type: application/json');
        $user = User::current_user();
        if (is_object($user)) {
            echo json_encode($user->toArray());
        } else {
            echo json_encode(array("status" => "error", "message" => "Not logged on"));
        }
    }

    public function last_update()
    {
        $this->render = false;
        header('Content-Type: application/json');
        $user = User::current_user();
        if (is_object($user)) {
            echo json_encode(array("last_update" => $user->getLastUpdated()));
        } else {
            echo json_encode(array("status" => "error", "message" => "Not logged on"));
        }
    }

    /**
     * This action is an admin page to access user management javascript apps.
     * @param array $params
     */
    public function user_management($params = array())
    {
        $this->interface_security_check();
        $this->set("app", "Apps/UserManagement/app");
    }

    public function socials()
    {
        if (!User::logged_in())
        {
            $this->redirect("/");
        }
        $this->set("app", "Apps/Socials/app");
    }

    public function connect($params = array())
    {
        $this->render = false;
        header("application/json");
        $users = User::where(array("name" => $params["username"], "hash" => $params["password"]));
        if (count($users) > 0)
        {
            $user = $users[0];
            $token = sha1(microtime());
            $user->setToken($token);
            $user->save();
            $session = $user->getSessionId();

            if ($session == null)
            {
                $session = md5(microtime() . rand());
                $user->setSessionId($session);
                $user->save();

                $array = array("session_id" => $session, "token" => $token);

                echo json_encode($array);
            }
            else
            {
                $array = array("session_id" => $user->getSessionId(), "token" => $token);

                echo json_encode($array);
            }
        }
    }
}
