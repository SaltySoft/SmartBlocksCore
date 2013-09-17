<?php
/**
 * Date: 12/03/2013
 * Time: 11:28
 * This is the model class called File
 */

/**
 * @Entity @Table(name="files")
 */
class File extends Model
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
    private $path;

    /**
     * @ManyToOne(targetEntity="User")
     */
    private $owner;

    /**
     * @ManyToOne(targetEntity="File", inversedBy="subfiles")
     */
    private $parent_folder;

    /**
     * @OneToMany(targetEntity="File", mappedBy="parent_folder")
     */
    private $subfiles;

    /**
     * @Column(type="boolean")
     */
    private $is_folder;

    /**
     * @ManyToMany(targetEntity="User")
     */
    private $users_allowed;

    private $address;

    public function __construct()
    {
        $this->subfiles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->is_folder = false;
        $this->users_allowed = new \Doctrine\Common\Collections\ArrayCollection();
        $this->address = null;
    }

    public function setId($id)
    {
        $this->id = $id;
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

    public function setParentFolder($parent_folder)
    {
        $this->parent_folder = $parent_folder;
    }

    public function getParentFolder()
    {
        return $this->parent_folder;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function getSubfiles()
    {
        return $this->subfiles;
    }

    public function addSubfile($subfile)
    {
        $this->subfiles[] = $subfile;
    }

    public function removeSubfile($subfile)
    {
        $this->subfiles->removeElement($subfile);
    }

    public function isFolder()
    {
        return $this->is_folder;
    }

    public function setAsFolder($is_folder)
    {
        return $this->is_folder = $is_folder;
    }

    public function getUsersAllowed()
    {
        return $this->users_allowed;
    }

    public function addAllowedUser($user)
    {
        $this->users_allowed[] = $user;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function getAddress()
    {
        if ($this->address == null)
        {
            $address = $this->name;
            $parent = $this->parent_folder;
            while (is_object($parent))
            {
                $address = $parent->getName() . "/" . $address;
                $parent = $parent->getParentFolder();
            }
            return $address;
        }
        else
        {
            return $this->address;
        }

    }

    public function getType()
    {
        $type = "unknown";
        if (file_exists(ROOT . DS . "Data" . DS . "User_files" . DS . $this->path))
        {
            if (!$this->is_folder)
            {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $type = finfo_file($finfo, ROOT . DS . "Data" . DS . "User_files" . DS . $this->path);
                finfo_close($finfo);
            }
            else
                $type = "folder";
        }
        return $type;
    }

    public static function getHome()
    {
        $file = new File();
        $file->id = 0;
        $file->setOwner(\User::current_user());
        $file->setAsFolder(true);
        $file->setName("Home");
        //getting user subfiles
        $em = Model::getEntityManager();
        $qb = $em->createQuerybuilder();
        $qb->select("f")
            ->from("File", "f")
            ->andWhere("f.parent_folder is NULL")
            ->andWhere("f.owner = :user")
            ->setParameter("user", \User::current_user());
        $results = $qb->getQuery()->getResult();
        foreach ($results as $result)
        {
            $file->addSubfile($result);
        }
        return $file;
    }

    public function toArray($reach_subfiles = false)
    {
        $subfiles = array();
        if ($reach_subfiles)
        {
            if ($this->is_folder)
            {
                foreach ($this->subfiles as $subfile)
                {
                    if ($subfile->getId() != $this->getId())
                        $subfiles[] = $subfile->toArray();
                }
            }
        }

        $users_allowed = array();

        foreach ($this->users_allowed as $user)
        {
            $users_allowed[] = $user->toArray();
        }


        $type = "unknown";
        if (!$this->is_folder)
        {
            if (file_exists(ROOT . DS . "Data" . DS . "User_files" . DS . $this->path))
            {

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $type = finfo_file($finfo, ROOT . DS . "Data" . DS . "User_files" . DS . $this->path);
                finfo_close($finfo);

            }
        }
        else
            $type = "folder";


        return array(
            "id" => $this->id,
            "name" => $this->name,
            "parent_folder" => $this->parent_folder != null ? $this->parent_folder->toArray() : self::getHome(),
            "owner" => $this->getOwner() != null ? $this->getOwner()->toArray() : null,
            "is_folder" => $this->is_folder,
            "subfiles" => $subfiles,
            "users_allowed" => $users_allowed,
            "address" => "/" . $this->getAddress(),
            "type" => $type
        );
    }
}