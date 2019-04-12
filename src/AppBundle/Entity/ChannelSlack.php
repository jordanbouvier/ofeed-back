<?php
namespace AppBundle\Entity;

use AppBundle\Service\MessageChannel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="channel_slack")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ChannelSlackRepository")
 */
class ChannelSlack
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
     * @JMS\Groups({"defaultState", "details", "channelList"})
     */
    private $idSlack;

    /**
     * @ORM\Column(name="name", type="string", length=191)
     * @JMS\Groups({"defaultState", "details", "channelList", "search_result"})
     */
    private $name;


    /**
     * @ORM\Column(name="createdAt", type="datetime")
     * @JMS\Type("DateTime<'d-m-Y'>")
     * @JMS\Groups({"defaultState", "details", "channelList"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="TeamSlack")
     * @JMS\Groups({"defaultState", "details", "channelList"})
     */
    private $team;


    /**
     * @ORM\ManyToMany(targetEntity="UserSlack", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="channel_users",
     *     joinColumns={@ORM\JoinColumn(name="id_channel", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="id_user", referencedColumnName="id")}
     *     )
     * @JMS\Groups({"details", "defaultState"})
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity="UserSlack")
     * @JMS\Groups({"defaultState", "details", "channelList"})
     */
    private $creator;

    /**
     * @ORM\Column(name="purpose", type="string", length=191)
     * @JMS\Groups({"defaultState", "details", "channelList"})
     */
    private $purpose;

    /**
     * @return mixed
     */

    /**
     * @ORM\OneToMany(targetEntity="MessageSlack", mappedBy="channel", cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"createdAt" = "ASC"})
     * @JMS\Exclude
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity="FileSlack", mappedBy="channel", cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
     * @JMS\Exclude
     */
    private $files;


    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->users = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->files = new ArrayCollection();

    }
    public function addMessage(MessageSlack $messageSlack)
    {
        $messageSlack->setChannel($this);
        $this->messages->add($messageSlack);

    }
    public function removeUser(UserSlack $user)
    {
        $this->users->removeElement($user);
    }
    public function removeMessage(MessageSlack $messageSlack)
    {
        $this->messages->removeElement($messageSlack);
    }
    public function addFile(FileSlack $file)
    {
        $file->setChannel($this);
        $this->files->add($file);
    }
    public function removeFile(FileSlack $file)
    {
        $this->files->removeElement($file);
    }

    /**
     * @return mixed
     */
    public function getPurpose()
    {
        return $this->purpose;
    }

    /**
     * @param mixed $purpose
     * @return ChannelSlack
     */
    public function setPurpose($purpose)
    {
        $this->purpose = $purpose;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param mixed $files
     * @return ChannelSlack
     */
    public function setFiles($files)
    {
        $this->files = $files;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param mixed $messages
     * @return ChannelSlack
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
        return $this;
    }


    public function getId()
    {
        return $this->id;
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
     * @return ChannelSlack
     */
    public function setIdSlack($idSlack)
    {
        $this->idSlack = $idSlack;
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
     * @return ChannelSlack
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return ChannelSlack
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt->setTimestamp($createdAt);
        return $this;
    }


    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param mixed $users
     * @return ChannelSlack
     */
    public function setUsers($users)
    {
        $this->users = $users;
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
     * @return ChannelSlack
     */
    public function setTeam($team)
    {
        $this->team = $team;
        return $this;
    }

    public function addUser(UserSlack $user)
    {
        $this->users[] = $user;
    }

    /**
     * @return mixed
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param mixed $creator
     * @return ChannelSlack
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
        return $this;
    }





}