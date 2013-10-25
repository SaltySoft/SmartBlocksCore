<?php

/**
 * @Entity @Table(name="contact_requests")
 */
class ContactRequest extends \Model
{
    /**
     * @Id @GeneratedValue(strategy="AUTO") @Column(type="integer")
     */
    public $id;

    /**
     * @Column(type="datetime")
     */
    private $date;

    /**
     * @ManyToOne(targetEntity="User")
     */
    private $sender;

    /**
     * @ManyToOne(targetEntity="User")
     */
    private $target;

    public function __construct()
    {
        $this->date = new DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setTarget($target)
    {
        $this->target = $target;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    public function getSender()
    {
        return $this->sender;
    }

    public function toArray()
    {
        $array = array(
            "id" => $this->id,
            "sender" => $this->sender->toArray(0),
            "target" => $this->sender->toArray(0),
            "date" => $this->date->getTimeStamp()
        );

        return $array;
    }
}