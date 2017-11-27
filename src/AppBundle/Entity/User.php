<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
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
     * @ORM\Column(type="boolean", length=255)
     * @Groups({"user"})
     */
    protected $subscribed;

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
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"user"})
     */
    protected $picturePath;

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
        $this->subscribed = false;
    }

    /**
     * @return bool
     */
    public function isSubscribed() : bool
    {
        return $this->subscribed;
    }

    /**
     * @param bool $subscribed
     *
     * @return User $this
     */
    public function setSubscribed(bool $subscribed) : self
    {
        $this->subscribed = $subscribed;

        return $this;
    }



    /**
     * @return string
     */
    public function getFirstName() : ?string
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
    public function getLastName() : ?string
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
    public function getPicturePath() : ?string
    {
        return (string) $this->picturePath;
    }

    /**
     * @param string $picturePath
     *
     * @return $this
     */
    public function setPicturePath(?string $picturePath) : self
    {
        $this->picturePath = $picturePath;

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

    /**
     * @return string
     */
    public function getFullName() : string
    {
        return $this->getLastName() . ' ' . $this->getFirstName();
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return [
            'id' => $this->getId(),
            'fullName' => $this->getFullName(),
            'email' => $this->getEmail(),
            'picturePath' => $this->getPicturePath()
        ];
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setProperties(array $data) : self
    {
        $this->setEmail($data['email'] ?? '');
        $this->setUsername($this->getEmail());
        $this->setLastName(explode(' ', $data['fullName'])[0] ?? '');
        $this->setFirstName(explode(' ', $data['fullName'])[1] ?? '');
        $this->setPicturePath($data['picture'] ?? '');
        $this->setPlainPassword($data['password'] ?? '');
        $this->addRole("ROLE_USER");
        $this->setEnabled(true);

        return $this;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function updateProperties(array $data) : self
    {
        if (isset($data['fullName'])) {
            $this->setLastName(explode(' ', $data['fullName'])[0]);
            $this->setFirstName(explode(' ', $data['fullName'])[1]);
        }

        if (isset($data['picture']) && null !== $data['picture']) {
            $this->setPicturePath($data['picture']);
        }

        if (isset($data['password'])) {
            $this->setPlainPassword($data['password']);
        }

        if (isset($data['email'])) {
            $this->setEmail($data['email']);
            $this->setEmailCanonical($data['email']);
        }

        return $this;
    }

    /**
     * Returns the highest user role
     *
     * @return string
     */
    public function getHighestRole() : string
    {
        $userRoles = $this->getRoles();
        $rolesSortedByImportance = ['ROLE_ADMIN', 'ROLE_TRAINER'];
        foreach ($rolesSortedByImportance as $role)
        {
            if (in_array($role, $userRoles))
            {
                return $role;
            }
        }

        return 'ROLE_USER';
    }
}
