<?php

class ContactRequestsController extends \Controller
{
    private function security_check()
    {
        return \User::logged_in();
    }


    public function index()
    {
        $em = \Model::getEntityManager();

        $qb = $em->createQueryBuilder();

        $qb->select("cr")
            ->from("ContactRequest", "cr")
            ->where("cr.target = :user")
            ->setParameter("user", \User::current_user());

        $results = $qb->getQuery()->getResult();
        $response = array();

        foreach ($results as $cr)
        {
            $response[] = $cr->toArray();
        }
        $this->return_json($response);
    }

    public function show($params = array())
    {
        $contact_request = \ContactRequest::find($params["id"]);

        if (is_object($contact_request))
        {
            $this->return_json($contact_request);
        }
        else
        {
            $this->json_error("The contact request does not exist");
        }
    }

    public function create()
    {
        $data = $this->getRequestData();

        $contact_request = new ContactRequest();

        $sender = User::find($data["sender"]["id"]);
        $target = User::find($data["target"]["id"]);


        if (is_object($sender) && is_object($target))
        {
            $em = \Model::getEntityManager();

            $qb = $em->createQueryBuilder();

            $qb->select("COUNT(cr)")
                ->from("ContactRequest", "cr")
                ->where("(cr.target = :target AND cr.sender = :sender) OR (cr.sender = :target AND cr.sender = :target)")
                ->setParameter("sender", $sender)
                ->setParameter("target", $target);

            if ($qb->getQuery()->getSingleScalarResult() == 0)
            {
                $contact_request->setSender($sender);
                $contact_request->setTarget($target);

                $contact_request->save();

                $this->return_json($contact_request);
            }
            else
            {
                $this->json_error("A request between you two has already been issued.");
            }
        }
        else
        {
            $this->json_error("One or more users were not found");
        }

    }

    public function update($params = array())
    {
        $contact_request = \ContactRequest::find($params["id"]);
        $data = $this->getRequestData();

        $sender = User::find($data["sender"]["id"]);
        $target = User::find($data["target"]["id"]);

        if (is_object($contact_request))
        {

            if (is_object($sender) && is_object($target))
            {
                $contact_request->setSender($sender);
                $contact_request->setTarget($target);

                $contact_request->save();

                $this->return_json($contact_request);
            }
            else
            {
                $this->json_error("One or more users were not found");
            }
        }
        else
        {
            $this->json_error("The contact request does not exist");
        }
    }

    public function destroy($params = array())
    {
        $contact_request = \ContactRequest::find($params["id"]);
        if (is_object($contact_request))
        {
            $contact_request->delete();
            $this->json_message("The contact request was successfully deleted");
        }
        else
        {
            $this->json_error("One or more users were not found");
        }
    }


}