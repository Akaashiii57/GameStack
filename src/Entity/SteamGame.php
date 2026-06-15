<?php

namespace App\Entity;

use App\Repository\SteamGameRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SteamGameRepository::class)]
class SteamGame
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameUser $user = null;

    #[ORM\Column]
    private ?int $appId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $playtimeForever = 0;

    #[ORM\Column]
    private ?int $playtime2Weeks = 0;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastPlayed = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $lastSyncAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?GameUser
    {
        return $this->user;
    }

    public function setUser(?GameUser $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAppId(): ?int
    {
        return $this->appId;
    }

    public function setAppId(int $appId): static
    {
        $this->appId = $appId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPlaytimeForever(): ?int
    {
        return $this->playtimeForever;
    }

    public function setPlaytimeForever(int $playtimeForever): static
    {
        $this->playtimeForever = $playtimeForever;

        return $this;
    }

    public function getPlaytime2Weeks(): ?int
    {
        return $this->playtime2Weeks;
    }

    public function setPlaytime2Weeks(int $playtime2Weeks): static
    {
        $this->playtime2Weeks = $playtime2Weeks;

        return $this;
    }

    public function getLastPlayed(): ?\DateTimeInterface
    {
        return $this->lastPlayed;
    }

    public function setLastPlayed(?\DateTimeInterface $lastPlayed): static
    {
        $this->lastPlayed = $lastPlayed;

        return $this;
    }

    public function getLastSyncAt(): ?\DateTimeInterface
    {
        return $this->lastSyncAt;
    }

    public function setLastSyncAt(\DateTimeInterface $lastSyncAt): static
    {
        $this->lastSyncAt = $lastSyncAt;

        return $this;
    }
}
