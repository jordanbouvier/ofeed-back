<?php
namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="reactions")
 * @ORM\Entity
 */

class Reaction
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Exclude
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="UserSlack")
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function addUser(UserSlack $user)
    {
        $this->users->add($user);
    }

    public function removeUser(UserSlack $user)
    {
        $this->users->removeElement($user);
    }
    public function getUsers()
    {
        return $this->users;
    }
    public function setUsers($users)
    {
        $this->users = $users;
        return $this;
    }
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Reaction
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }








}