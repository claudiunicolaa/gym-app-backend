<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Alexandru Emil Popa <a.pope95@yahoo.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="courses")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CourseRepository")
 */
class Course
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="trainer_id", referencedColumnName="id")
     */
    protected $trainer;

    /**
     * @ORM\Column(name="event_date", type="datetime")
     */
    protected $eventDate;

    /**
     * @ORM\Column(name="capacity", type="integer")
     */
    protected $capacity;

    /**
     * Many Courses have Many Users.
     *
     * @ManyToMany(targetEntity="User", mappedBy="attendingCourses")
     */
    protected $registeredUsers;

    /**
     * Course constructor.
     */
    public function __construct()
    {
        $this->registeredUsers = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getTrainer() : User
    {
        return $this->trainer;
    }

    /**
     * @param User $trainer
     * @return $this
     */
    public function setTrainer(User $trainer) : self
    {
        $this->trainer = $trainer;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEventDate() : \DateTime
    {
        return $this->eventDate;
    }

    /**
     * @param \DateTime $eventDate
     *
     * @return $this
     */
    public function setEventDate(\DateTime $eventDate) : self
    {
        $this->eventDate = $eventDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getCapacity() : int
    {
        return $this->capacity;
    }

    /**
     * @param int $capacity
     *
     * @return $this
     */
    public function setCapacity(int $capacity) : self 
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getRegisteredUsers(): Collection
    {
        return $this->registeredUsers;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function addRegisteredUser(User $user) : self
    {
        $this->registeredUsers->add($user);

        return $this;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function removeRegisteredUser(User $user) : self
    {
        $this->registeredUsers->removeElement($user);

        return $this;
    }
}
