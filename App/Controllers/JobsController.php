<?php
/**
 * Date: 08/03/2013
 * Time: 12:33
 * This is the model class called Job
 */

class JobsController extends Controller
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
        $qb->select("j")
            ->from("Job", "j");

        if (isset($_GET["page"])) {
            $page = (isset($_GET["page"]) ? $_GET["page"] : 1);
            $page_size = (isset($_GET["page_size"]) ? $_GET["page_size"] : 10);
            $qb->setFirstResult(($page - 1) * $page_size)
                ->setMaxResults($page_size);
        }


        if (isset($_GET["filter"]) && $_GET["filter"] != "") {
            $qb->andWhere("j.name LIKE :name")
                ->setParameter("name", '%' . mysql_real_escape_string($_GET["filter"]) . '%');
        }

        $jobs = $qb->getQuery()->getResult();

        $response = array();

        foreach ($jobs as $job) {
            $response[] = $job->toArray();
        }
        $this->render = false;
        header("Content-Type: application/json");
        echo json_encode($response);
    }

    public function show($params = array())
    {
        header("Content-Type: application/json");
        $this->render = false;

        $job = Job::find($params["id"]);

        if (is_object($job)) {
            echo json_encode($job->toArray());
        } else {
            echo json_encode(array("error"));
        }
    }

    public function create()
    {
        $this->security_check();
        header("Content-Type: application/json");
        $this->render = false;

        $job = new Job();
        $data = $this->getRequestData();
        $job->setToken($this->tokenize($data["name"]));
        $job->setName($data["name"]);

        $job->save();
        echo json_encode($job->toArray());

    }

    public function update($params = array())
    {
        $this->security_check();
        header("Content-Type: application/json");
        $this->render = false;

        $job = Job::find($params["id"]);
        $data = $this->getRequestData();
        $job->setToken($this->tokenize($data["name"]));
        $job->setName($data["name"]);
        $job->save();

        if (is_object($job)) {
            echo json_encode($job->toArray());
        } else {
            echo json_encode(array("error"));
        }
    }

    public function destroy($params = array())
    {
        $this->security_check();
        header("Content-Type: application/json");
        $this->render = false;

        $job = Job::find($params["id"]);
        $job->delete();

        echo json_encode(array("message" => "Job successfully deleted"));
    }
}