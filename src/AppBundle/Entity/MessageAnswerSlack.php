<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="message_answer_slack")
 * @ORM\Entity
 */
class MessageAnswerSlack
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="UserSlack")
     */
    private $author;

    /**
     * @ORM\Column(name="createdAt", type="datetime")
     * @JMS\Type("DateTime<'d-m-Y-g:iA'>")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="MessageSlack", inversedBy="replies")
     */
    private $message;

    /**
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(name="ts", type="string")
     */
    private $ts;

    /**
     * @ORM\Column(name="thread_ts", type="string")
     */
    private $threadTs;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->reactions = new ArrayCollection();
    }

    /**
     * @ORM\ManyToMany(targetEntity="Reaction", cascade={"persist", "remove"})
     */
    private $reactions;

    public function getReactions()
    {
        return $this->reactions;
    }

    public function addReaction(Reaction $reaction)
    {
        $this->reactions->add($reaction);
    }
    public function removeReaction(Reaction $reaction)
    {
        $this->reactions->removeElement($reaction);
    }
    public function setReactions($reactions)
    {
        return $this->reactions = $reactions;
    }

    /**
     * @return mixed
     */
    public function getThreadTs()
    {
        return $this->threadTs;
    }

    /**
     * @param mixed $threadTs
     * @return MessageAnswerSlack
     */
    public function setThreadTs($threadTs)
    {
        $this->threadTs = $threadTs;
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
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     * @return MessageAnswerSlack
     */
    public function setAuthor($author)
    {
        $this->author = $author;
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
     * @return MessageAnswerSlack
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt->setTimestamp($createdAt);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     * @return MessageAnswerSlack
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
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
     * @return MessageAnswerSlack
     */
    public function setContent($content)
    {
        $this->content = $content;
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
     * @return MessageAnswerSlack
     */
    public function setTs($ts)
    {
        $this->ts = $ts;
        return $this;
    }






}
