<?php
/**
 * Date: 09/03/2013
 * Time: 14:22
 * This is the model class called Group
 */

class GroupsController extends Controller
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
        $qb->select("g")
            ->from("Group", "g");

        if (isset($_GET["page"])) {
            $page = (isset($_GET["page"]) ? $_GET["page"] : 1);
            $page_size = (isset($_GET["page_size"]) ? $_GET["page_size"] : 10);
            $qb->setFirstResult(($page - 1) * $page_size)
                ->setMaxResults($page_size);
        }


        if (isset($_GET["filter"]) && $_GET["filter"] != "") {
            $qb->andWhere("g.name LIKE :name")
                ->setParameter("name", '%' . mysql_real_escape_string($_GET["filter"]) . '%');
        }

        $groups = $qb->getQuery()->getResult();

        $response = array();

        foreach ($groups as $group) {
            $response[] = $group->toArray();
        }
        $this->render = false;
        header("Content-Type: application/json");
        echo json_encode($response);
    }


    public function show($params = array())
    {
        header("Content-Type: application/json");
        $this->render = false;

        $group = Group::find($params["id"]);

        if (is_object($group)) {
            echo json_encode($group->toArray());
        } else {
            echo json_encode(array("error"));
        }
    }

    public function create()
    {
        $this->security_check();
        header("Content-Type: application/json");
        $this->render = false;

        $group = new Group();
        $data = $this->getRequestData();
        $group->setToken($this->tokenize($data["name"]));
        $group->setName($data["name"]);

        $group->save();
        echo json_encode($group->toArray());
    }

    public function update($params = array())
    {
        $this->security_check();
        header("Content-Type: application/json");
        $this->render = false;

        $group = Group::find($params["id"]);

        if (is_object($group)) {
            $data = $this->getRequestData();
            $group->setToken($this->tokenize($data["name"]));
            $group->setName($data["name"]);
            $group->save();
            echo json_encode($group->toArray());
        } else {
            echo json_encode(array("error"));
        }
    }

    public function destroy($params = array())
    {
        $this->security_check();
        header("Content-Type: application/json");
        $this->render = false;

        $group = Group::find($params["id"]);
        $group->delete();

        echo json_encode(array("message" => "Group successfully deleted"));
    }
}

