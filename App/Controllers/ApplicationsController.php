<?php
/**
 * Created by Antoine Jackson
 * User: Antoine Jackson
 * Date: 9/18/13
 * Time: 3:06 AM
 */

 class ApplicationsController extends \Controller
{
    public function index()
    {
        $em = Model::getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select("app")->from('\Application', 'app');

        $results = $qb->getQuery()->getResult();

        $response = array();

        foreach ($results as $result)
        {
            $response[] = $result->toArray();
        }

        $this->return_json($response);
    }
}