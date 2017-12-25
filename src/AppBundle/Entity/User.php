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
    const DEFAULT_IMAGE_NAME = 'default.jpg';

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
     * @ORM\Column(type="boolean")
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
     * @var bool
     *
     * @ORM\Column(type="boolean")
     * @Groups({"user"})
     */
    protected $isAtTheGym = 0;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"user"})
     */
    protected $image;

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

        $this->trainedCourses   = new ArrayCollection();
        $this->attendingCourses = new ArrayCollection();

        $this->subscribed = false;
        $this->image      = self::DEFAULT_IMAGE_NAME;
        $this->isAtTheGym = false;
    }

    /**
     * @return bool
     */
    public function isSubscribed(): bool
    {
        return $this->subscribed;
    }

    /**
     * @param bool $subscribed
     *
     * @return $this
     */
    public function setSubscribed(bool $subscribed): self
    {
        $this->subscribed = $subscribed;

        return $this;

    }

    /**
     * @return string
     */
    public function getFirstName(): ?string
    {
        return (string)$this->firstName;
    }

    /**
     * @param string $firstName
     * @return $this
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): ?string
    {
        return (string)$this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getImage(): ?string
    {
        return (string)$this->image;
    }

    /**
     * @param null|string $image
     *
     * @return $this
     */
    public function setImage(?string $image): self
    {
        $this->image = $image;

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
    public function addTrainedCourse(Course $course): self
    {
        $this->trainedCourses->add($course);

        return $this;
    }

    /**
     * @param Course $course
     *
     * @return $this
     */
    public function removeTrainedCourse(Course $course): self
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
    public function addAttendingCourse(Course $course): self
    {
        $this->attendingCourses->add($course);

        return $this;
    }

    /**
     * @param Course $course
     *
     * @return $this
     */
    public function removeAttendingCourse(Course $course): self
    {
        $this->attendingCourses->removeElement($course);

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->getLastName() . ' ' . $this->getFirstName();
    }

    /**
     * @return bool
     */
    public function isAtTheGym(): bool
    {
        return $this->isAtTheGym;
    }

    /**
     * @param bool $isAtTheGym
     *
     * @return $this
     */
    public function setIsAtTheGym(bool $isAtTheGym): self
    {
        $this->isAtTheGym = $isAtTheGym;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'         => $this->getId(),
            'fullName'   => $this->getFullName(),
            'email'      => $this->getEmail(),
            'image'    => $this->getImage(),
            'isAtTheGym' => $this->isAtTheGym()
        ];
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setProperties(array $data): self
    {
        $this->setEmail($data['email'] ?? '');
        $this->setUsername($this->getEmail());
        $this->setLastName(explode(' ', $data['fullName'])[0] ?? '');
        $this->setFirstName(explode(' ', $data['fullName'])[1] ?? '');
        $this->setImage($data['image'] ?? '');
        $this->setPlainPassword($data['password'] ?? '');
        $this->addRole("ROLE_USER");
        $this->setEnabled(true);
        $this->setIsAtTheGym(false);

        return $this;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function updateProperties(array $data): self
    {
        if (isset($data['fullName'])) {
            $this->setLastName(explode(' ', $data['fullName'])[0]);
            $this->setFirstName(explode(' ', $data['fullName'])[1]);
        }

        if (isset($data['image']) && null !== $data['image']) {
            $this->setImage($data['image']);
        }

        if (isset($data['password'])) {
            $this->setPlainPassword($data['password']);
        }

        if (isset($data['email'])) {
            $this->setEmail($data['email']);
            $this->setEmailCanonical($data['email']);
        }

        if (isset($data['isAtTheGym'])) {
            $this->setIsAtTheGym($data['isAtTheGym'] === 'true' ? 1 : 0);
        }

        return $this;
    }

    /**
     * Returns the highest user role
     *
     * @return string
     */
    public function getHighestRole(): string
    {
        $userRoles               = $this->getRoles();
        $rolesSortedByImportance = ['ROLE_ADMIN', 'ROLE_TRAINER'];
        foreach ($rolesSortedByImportance as $role) {
            if (in_array($role, $userRoles)) {
                return $role;
            }
        }

        return 'ROLE_USER';
    }
}
