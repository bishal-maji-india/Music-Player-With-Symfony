<?php

namespace App\Entity;

use App\Repository\MusicTableRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MusicTableRepository::class)
 */
class MusicTable
{
  /**
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $audio;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $name;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $singer;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $genre;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $thumb;

  /**
   * @ORM\Column(type="integer")
   */
  private $upload_by;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getAudio(): ?string
  {
    return $this->audio;
  }

  public function setAudio(string $audio): self
  {
    $this->audio = $audio;

    return $this;
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

  public function getSinger(): ?string
  {
    return $this->singer;
  }

  public function setSinger(string $singer): self
  {
    $this->singer = $singer;

    return $this;
  }

  public function getGenre(): ?string
  {
    return $this->genre;
  }

  public function setGenre(string $genre): self
  {
    $this->genre = $genre;

    return $this;
  }

  public function getThumb(): ?string
  {
    return $this->thumb;
  }

  public function setThumb(string $thumb): self
  {
    $this->thumb = $thumb;

    return $this;
  }

  public function getUploadBy(): ?int
  {
    return $this->upload_by;
  }

  public function setUploadBy(int $upload_by): self
  {
    $this->upload_by = $upload_by;

    return $this;
  }
}
