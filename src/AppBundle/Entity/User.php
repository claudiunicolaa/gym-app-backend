<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;

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
     * @Groups({"user-write"})
     */
    protected $plainPassword;

    /**
     * @Groups({"user"})
     */
    protected $username;

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
}

