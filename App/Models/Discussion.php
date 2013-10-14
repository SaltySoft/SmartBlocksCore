<?php
/**
 * Date: 10/03/2013
 * Time: 15:34
 * This is the model class called Discussion
 */

/**
 * @Entity @Table(name="k_discussions")
 */
class Discussion extends Model
{
    /**
     * @Id @GeneratedValue(strategy="AUTO") @Column(type="integer")
     */
    public $id;

    /**
     * @Column(type="string")
     */
    private $name;

    /**
     * @Column(type="integer")
     */
    private $creation_date;

    /**
     * @Column(type="integer")
     */
    private $last_updated;

    /**
     * @OneToMany(targetEntity="Message", mappedBy="discussion")
     */
    private $messages;

    /**
     * @ManyToOne(targetEntity="User")
     */
    private $creator;

    /**
     * @ManyToMany(targetEntity="User")
     */
    private $participants;

    /**
     * @ManyToMany(targetEntity="User")
     * @JoinTable(name="discussion_notifications",
     *      joinColumns={@JoinColumn(name="discussion_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
     */
    private $notifications;

    public function __construct()
    {
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->name = "Conversation";
        $this->creation_date = time();
        $this->last_updated = time();
        $this->notifications = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setCreationDate($creation_date)
    {
        $this->creation_date = $creation_date;
    }

    public function getCreationDate()
    {
        return $this->creation_date;
    }

    public function setCreator($creator)
    {
        $this->creator = $creator;
    }

    public function getCreator()
    {
        return $this->creator;
    }

    public function setLastUpdated($last_updated)
    {
        $this->last_updated = $last_updated;
    }

    public function getLastUpdated()
    {
        return $this->last_updated;
    }

    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function addParticipant($participant)
    {
        $this->participants[] = $participant;
    }

    public function removeParticipant($participant)
    {
        $this->participants->removeElement($participant);
    }

    public function getParticipants()
    {
        return $this->participants;
    }

    public function addNotification($user)
    {
        if (!$this->notifications->contains($user))
            $this->notifications[] = $user;
    }

    public function removeNotification($user)
    {
        if ($this->notifications->contains($user))
            $this->notifications->removeElement($user);
    }

    public function getNotifications()
    {
        return $this->notifications;
    }


    public function toArray($load_sub = 0)
    {
        $messages = array();

        foreach ($this->messages as $message)
        {
            $messages[] = $message->toArray();
        }

        $participants = array();

        foreach ($this->participants as $participant)
        {
            $participants[] = $participant->toArray(0);
        }

        $notify = false;

        if ($this->notifications->contains(User::current_user()))
        {
            $notify = true;
        }


        $array = array(
            "id" => $this->id,
            "name" => $this->name,
            "participants" => $participants,
            "messages_count" => count($messages),
            "creator" => ($this->creator != null) ? $this->creator->toArray(0) : null,
            "notify" => $notify
        );

        if ($load_sub == 1)
        {
            $array["messages"] = $messages;
        }
        return $array;
    }


}

