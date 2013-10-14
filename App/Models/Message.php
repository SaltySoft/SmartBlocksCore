<?php
/**
 * Date: 10/03/2013
 * Time: 15:35
 * This is the model class called Message
 */

/**
 * @Entity @Table(name="k_messages")
 */
class Message extends Model
{
    /**
     * @Id @GeneratedValue(strategy="AUTO") @Column(type="integer")
     */
    public $id;

    /**
     * @Column(type="integer")
     */
    private $date;

    /**
     * @Column(type="text")
     */
    private $content;

    /**
     * @ManyToOne(targetEntity="Discussion", inversedBy="messages")
     */
    private $discussion;

    /**
     * @ManyToOne(targetEntity="User")
     */
    private $sender;

    public function __construct()
    {
        $this->content = "";
        $this->date = time();
        $this->sender = new User();
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

    public function setDiscussion($discussion)
    {
        $this->discussion = $discussion;
    }

    public function getDiscussion()
    {
        return $this->discussion;
    }

    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    public function getSender()
    {
        return $this->sender;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getDate()
    {
        return $this->date;
    }

    protected function before_save()
    {
        $this->content = htmlentities($this->content);
    }


    public function toArray()
    {
        $time = getdate($this->date);
        return array(
            "id" => $this->id,
            "sender" => $this->sender->toArray(0),
            "content" => $this->content,
            "date" => array(
                "raw" =>$this->date,
                "day" =>$time["mday"],
                "month" =>$time["mon"],
                "year" => $time["year"],
                "hour" => $time["hours"] < 10 ? "0".$time["hours"] : $time["hours"],
                "minute" => $time["minutes"] < 10 ? "0".$time["minutes"] : $time["minutes"],
                "second" =>  $time["seconds"] < 10 ? "0".$time["seconds"] : $time["seconds"],
            ),
            "discussion_id" => $this->discussion->getId()
        );
    }
    
}

