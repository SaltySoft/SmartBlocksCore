<?php
/**
 * Created by Antoine Jackson
 * User: Antoine Jackson
 * Date: 6/17/13
 * Time: 11:14 PM
 */

/**
 * @Entity @Table(name="api_keys")
 */
class ApiKey extends \Model
{
    /**
     * @Id @GeneratedValue(strategy="AUTO") @Column(type="integer")
     */
    public $id;

    /**
     * @Column(type="string")
     */
    private $api_name;

    /**
     * @ManyToOne(targetEntity="\User")
     */
    private $user;

    /**
     * @Column(type="string")
     */
    private $token;

    /**
     * @Column(type="bigint")
     */
    private $last_sync;

    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function setApiName($api_name)
    {
        $this->api_name = $api_name;
    }

    public function getApiName()
    {
        return $this->api_name;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setLastSync($last_sync)
    {
        $this->last_sync = $last_sync;
    }

    public function getLastSync()
    {
        return $this->last_sync;
    }
}