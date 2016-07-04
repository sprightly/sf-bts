<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @package AppBundle\Entity
 */
class User implements UserInterface, \Serializable
{
    const ADMIN_ROLE = 'ROLE_ADMIN';
    const MANAGER_ROLE = 'ROLE_MANAGER';
    const OPERATOR_ROLE = 'ROLE_OPERATOR';
    const USER_ROLE = 'ROLE_USER';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @var string
     */
    private $username;

    /**
     * @ORM\Column(type="string", name="full_name", length=120)
     * @var string
     */
    private $fullName;

    /**
     * @ORM\Column(type="string", length=64)
     * @var string
     */
    private $password;

    /**
     * @ORM\Column(type="array")
     * @var array
     */
    private $roles;

    /**
     * @ORM\ManyToMany(targetEntity="Project", mappedBy="members")
     * @var ArrayCollection Project[]
     */
    private $projects;

    /**
     * @ORM\ManyToMany(targetEntity="Issue", mappedBy="collaborators")
     * @var ArrayCollection Issue[]
     */
    private $issues;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     * @var string
     */
    private $timezone;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->issues = new ArrayCollection();
        $this->roles = array(self::USER_ROLE);
        $this->timezone = 'UTC';
    }

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
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set roles
     *
     * @param array $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Add projects
     *
     * @param Project $projects
     * @return User
     */
    public function addProject(Project $projects)
    {
        $this->projects[] = $projects;

        return $this;
    }

    /**
     * Remove projects
     *
     * @param Project $projects
     */
    public function removeProject(Project $projects)
    {
        $this->projects->removeElement($projects);
    }

    /**
     * Get projects
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Add issues
     *
     * @param Issue $issues
     * @return User
     */
    public function addIssue(Issue $issues)
    {
        $this->issues[] = $issues;

        return $this;
    }

    /**
     * Remove issues
     *
     * @param Issue $issues
     */
    public function removeIssue(Issue $issues)
    {
        $this->issues->removeElement($issues);
    }

    /**
     * Get issues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIssues()
    {
        return $this->issues;
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize(
            array(
                $this->id,
                $this->username,
                $this->password,
            )
        );
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            ) = unserialize($serialized);
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }

    /**
     * Set timezone
     *
     * @param string $timezone
     * @return User
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }
}
