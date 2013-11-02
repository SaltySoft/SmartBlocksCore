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
class UserBase extends Model
{
    /**
     * @Column(type="string")
     */
    protected  $name;

    /** @Column(type="string") */
    protected $hash;

    /** @Column(type="string") */
    protected $email;

    /**
     * @Column(type="integer")
     */
    protected $admin = 0;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $session_id;

    /**
     * @Column(type="integer", nullable=true)
     */
    protected $last_activity;

    public function getName()
    {
        return $this->name;
    }

    public function setName($str)
    {
        $this->name = $str;
    }

    public function getMail()
    {
        return $this->email;
    }

    public function setMail($str)
    {
        $this->email = $str;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function setHash($password)
    {
        $this->hash = sha1($password);
    }

    public function setAdmin()
    {
        $this->admin = 1;
    }

    public function setNormal()
    {
        $this->admin = 0;
    }

    public function isValid()
    {
        if ($this->id != "")
        {
            $user = User::find($this->id);
            if ($user->hash == $this->hash)
                return true;
        }
        else
            return false;
    }

    public function isAdmin()
    {
        return $this->admin == true;
    }


    /**
     * Logs the user and sets their session id.
     */
    public function login()
    {
        if ($this->isValid())
        {
            $_SESSION["user_id"] = $this->id;
            $_SESSION["user_logged"] = true;
            $this->setSessionId(MuffinApplication::getSessionId());
            $this->save();
        }
    }

    public function logout()
    {
        $_SESSION["user_id"] = 0;
        unset($_SESSION["user_id"]);
        $_SESSION["user_logged"] = false;
    }

    public static function logged_in()
    {
        return isset($_SESSION["user_id"]) && isset($_SESSION["user_logged"]) && $_SESSION["user_logged"];
    }

    public static function is_admin()
    {
        $current_user = self::current_user();
        return ($current_user != null) && $current_user->isAdmin();
    }

    public static function current_user()
    {
        if (self::logged_in())
            return User::find($_SESSION["user_id"]);
        else
            return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getAdmin()
    {
        return $this->admin;
    }

    public function setSessionId($session_id)
    {
        $this->session_id = $session_id;
    }

    public function getSessionId()
    {
        return $this->session_id;
    }

    public function updateActivity()
    {
        $this->last_activity = time();
        $this->save();
    }
}
