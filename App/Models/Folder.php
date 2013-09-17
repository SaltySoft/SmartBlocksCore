<?php
/**
 * Date: 12/03/2013
 * Time: 11:27
 * This is the model class called Folder
 */

/**
 * @Entity @Table(name="folders")
 */
class Folder extends Model
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
     * @Column(type="integer", nullable=true)
     */
    private $parent_folder;

    /**
     * @OneToMany(targetEntity="Folder", mappedBy="parent")
     */
    private $children;

    /**
     * @ManyToOne(targetEntity="Folder")
     */
    private $parent;

    /**
     * @ManyToOne(targetEntity="User")
     */
    private $creator;

    /**
     * @ManyToMany(targetEntity="User")
     */
    private $users_allowed;

    /**
     * @ManyToMany(targetEntity="Group")
     */
    private $groups_allowed;

    /**
     * @OneToMany(targetEntity="File", mappedBy="parent_folder")
     */
    private $files;

    public function __construct()
    {

        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users_allowed = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groups_allowed = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function setCreator($creator)
    {
        $this->creator = $creator;
    }

    public function getCreator()
    {
        return $this->creator;
    }

    public function setFiles($files)
    {
        $this->files = $files;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function setGroupsAllowed($groups_allowed)
    {
        $this->groups_allowed = $groups_allowed;
    }

    public function getGroupsAllowed()
    {
        return $this->groups_allowed;
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

    public function setUsersAllowed($users_allowed)
    {
        $this->users_allowed = $users_allowed;
    }

    public function getUsersAllowed()
    {
        return $this->users_allowed;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function toArray($depth = -1)
    {
        $files = array();
        if ($depth > 0)
        {
            foreach ($this->files as $file)
            {
                $files[] = $file->toArray();
            }
        }

        $users = array();
        foreach ($this->users_allowed as $user)
        {
            $users[] = $user->toArray();
        }

        $groups = array();
        foreach ($this->groups_allowed as $group)
        {
            $groups[] = $group->toArray();
        }

        $folders = array();
        if ($depth >= 0)
        {
            $em = Model::getEntityManager();
            $qb = $em->createQueryBuilder();
            $qb->select("f")
                ->from("Folder", "f")
                ->Where("f.parent_folder = :p")
                ->setParameter("p", $this->id);

            $folders_result = $qb->getQuery()->getResult();

            foreach($folders_result as $folder)
                $folders[] = $folder->toArray($depth - 1);

        }

        return array(
            "id" => $this->id,
            "name" => $this->name,
            "parent_folder" => $this->parent != null ? $this->parent->getId() : 0,
            "creator" => $this->creator != null ? $this->creator->toArray() : null,
            "users_allowed" => $users,
            "groups_allowed" => $groups,
            "folders" => $folders,
            "files" => $files
        );
    }

    public function addUser($user)
    {
        $this->users_allowed[] = $user;
    }

    public function addGroup($group)
    {
        $this->groups_allowed[] = $group;
    }

    public function addFile($file)
    {
        $this->files[] = $file;
    }
}

