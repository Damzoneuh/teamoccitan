<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     */
    private $pilotEngage;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Car")
     */
    private $car;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Relay")
     */
    private $relay;

    /**
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Track")
     */
    private $track;

    public function __construct()
    {
        $this->pilotEngage = new ArrayCollection();
        $this->car = new ArrayCollection();
        $this->relay = new ArrayCollection();
        $this->track = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getPilotEngage(): Collection
    {
        return $this->pilotEngage;
    }

    public function addPilotEngage(User $pilotEngage): self
    {
        if (!$this->pilotEngage->contains($pilotEngage)) {
            $this->pilotEngage[] = $pilotEngage;
        }

        return $this;
    }

    public function removePilotEngage(User $pilotEngage): self
    {
        if ($this->pilotEngage->contains($pilotEngage)) {
            $this->pilotEngage->removeElement($pilotEngage);
        }

        return $this;
    }

    /**
     * @return Collection|Car[]
     */
    public function getCar(): Collection
    {
        return $this->car;
    }

    public function addCar(Car $car): self
    {
        if (!$this->car->contains($car)) {
            $this->car[] = $car;
        }

        return $this;
    }

    public function removeCar(Car $car): self
    {
        if ($this->car->contains($car)) {
            $this->car->removeElement($car);
        }

        return $this;
    }

    /**
     * @return Collection|Relay[]
     */
    public function getRelay(): Collection
    {
        return $this->relay;
    }

    public function addRelay(Relay $relay): self
    {
        if (!$this->relay->contains($relay)) {
            $this->relay[] = $relay;
        }

        return $this;
    }

    public function removeRelay(Relay $relay): self
    {
        if ($this->relay->contains($relay)) {
            $this->relay->removeElement($relay);
        }

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return Collection|Track[]
     */
    public function getTrack(): Collection
    {
        return $this->track;
    }

    public function addTrack(Track $track): self
    {
        if (!$this->track->contains($track)) {
            $this->track[] = $track;
        }

        return $this;
    }

    public function removeTrack(Track $track): self
    {
        if ($this->track->contains($track)) {
            $this->track->removeElement($track);
        }

        return $this;
    }
}
