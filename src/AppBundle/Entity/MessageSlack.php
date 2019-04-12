<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
/**
 * @ORM\Table(name="messages_slack", indexes={@ORM\Index(name="search_inx", columns={"ts"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MessageSlackRepository")
 */
class MessageSlack
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     * @JMS\Type("DateTime<'d-m-Y-g:iA'>")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="UserSlack", inversedBy="messages")
     */
    private $userSlack;

    /**
     * @ORM\ManyToOne(targetEntity="ChannelSlack", inversedBy="messages")
     * @JMS\Groups("search_result")
     */
    private $channel;

    /**
     * @ORM\ManyToOne(targetEntity="TeamSlack")
     * @JMS\Groups("search_result")
     */
    private $team;

    /**
     * @ORM\Column(name="ts", type="string")
     */
    private $ts;

    /**
     * @ORM\Column(name="subtype", type="string", length=30, nullable=true)
     */
    private $subtype;

    /**
     * @ORM\Column(name="pinned", type="boolean")
     */
    private $pinned;

    /**
     * @ORM\ManyToOne(targetEntity="FileSlack", cascade={"persist"})
     */
    private $file;


    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->reactions = new ArrayCollection();
        $this->pinned = false;
    }

    /**
     * @ORM\ManyToMany(targetEntity="Reaction", cascade={"persist", "remove"})
     */
    private $reactions;

    /**
     * @ORM\OneToMany(targetEntity="MessageAnswerSlack", mappedBy="message", cascade={"remove"})
     */
    private $replies;

    /**
     * @return mixed
     */
    public function getReplies()
    {
        return $this->replies;
    }

    /**
     * @param mixed $replies
     * @return MessageSlack
     */
    public function setReplies($replies)
    {
        $this->replies = $replies;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     * @return MessageSlack
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getPinned()
    {
        return $this->pinned;
    }

    /**
     * @param mixed $pinned
     * @return MessageSlack
     */
    public function setPinned($pinned)
    {
        $this->pinned = $pinned;
        return $this;
    }



    public function getReactions()
    {
        return $this->reactions;
    }
    public function setReactions($reactions)
    {
        return $this->reactions = $reactions;
    }

    public function addReaction(Reaction $reaction)
    {
        $this->reactions->add($reaction);
    }
    public function removeReaction(Reaction $reaction)
    {
        $this->reactions->removeElement($reaction);
    }

    /**
     * @return mixed
     */
    public function getSubtype()
    {
        return $this->subtype;
    }

    /**
     * @param mixed $subtype
     * @return MessageSlack
     */
    public function setSubtype($subtype)
    {
        $this->subtype = $subtype;
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
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     * @return MessageSlack
     */
    public function setContent($content)
    {
        $this->content = $content;
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
     * @return MessageSlack
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt->setTimestamp($createdAt);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserSlack()
    {
        return $this->userSlack;
    }

    /**
     * @param mixed $userSlack
     * @return MessageSlack
     */
    public function setUserSlack($userSlack)
    {
        $this->userSlack = $userSlack;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param mixed $channel
     * @return MessageSlack
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
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
     * @return MessageSlack
     */
    public function setTeam($team)
    {
        $this->team = $team;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTs()
    {
        return $this->ts;
    }

    /**
     * @param mixed $ts
     * @return MessageSlack
     */
    public function setTs($ts)
    {
        $this->ts = $ts;
        return $this;
    }



}