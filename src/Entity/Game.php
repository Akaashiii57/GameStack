<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cover_url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $developer = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $publisher = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $release_date = null;

    #[ORM\Column(nullable: true)]
    private ?int $estimated_playtime = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $mode = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, LibraryGame>
     */
    #[ORM\OneToMany(targetEntity: LibraryGame::class, mappedBy: 'game')]
    private Collection $libraryGames;

    public function __construct()
    {
        $this->libraryGames = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCoverUrl(): ?string
    {
        return $this->cover_url;
    }

    public function setCoverUrl(?string $cover_url): static
    {
        $this->cover_url = $cover_url;

        return $this;
    }

    public function getDeveloper(): ?string
    {
        return $this->developer;
    }

    public function setDeveloper(?string $developer): static
    {
        $this->developer = $developer;

        return $this;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(?string $publisher): static
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function getReleaseDate(): ?\DateTime
    {
        return $this->release_date;
    }

    public function setReleaseDate(?\DateTime $release_date): static
    {
        $this->release_date = $release_date;

        return $this;
    }

    public function getEstimatedPlaytime(): ?int
    {
        return $this->estimated_playtime;
    }

    public function setEstimatedPlaytime(?int $estimated_playtime): static
    {
        $this->estimated_playtime = $estimated_playtime;

        return $this;
    }

    public function getMode(): ?string
    {
        return $this->mode;
    }

    public function setMode(?string $mode): static
    {
        $this->mode = $mode;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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
            $libraryGame->setGame($this);
        }

        return $this;
    }

    public function removeLibraryGame(LibraryGame $libraryGame): static
    {
        if ($this->libraryGames->removeElement($libraryGame)) {
            // set the owning side to null (unless already changed)
            if ($libraryGame->getGame() === $this) {
                $libraryGame->setGame(null);
            }
        }

        return $this;
    }
}
