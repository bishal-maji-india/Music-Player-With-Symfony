<?php

namespace App\Entity;

use App\Repository\FavouriteTableRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FavouriteTableRepository::class)
 */
class FavouriteTable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $music_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getMusicId(): ?int
    {
        return $this->music_id;
    }

    public function setMusicId(int $music_id): self
    {
        $this->music_id = $music_id;

        return $this;
    }
}
