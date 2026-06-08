<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, LibraryGame>
     */
    #[ORM\OneToMany(targetEntity: LibraryGame::class, mappedBy: 'status_id')]
    private Collection $libraryGames;

    public function __construct()
    {
        $this->libraryGames = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, LibraryGame>
     */
    public function getLibraryGames(): Collection
    {
        return $this->libraryGames;
    }

    public function addLibraryGame(LibraryGame $libraryGame): static
    {
        if (!$this->libraryGames->contains($libraryGame)) {
            $this->libraryGames->add($libraryGame);
            $libraryGame->setStatusId($this);
        }

        return $this;
    }

    public function removeLibraryGame(LibraryGame $libraryGame): static
    {
        if ($this->libraryGames->removeElement($libraryGame)) {
            // set the owning side to null (unless already changed)
            if ($libraryGame->getStatusId() === $this) {
                $libraryGame->setStatusId(null);
            }
        }

        return $this;
    }
}
