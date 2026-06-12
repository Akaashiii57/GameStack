<?php

namespace App\Entity;

use App\Repository\SteamAccountRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SteamAccountRepository::class)]
class SteamAccount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameUser $user = null;

    #[ORM\Column(length: 255)]
    private ?string $steamId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $personaName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profileUrl = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $linkedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
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

    public function getSteamId(): ?string
    {
        return $this->steamId;
    }

    public function setSteamId(string $steamId): static
    {
        $this->steamId = $steamId;

        return $this;
    }

    public function getPersonaName(): ?string
    {
        return $this->personaName;
    }

    public function setPersonaName(?string $personaName): static
    {
        $this->personaName = $personaName;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getProfileUrl(): ?string
    {
        return $this->profileUrl;
    }

    public function setProfileUrl(?string $profileUrl): static
    {
        $this->profileUrl = $profileUrl;

        return $this;
    }

    public function getLinkedAt(): ?\DateTimeInterface
    {
        return $this->linkedAt;
    }

    public function setLinkedAt(\DateTimeInterface $linkedAt): static
    {
        $this->linkedAt = $linkedAt;

        return $this;
    }

    public function getLastSyncAt(): ?\DateTimeInterface
    {
        return $this->lastSyncAt;
    }

    public function setLastSyncAt(?\DateTimeInterface $lastSyncAt): static
    {
        $this->lastSyncAt = $lastSyncAt;

        return $this;
    }
}
