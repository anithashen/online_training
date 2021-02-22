<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 */
class Reservation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="reservation")
     */
    private $employeeid;

    /**
     * @ORM\ManyToMany(targetEntity=Course::class, inversedBy="reservation")
     */
    private $courseid;

    public function __construct()
    {
        $this->employeeid = new ArrayCollection();
        $this->courseid = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|User[]
     */
    public function getEmployeeid(): Collection
    {
        return $this->employeeid;
    }

    public function addEmployeeid(User $employeeid): self
    {
        if (!$this->employeeid->contains($employeeid)) {
            $this->employeeid[] = $employeeid;
        }

        return $this;
    }

    public function removeEmployeeid(User $employeeid): self
    {
        $this->employeeid->removeElement($employeeid);

        return $this;
    }

    /**
     * @return Collection|Course[]
     */
    public function getCourseid(): Collection
    {
        return $this->courseid;
    }

    public function addCourseid(Course $courseid): self
    {
        if (!$this->courseid->contains($courseid)) {
            $this->courseid[] = $courseid;
        }

        return $this;
    }

    public function removeCourseid(Course $courseid): self
    {
        $this->courseid->removeElement($courseid);

        return $this;
    }
}
