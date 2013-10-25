<?php
/**
 * Created by Antoine Jackson
 * User: Antoine Jackson
 * Date: 10/25/13
 * Time: 6:53 PM
 */

class UsersBusiness
{
    public static function createOrUpdate($data)
    {
        $exists = false;
        if (isset($data["id"]))
        {
            $user = \User::find($data["id"]);
            $exists = is_object($user);
            unset($data["id"]);



        }

        if (!$exists)
        {
            $users = \User::where(array("email" => $data["email"]));

            if (isset($users[0])) {
                echo $data["email"];
                return null;
            }

            $user = new \User();
            $all_users = \User::all();
            if (!isset($all_users[0])) {
                $user->setAdmin(true);
            }
        }

        $user->setName($data["name"]);
        unset($data["name"]);

        if (isset($data["admin"]))
        {
            $user->setAdmin($data["admin"]);
            unset($data["admin"]);
        }


        $user->setEmail($data["email"]);
        unset($data["email"]);

        $user->setFirstname($data["firstname"]);
        unset($data["firstname"]);

        $user->setLastname($data["lastname"]);
        unset($data["lastname"]);

        if (isset($data["password"]))
        {
            $user->setHash($data["password"]);
            unset($data["password"]);
        }

        if (isset($data["rights"]))
        {
            if (is_array($data["rights"]))
            {
                $user->setRights($data["rights"]);
            }
            unset($data["rights"]);
        }

        $user_data = $user->getData();

        if (is_array($data))
        {
            foreach ($data as $key => $d)
            {
                $user_data[$key] = $d;
            }

        }

        foreach ($user_data as $key => $d)
        {
            if (!isset($data[$key])) {
                unset($user_data[$key]);
            }
        }
        $user->setData($user_data);

        $user->save();
        return $user;
    }
} 