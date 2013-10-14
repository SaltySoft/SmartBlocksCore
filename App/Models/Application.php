<?php
/**
 * Date: 15/03/2013
 * Time: 16:27
 * This is the model class called Application
 */

/**
 * @Entity @Table(name="applications")
 */
class Application extends Model
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
     * @Column(type="string")
     */
    private $token;

    /**
     * @Column(type="text")
     */
    private $description;

    /**
     * @Column(type="string", nullable = true)
     */
    private $link;

    /**
     * @Column(type="string", nullable = true)
     */
    private $logo_url;

    /**
     * @ManyToOne(targetEntity="ApplicationBlock", inversedBy="applications")
     */
    private $block;

    /**
     * @Column(type="boolean")
     */
    private $admin_app;

    /**
     * @Column(type="string", nullable=true)
     */
    private $entry_point;

    /**
     * @Column(type="string", nullable=true)
     */
    private $style;

    public function __construct()
    {
        $this->name = "";
        $this->token = "";
        $this->description = "";
    }

    public function getId()
    {
        return $this->id;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setBlock($block)
    {
        $this->block = $block;
    }

    public function getBlock()
    {
        return $this->block;
    }

    public function setEntryPoint($entry_point)
    {
        $this->entry_point = $entry_point;
    }

    public function getEntryPoint()
    {
        return $this->entry_point;
    }

    public function setStyle($style)
    {
        $this->style = $style;
    }

    public function getStyle()
    {
        return $this->style;
    }



    public function toArray()
    {
        $appArray = array();
        $appArray["id"] = $this->id;
        $appArray["name"] = $this->name;
        $appArray["token"] = $this->token;
        $appArray["description"] = $this->description;
        $appArray["link"] = $this->link;
        $appArray["logo_url"] = $this->logo_url;
        $appArray["admin"] = $this->getAdminApp();
        $appArray["entry_point"] = $this->getEntryPoint();
        $appArray["style"] = $this->getStyle();

        return $appArray;
    }



    public function setAdminApp($admin_app)
    {
        $this->admin_app = $admin_app;
    }

    public function getAdminApp()
    {
        return $this->admin_app;
    }

    public function setLogoUrl($logo_url)
    {
        $this->logo_url = $logo_url;
    }

    public function getLogoUrl()
    {
        return $this->logo_url;
    }


}

