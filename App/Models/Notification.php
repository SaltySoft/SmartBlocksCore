<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Antoine
 * Date: 5/19/13
 * Time: 8:19 PM
 * To change this template use File | Settings | File Templates.
 */

/**
 * @Entity @Table(name="notifications")
 */
class Notification extends Model
{
    /**
     * @Id @GeneratedValue(strategy="AUTO") @Column(type="integer")
     */
    public $id;

    /**
     * @ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * @Column(type="string")
     */
    private $content;

    /**
     * @Column(type="string")
     */
    private $link;

    /**
     * @Column(type="boolean")
     */
    private $seen;

    /**
     * @Column(type="integer")
     */
    private $timestamp;

    public function __construct()
    {
        $this->content = "";
        $this->seen = false;
        $this->timestamp = time();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setSeen($seen)
    {
        $this->seen = $seen;
    }

    public function getSeen()
    {
        return $this->seen;
    }

    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    protected function after_save()
    {
        if (is_object($this->user))
        {
            \NodeDiplomat::sendMessage($this->user->getSessionId(), array(
                "app" => "notifications",
                "notification" => $this->toArray()
            ));
        }
    }

    public function toArray()
    {
        $array = array(
            "id" => $this->id,
            "content" => $this->content,
            "link" => $this->link,
            "user" => $this->user != null ? $this->user->toArray() : null,
            "seen" => $this->seen,
            "timestamp" => $this->timestamp * 1000
        );

        return $array;
    }
}