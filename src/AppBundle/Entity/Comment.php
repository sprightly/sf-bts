<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Comment
 * @ORM\Entity
 * @ORM\Table(name="comment")
 * @package AppBundle\Entity
 */
class Comment
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * @var User
     */
    private $author;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $body;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="comments")
     * @ORM\JoinColumn(name="issue_id", referencedColumnName="id", onDelete="CASCADE")
     * @var Issue
     */
    private $issue;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return Comment
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Comment
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set author
     *
     * @param \AppBundle\Entity\User $author
     * @return Comment
     */
    public function setAuthor(User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \AppBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set issue
     *
     * @param \AppBundle\Entity\Issue $issue
     * @return Comment
     */
    public function setIssue(Issue $issue = null)
    {
        $this->issue = $issue;

        return $this;
    }

    /**
     * Get issue
     *
     * @return \AppBundle\Entity\Issue
     */
    public function getIssue()
    {
        return $this->issue;
    }
}
