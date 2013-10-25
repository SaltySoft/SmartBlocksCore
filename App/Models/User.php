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

/**
 * @Entity @Table(name="users")
 */
class User extends UserBase
{
    /**
     * @Id @GeneratedValue(strategy="AUTO") @Column(type="integer")
     */
    public $id;

    /**
     * @Column(type="string")
     */
    private $lastname;

    /**
     * @Column(type="string")
     */
    private $firstname;

    /**
     * @Column(type="string")
     */
    private $token;


    /**
     * @Column(type="integer")
     */
    private $last_updated;

    /**
     * @OneToMany(targetEntity="Right", mappedBy="user")
     */
    private $rights;

    /**
     * @Column(type="string")
     */
    private $data;


    public function __construct()
    {
        $this->firstname = "";
        $this->lastname = "";
        $this->token = "";
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->contacts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->rights = new \Doctrine\Common\Collections\ArrayCollection();
        $this->authorized_apps = new \Doctrine\Common\Collections\ArrayCollection();
        $this->last_updated = time();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setLastActivity($last_activity)
    {
        $this->last_activity = $last_activity;
    }

    public function getLastActivity()
    {
        return $this->last_activity;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        $this->last_updated = time();
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function addGroup($group)
    {
        $this->groups[] = $group;
    }

    public function removeGroup($group)
    {
        $this->groups->removeElement($group);
    }

    public function getGroups()
    {
        return $this->groups;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setAuthorizedApps($authorized_apps)
    {
        $this->authorized_apps = $authorized_apps;
    }

    public function getAuthorizedApps()
    {
        return $this->authorized_apps;
    }

    public function setContacts($contacts)
    {

        $this->last_updated = time();
    }

    public function getContacts()
    {

        return $this->contacts;
    }

    public function setContactWithMe($contact_with_me)
    {
        $this->contact_with_me = $contact_with_me;
    }

    public function getContactWithMe()
    {
        return $this->contact_with_me;
    }

    public function setLastUpdated($last_updated)
    {
        $this->last_updated = $last_updated;
    }

    public function getLastUpdated()
    {
        return $this->last_updated;
    }


    public function getRights()
    {
        $return_array = array();
        foreach ($this->rights as $right)
        {
            if (is_object($right))
            {
                $return_array[] = $right->getToken();
            }
            else
            {
                $return_array[] = $right;
            }
        }

        return $return_array;
    }

    public function setRights($rights)
    {
        if (PERSISTANCE == "rdbm")
        {
            $rights_to_add = array();

            foreach ($rights as $right_a)
            {
                $rights = Right::where(array("token" => $right_a));
                if (isset($rights[0]))
                {
                    $rights_to_add[] = $rights[0];
                }
            }

            $rights = new \Doctrine\Common\Collections\ArrayCollection($rights_to_add);
        }

        $this->rights = $rights;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        if (PERSISTANCE == "rdbm")
            $data = json_encode($data);

        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        $data = $this->data;
        if (PERSISTANCE == "rdbm")
            $data = json_decode($data, true);
        return $data;
    }

    public function toArray($load_sub = 1)
    {

        $rights = array("user");

        foreach ($this->getRights() as $right)
        {
            $rights[] = $right;
        }


        $array = array(
            "id" => $this->getId(),
            "firstname" => $this->getFirstname(),
            "lastname" => $this->getLastname(),
            "name" => $this->getName(),
            "email" => $this->getEmail(),
            "session_id" => $this->getSessionId(),
            "last_updated" => $this->last_updated,
            "rights" => $rights
        );

        return $array;
    }

    /**
     * Returns true if the user has the right somewhere, or in
     * the specified group (the group must be a token).
     *
     * @param $right_token
     * @return bool
     */
    public function hasRight($right_token)
    {
        $hasright = false;
        foreach ($this->rights as $right)
        {
            if ($right->getToken() == $right_token)
            {
                $hasright = true;
            }
        }
        return $hasright || $right_token == "user";
    }


    /**
     * @param $required_right
     * @param bool $interface
     *
     * This function restricts data access in controllers.
     */
    public static function restrict($required_right = null, $interface = false)
    {
        $current_user = \User::current_user();
        $continue = false;
        if (is_object($current_user))
        {
            foreach ($current_user->getRights() as $right)
            {
                if ($right->getToken() == $required_right || $required_right == null)
                {
                    $continue = true;
                }
            }
            if ($required_right == null)
            {
                $continue = true;
            }
        }

        if (!$continue)
        {
            if ($interface)
            {
                \Router::redirect("/Users/login_form");
            }
            else
            {
                \Router::redirect("/Users/unauthorized");
            }

        }
    }
}