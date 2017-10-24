<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use FOS\UserBundle\Model\UserInterface;
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
     * @return string | null
     */
    public function getFirstName()
    {
        if(isset($this->firstName))
            return $this->firstName;

        return null;
    }

    /**
     * @param string $firstName | null
     * @return User $this
     */
    public function setFirstName($firstName)
    {
        if(isset($firstName))
            $this->firstName = $firstName;
        else
            $this->firstName = null;

        return $this;
    }

    /**
     * @return string | null
     */
    public function getLastName()
    {
        if(isset($this->lastName))
            return $this->lastName;

        return null;
    }

    /**
     * @param string $lastName | null
     * @return $this
     */
    public function setLastName($lastName)
    {
        if(isset($lastName))
            $this->lastName = $lastName;
        else
            $this->lastName = null;

        return $this;
    }

    /**
     * @return string | null
     */
    public function getPicture()
    {
        if(isset($this->picture))
            return $this->picture;

        return null;
    }

    /**
     * @param string $picture | null
     * @return $this
     */
    public function setPicture($picture)
    {
        if(isset($picture))
            $this->picture = $picture;
        else
            $this->picture = null;

        return $this;
    }



    public function isUser(UserInterface $user = null)
    {
        return $user instanceof self && $user->id === $this->id;
    }
}

