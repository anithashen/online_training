<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CourseRepository::class)
 */
class Course
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $topic;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startdatetime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $enddatetime;

    /**
     * @ORM\Column(type="integer")
     */
    private $limitedseats;

    /**
     * @ORM\ManyToMany(targetEntity=Reservation::class, mappedBy="courseid")
     * @ORM\JoinTable(name="reservation_course")
     */
    private $reservation;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="courses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $traineroruserid;

    public function __construct()
    {
        $this->reservation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTopic(): ?string
    {
        return $this->topic;
    }

    public function setTopic(?string $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStartdatetime(): ?\DateTimeInterface
    {
        return $this->startdatetime;
    }

    public function setStartdatetime(?\DateTimeInterface $startdatetime): self
    {
        $this->startdatetime = $startdatetime;

        return $this;
    }

    public function getEnddatetime(): ?\DateTimeInterface
    {
        return $this->enddatetime;
    }

    public function setEnddatetime(?\DateTimeInterface $enddatetime): self
    {
        $this->enddatetime = $enddatetime;

        return $this;
    }

    public function getLimitedseats(): ?int
    {
        return $this->limitedseats;
    }

    public function setLimitedseats(int $limitedseats): self
    {
        $this->limitedseats = $limitedseats;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUserid(): Collection
    {
        return $this->userid;
    }

    /**
     * @return Collection|Reservation[]
     */
    public function getReservation(): Collection
    {
        return $this->reservation;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservation->contains($reservation)) {
            $this->reservation[] = $reservation;
            $reservation->addCourseid($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservation->removeElement($reservation)) {
            $reservation->removeCourseid($this);
        }

        return $this;
    }

    public function getTraineroruserid(): ?User
    {
        return $this->traineroruserid;
    }

    public function setTraineroruserid(?User $traineroruserid): self
    {
        $this->traineroruserid = $traineroruserid;

        return $this;
    }
}
