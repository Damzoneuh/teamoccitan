<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GaleryRepository")
 */
class Galery
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Img")
     * @ORM\JoinColumn(nullable=false)
     */
    private $img;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImg(): ?Img
    {
        return $this->img;
    }

    public function setImg(?Img $img): self
    {
        $this->img = $img;

        return $this;
    }
}
