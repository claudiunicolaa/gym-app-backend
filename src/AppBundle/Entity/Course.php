<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @author Alexandru Emil Popa <a.pope95@yahoo.com>
 *
 * @ORM\Entity
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
    private $trainer;

    /**
     * @var
     * @ORM\Column(name="event_date", type="datetime", nullable=false)
     */
    protected $eventDate;

    /**
     * @var
     * @ORM\Column(name="capacity", type="integer", nullable=false)
     */
    protected $capacity;
}