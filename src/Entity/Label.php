<?php

namespace App\Entity;

use App\Repository\LabelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LabelRepository::class)]
class Label
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updateAt = null;

    #[ORM\ManyToMany(targetEntity: Artist::class, inversedBy: 'labels')]
    private Collection $artist_idArtist;

    public function __construct()
    {
        $this->artist_idArtist = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeImmutable $updateAt): static
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * @return Collection<int, Artist>
     */
    public function getArtistIdArtist(): Collection
    {
        return $this->artist_idArtist;
    }

    public function addArtistIdArtist(Artist $artistIdArtist): static
    {
        if (!$this->artist_idArtist->contains($artistIdArtist)) {
            $this->artist_idArtist->add($artistIdArtist);
        }

        return $this;
    }

    public function removeArtistIdArtist(Artist $artistIdArtist): static
    {
        $this->artist_idArtist->removeElement($artistIdArtist);

        return $this;
    }
}
