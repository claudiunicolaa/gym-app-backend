<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;

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
     * @return $this
     */
    public function setEventDate(\DateTime $eventDate) : self
    {
        $this->eventDate = $eventDate;
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
     * @return $this
     */
    public function setCapacity(int $capacity) : self 
    {
        $this->capacity = $capacity;
    }
}
