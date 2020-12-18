<?php

namespace App\Infrastructure\Social\Entity;

use Doctrine\ORM\Mapping as ORM;

trait SocialLoggableTrait
{

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $discordId = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $battleId = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $steamId = null;

    public function getDiscordId(): ?string
    {
        return $this->discordId;
    }

    public function setDiscordId(?string $discordId): self
    {
        $this->discordId = $discordId;
        return $this;
    }

    public function getBattleId(): ?string
    {
        return $this->battleId;
    }

    public function setBattleId(?string $battleId): self
    {
        $this->battleId = $battleId;
        return $this;
    }

    public function getSteamId(): ?string
    {
        return $this->steamId;
    }

    public function setSteamId(?string $steamId): self
    {
        $this->steamId = $steamId;
        return $this;
    }

    public function useOauth(): bool
    {
        return null !== $this->discordId && null !== $this->battleId && null !== $this->steamId;
    }

}
