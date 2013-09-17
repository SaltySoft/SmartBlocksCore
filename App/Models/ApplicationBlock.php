<?php
/**
 * Date: 15/03/2013
 * Time: 16:28
 * This is the model class called ApplicationBlock
 */

/**
 * @Entity @Table(name="applicationblocks")
 */
class ApplicationBlock extends Model
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
    private $logo_url;

    /**
     * @Column(type="string", nullable = true)
     */
    private $color;

    /**
     * @OneToMany(targetEntity="Application", mappedBy="block")
     */
    private $applications;

    public function __construct()
    {
        $this->name = "";
        $this->token = "";
        $this->description = "";
    }

    public function addApplication($application)
    {
        $this->applications[] = $application;
    }

    public function removeApplication($application)
    {
        $this->applications->removeElement($application);
    }

    public function getApplications()
    {
        return $this->applications;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getId()
    {
        return $this->id;
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

    public function setColor($color)
    {
        $this->color = $color;
    }

    public function getColor()
    {
        return $this->color;
    }


    public function toArray()
    {
        $appBlockArray = array();
        $appBlockArray["name"] = $this->name;
        $appBlockArray["token"] = $this->token;
        $appBlockArray["description"] = $this->description;
        $appBlockArray["logo_url"] = $this->logo_url;
        $appBlockArray["color"] = $this->color;
        $appsArray = array();

        foreach ($this->getApplications() as $app)
        {
            if (!$app->getAdminApp() || User::is_admin())
                $appsArray[] = $app->toArray();
        }
        $appBlockArray["apps"] = $appsArray;

        return $appBlockArray;
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

