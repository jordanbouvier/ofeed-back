<?php
namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="user_slack")
 * @ORM\Entity
 */
class UserSlack
{
    /**
     * @ORM\Column(name="id",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="id_slack", type="string", length=15)
     */
    private $idSlack;

    /**
     * @ORM\Column(name="real_name", type="string", length=191, nullable=true)
     */
    private $realName;

    /**
     * @ORM\Column(name="updated", type="datetime")
     * @JMS\Exclude()
     */
    private $updateDate;

    /**
     * @ORM\Column(name="email", type="string", length=191, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(name="display_name", type="string", length=191, nullable=true)
     */
    private $displayName;

    /**
     * @ORM\Column(name="picture", type="string", length=191)
     */
    private $picture;

    /**
     * @ORM\Column(name="big_picture", type="string", length=191, nullable=true)
     */
    private $bigPicture;

    /**
     * @ORM\ManyToOne(targetEntity="TeamSlack", inversedBy="users")
     * @JMS\Exclude()
     */
    private $team;

    /**
     * @ORM\ManyToMany(targetEntity="ChannelSlack")
     * @JMS\Exclude()
     */
    private $channels;

    /**
     * @ORM\OneToMany(targetEntity="MessageSlack", mappedBy="userSlack")
     * @JMS\Exclude
     */
    private $messages;

    public function __construct()
    {
        $this->updateDate = new \Datetime();
        $this->messages = new ArrayCollection();
    }

    public function addMessage(MessageSlack $message)
    {
        $this->messages->add($message);
    }
    public function removeMessage(MessageSlack $message)
    {
        $this->messages->removeElement($message);
    }
    public function getMessages()
    {
        return $this->messages;
    }
    public function setMessages($messages)
    {
        $this->messages = $messages;
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
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getBigPicture()
    {
        return $this->bigPicture;
    }

    /**
     * @param mixed $bigPicture
     * @return UserSlack
     */
    public function setBigPicture($bigPicture)
    {
        $this->bigPicture = $bigPicture;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param mixed $team
     * @return $this
     */
    public function setTeam($team)
    {
        $this->team = $team;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRealName()
    {
        return $this->realName;
    }

    /**
     * @param mixed $realName
     * @return $this
     */
    public function setRealName($realName)
    {
        $this->realName = $realName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * @param mixed $updateDate
     * @return $this
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate->setTimestamp($updateDate);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
     * @return UserSlack
     */
    public function setIdSlack($idSlack)
    {
        $this->idSlack = $idSlack;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param mixed $displayName
     * @return UserSlack
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * @param mixed $channels
     * @return UserSlack
     */
    public function setChannels($channels)
    {
        $this->channels = $channels;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param mixed $picture
     * @return UserSlack
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
        return $this;
    }

}