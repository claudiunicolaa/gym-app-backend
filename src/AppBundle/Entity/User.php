<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\Collection;

/**
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 *
 * @ORM\Entity
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Groups({"user"})
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user"})
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user"})
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user"})
     */
    protected $picture;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Course", mappedBy="trainer")
     */
    protected $trainedCourses;

    /**
     * Many Users are subscribed to Many Courses
     *
     * @ORM\ManyToMany(targetEntity="Course", inversedBy="registeredUsers")
     * @ORM\JoinTable(name="users_courses")
     */
    protected $attendingCourses;

    /**
     * @Groups({"user-write"})
     */
    protected $plainPassword;

    /**
     * @Groups({"user"})
     */
    protected $username;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->trainedCourses = new ArrayCollection();
        $this->attendingCourses = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getFirstName() : string
    {
        return (string) $this->firstName;
    }

    /**
     * @param string $firstName
     * @return User $this
     */
    public function setFirstName(string $firstName) : self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName() : string
    {
        return (string) $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastName(string $lastName) : self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getPicture() : string
    {
        return (string) $this->picture;
    }

    /**
     * @param string $picture
     *
     * @return $this
     */
    public function setPicture(string $picture) : self
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTrainedCourses(): Collection
    {
        return $this->trainedCourses;
    }

    /**
     * @param Course $course
     *
     * @return $this
     */
    public function addTrainedCourse(Course $course) : self
    {
        $this->trainedCourses->add($course);

        return $this;
    }

    /**
     * @param Course $course
     *
     * @return $this
     */
    public function removeTrainedCourse(Course $course) : self
    {
        $this->trainedCourses->removeElement($course);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAttendingCourses(): Collection
    {
        return $this->attendingCourses;
    }

    /**
     * @param Course $course
     *
     * @return $this
     */
    public function addAttendingCourse(Course $course) : self
    {
        $this->attendingCourses->add($course);

        return $this;
    }

    /**
     * @param Course $course
     *
     * @return $this
     */
    public function removeAttendingCourse(Course $course) : self
    {
        $this->attendingCourses->removeElement($course);

        return $this;
    }
}

