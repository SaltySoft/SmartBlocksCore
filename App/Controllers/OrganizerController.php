<?php
/**
 * Date: 09/03/2013
 * Time: 17:23
 * This is the model class called Organizer
 */

class OrganizerController extends Controller
{
    public function security_check()
    {
        if (!User::logged_in())
        {
            $this->redirect("/Users/login_form");
        }
    }

    public function organizer_error($params = array())
    {
        $this->security_check();
        $this->render = false;
        header("Content-Type: application/json");
        $response = array(
            "message" => "There was an error."
        );
        echo json_encode($response);
    }

    /**
     * This action is an admin page to access app organizer javascript apps.
     * @param array $params
     */
    public function app_organizer($params = array())
    {
        $this->security_check();
//        $this->interface_security_check();
        $this->set("app", "Apps/AppOrganizer/app");
    }
}

