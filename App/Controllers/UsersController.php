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

        if (\User::logged_in())
        {
            $this->json_message("logged");
        }
        else
        {
            $this->json_error("not logged");
        }

    }

    function logout($params = array())
    {
//        echo "ALERT";
//        $user = \User::current_user();
//        if ($user != null)
//        {
//            $user->logout();
//        }
//        if (!\User::logged_in())
//        {
//            $this->json_message("logged out");
//        }
//        else
//        {
//            $this->json_error("still logged");
//        }
    }

    function login_form()
    {
        $this->setLayout("login_layout");
    }

    public function show($params = array())
    {
        $this->render = false;

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
        $data = $this->getRequestData();

        $user = UsersBusiness::createOrUpdate($data);
        if (is_object($user))
        {
            $this->return_json($user->toArray());
        }
        else
        {
            $this->json_error("You cannot subscribe twice.");
        }
    }

    /**
     * This webservice waits for the following information :
     * firstname - first name of the user to update
     * lastname - last name of the user to update
     */
    function update($params = array())
    {
        $data = $this->getRequestData();
        $data["id"] = $params["id"];
        $user = \UsersBusiness::createOrUpdate($data);
        $this->return_json($user->toArray());
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

    public function current_user()
    {
        $this->render = false;
        header('Content-Type: application/json');
        $user = User::current_user();
        if (is_object($user))
        {
            echo json_encode($user->toArray());
        }
        else
        {
            echo json_encode(array("status" => "error", "message" => "Not logged on"));
        }
    }

    public function last_update()
    {
        $this->render = false;
        header('Content-Type: application/json');
        $user = User::current_user();
        if (is_object($user))
        {
            echo json_encode(array("last_update" => $user->getLastUpdated()));
        }
        else
        {
            echo json_encode(array("status" => "error", "message" => "Not logged on"));
        }
    }

    public function unauthorized()
    {
        $this->json_error("401 Unauthorized", 401);
    }
}
