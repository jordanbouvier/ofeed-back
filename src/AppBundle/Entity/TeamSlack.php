<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
/**
 * @ORM\Table(name="teams_slack")
 * @ORM\Entity
 */
class TeamSlack
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Exclude
     */
    private $id;

    /**
     * @ORM\Column(name="id_slack", type="string", length=15)
     * @JMS\Groups({"team_list", "defaultState", "details", "channelList"})
     */
    private $idSlack;

    /**
     * @ORM\Column(name="name", type="string", length=191, unique=true)
     * @JMS\Groups({"team_list", "defaultState", "details", "channelList", "search_result"})
     */
    private $name;

    /**
     * @ORM\Column(name="domain", type="string", length=191)
     * @JMS\Groups({"team_list"})
     */
    private $domain;

    /**
     * @ORM\Column(name="email_domain", type="string",length=191)
     * @JMS\Groups({"team_list"})
     */
    private $emailDomain;

    /**
     * @ORM\Column(name="icon", type="string", length=191)
     * @JMS\Groups({"team_list"})
     */
    private $icon;

    /**
     * @ORM\Column(name="token", type="string", length=191)
     * @JMS\Exclude
     */
    private $token;

    /**
     * @ORM\OneToMany(targetEntity="UserSlack", mappedBy="team")
     * @JMS\Groups({"team_list"})
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
    public function setUsers($users)
    {
        $this->users = $users;
    }
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     * @return TeamSlack
     */
    public function setToken($token)
    {
        $this->token = $token;
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
     * @param mixed $id
     * @return TeamSlack
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
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
     * @return TeamSlack
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param mixed $domain
     * @return TeamSlack
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmailDomain()
    {
        return $this->emailDomain;
    }

    /**
     * @param mixed $emailDomain
     * @return TeamSlack
     */
    public function setEmailDomain($emailDomain)
    {
        $this->emailDomain = $emailDomain;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param mixed $icon
     * @return TeamSlack
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdSlack()
    {
        return $this->idSlack;
    }

    /**
     * @param mixed $idSlack
     * @return TeamSlack
     */
    public function setIdSlack($idSlack)
    {
        $this->idSlack = $idSlack;
        return $this;
    }




}