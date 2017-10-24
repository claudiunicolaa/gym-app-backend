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
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User | null
     */
    public function getTrainer()
    {
        if(isset($this->trainer))
            return $this->trainer;

        return null;
    }

    /**
     * @param User $trainer | null
     * @return $this
     */
    public function setTrainer($trainer)
    {
        if(isset($trainer))
            $this->trainer = $trainer;

        return $this;
    }

    /**
     * @return datetime
     */
    public function getEventDate()
    {
        return $this->eventDate;
    }

    /**
     * @param datetime $eventDate | null
     * @return $this
     */
    public function setEventDate($eventDate)
    {
        if(isset($eventDate))
            $this->eventDate = $eventDate;

        return $this;
    }

    /**
     * @return integer
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @param integer $capacity | null
     * @return $this
     */
    public function setCapacity($capacity)
    {
        if(isset($capacity))
            $this->capacity = $capacity;

        return $this;
    }
}
