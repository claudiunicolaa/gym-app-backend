<?php
/**
 * Created by PhpStorm.
 * User: andu
 * Date: 27.11.2017
 * Time: 00:28
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Alexandru Emil Popa <a.pope95@yahoo.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="notes")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NoteRepository")
 */

class Note
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string")
     */
    protected $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_date", type="datetime")
     */
    protected $creationDate;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;

    public function __construct()
    {
        $this->creationDate = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getText() : string
    {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    public function setText(string $text) : self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreationDate() : \DateTime
    {
        return $this->creationDate;
    }

    /**
     * @param int $timestamp
     * @return $this
     */
    public function setCreationDate(int $timestamp) : self
    {
        $this->creationDate = (new \DateTime())->setTimestamp($timestamp);

        return $this;
    }

    /**
     * @return User
     */
    public function getUser() : User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user) : self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setProperties(array $data) : self
    {
        $this->setText($data['text']);
        $this->setCreationDate($data['creationDate']);
        $this->setUser($data['user']);

        return $this;
    }

    public function toArray() : array
    {
        return [
            'id' => $this->getId(),
            'user' => $this->getUser()->toArray(),
            'creationDate' => $this->getCreationDate()->getTimestamp(),
            'text' => $this->getText()
        ];
    }


}