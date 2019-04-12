<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="files_slack")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FileSlackRepository")
 */
class FileSlack
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="id_slack", type="string",length=20)
     */
    private $idSlack;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(name="name", type="string", length=191)
     */
    private $name;

    /**
     * @ORM\Column(name="title", type="string", length=191)
     */
    private $title;

    /**
     * @ORM\Column(name="mime_type", type="string", length=191)
     */
    private $mimeType;

    /**
     * @ORM\Column(name="file_type", type="string", length=60)
     */
    private $fileType;

    /**
     * @ORM\Column(name="pretty_type", type="string", length=30)
     */
    private $prettyType;

    /**
     * @ORM\ManyToOne(targetEntity="UserSlack")
     */
    private $user;

    /**
     * @ORM\Column(name="size", type="integer")
     */
    private $size;

    /**
     * @ORM\Column(name="permalink", type="text",length=300)
     */
    private $permalink;

    /**
     * @ORM\ManyToOne(targetEntity="ChannelSlack", inversedBy="files")
     * @JMS\Exclude
     */
    private $channel;

    /**
     * @ORM\ManyToMany(targetEntity="Reaction", cascade={"persist", "remove"})
     */
    private $reactions;

    /**
     * @ORM\Column(name="pinned", type="boolean")
     */
    private $isPinned;



    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->reactions = new ArrayCollection();
        $this->isPinned = false;
    }

    /**
     * @return mixed
     */
    public function getisPinned()
    {
        return $this->isPinned;
    }

    /**
     * @param mixed $isPinned
     * @return FileSlack
     */
    public function setIsPinned($isPinned)
    {
        $this->isPinned = $isPinned;
        return $this;
    }



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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return FileSlack
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return FileSlack
     */
    public function setIdSlack($idSlack)
    {
        $this->idSlack = $idSlack;
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
     * @return FileSlack
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt->setTimestamp($createdAt);
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
     * @return FileSlack
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return FileSlack
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param mixed $mimeType
     * @return FileSlack
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * @param mixed $fileType
     * @return FileSlack
     */
    public function setFileType($fileType)
    {
        $this->fileType = $fileType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrettyType()
    {
        return $this->prettyType;
    }

    /**
     * @param mixed $prettyType
     * @return FileSlack
     */
    public function setPrettyType($prettyType)
    {
        $this->prettyType = $prettyType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return FileSlack
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     * @return FileSlack
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPermalink()
    {
        return $this->permalink;
    }

    /**
     * @param mixed $permalink
     * @return FileSlack
     */
    public function setPermalink($permalink)
    {
        $this->permalink = $permalink;
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
     * @return FileSlack
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
        return $this;
    }



}
