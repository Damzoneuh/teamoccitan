<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RelayRepository")
 */
class Relay
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     */
    private $pilot;

    /**
     * @ORM\Column(type="integer")
     */
    private $timeOffset;

    public function __construct()
    {
        $this->pilot = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|User[]
     */
    public function getPilot(): Collection
    {
        return $this->pilot;
    }

    public function addPilot(User $pilot): self
    {
        if (!$this->pilot->contains($pilot)) {
            $this->pilot[] = $pilot;
        }

        return $this;
    }

    public function removePilot(User $pilot): self
    {
        if ($this->pilot->contains($pilot)) {
            $this->pilot->removeElement($pilot);
        }

        return $this;
    }

    public function getTimeOffset(): ?int
    {
        return $this->timeOffset;
    }

    public function setTimeOffset(int $timeOffset): self
    {
        $this->timeOffset = $timeOffset;

        return $this;
    }
}
