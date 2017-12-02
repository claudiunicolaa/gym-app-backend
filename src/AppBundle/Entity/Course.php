<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Alexandru Emil Popa <a.pope95@yahoo.com>
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
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
     * @ORM\JoinColumn(name="trainer_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $trainer;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="event_date", type="datetime")
     */
    protected $eventDate;

    /**
     * @var int
     *
     * @ORM\Column(name="capacity", type="integer")
     */
    protected $capacity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $imagePath;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * Many Courses have Many Users.
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="attendingCourses")
     */
    protected $registeredUsers;

    /**
     * Course constructor.
     */
    public function __construct()
    {
        $this->registeredUsers = new ArrayCollection();
        $this->imagePath = 'default.jpg';
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
    public function getTrainer() : ?User
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
    public function getEventDate() : ?\DateTime
    {
        return $this->eventDate;
    }

    /**
     * @param int $timestamp
     *
     * @return $this
     */
    public function setEventDate(int $timestamp) : self
    {
        $this->eventDate = (new \DateTime())->setTimestamp($timestamp);

        return $this;
    }

    /**
     * @return int
     */
    public function getCapacity() : ?int
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

    /**
     * @return string
     */
    public function getImagePath() : ?string
    {
        return $this->imagePath;
    }

    /**
     * @param string $imagePath
     *
     * @return $this
     */
    public function setImagePath(?string $imagePath) : self
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name) : self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return [
            'id' => $this->getId(),
            'trainer' => $this->getTrainer()->toArray(),
            'eventDate' => $this->getEventDate()->getTimestamp(),
            'capacity' => $this->getCapacity(),
            'name' => $this->getName(),
            'image' => $this->getImagePath(),
            'registeredUsers' => count($this->getRegisteredUsers())
        ];
    }

    /**
     * @return bool
     */
    public function isInThePast() : bool
    {
        $now = new \DateTime();
        return $now > $this->getEventDate();
    }
  
    /**
     * @return bool
     */
    public function reachedCapacity() : bool
    {
        return count($this->getRegisteredUsers()) >= $this->getCapacity();
    }

    /**
     * Used for sonata admin purposes
     *
     * @return int|null
     */
    public function getTimestamp() : ?int
    {
        if (null === $this->getEventDate()) {
            return null;
        }

        return $this->getEventDate()->getTimestamp();
    }

    /**
     * Used for sonata admin purposes
     *
     * @param int $timestamp
     *
     * @return $this
     */
    public function setTimestamp(int $timestamp) : self
    {
        $this->setEventDate($timestamp);

        return $this;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setProperties(array $data) : self
    {
        $this->setName($data['name']);
        $this->setTrainer($data['trainer']);
        $this->setImagePath($data['image'] ?? '');
        $this->setCapacity($data['capacity']);
        $this->setEventDate($data['eventDate']);

        return $this;
    }
}
