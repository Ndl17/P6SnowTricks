<?php

namespace App\Entity;

use App\Entity\Trait\SlugTrait;
use App\Repository\FigureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FigureRepository::class)]
class Figure
{

  use SlugTrait;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 150)]
  private ?string $name = null;

  #[ORM\Column(length: 255)]
  private ?string $description = null;

  #[ORM\Column(length: 255)]
  private ?string $type = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $imgName = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $vidName = null;

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

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(string $description): self
  {
    $this->description = $description;

    return $this;
  }

  public function getType(): ?string
  {
    return $this->type;
  }

  public function setType(string $type): self
  {
    $this->type = $type;

    return $this;
  }

  public function getImgName(): ?string
  {
    return $this->imgName;
  }

  public function setImgName(?string $imgName): self
  {
    $this->imgName = $imgName;

    return $this;
  }

  public function getVidName(): ?string
  {
    return $this->vidName;
  }

  public function setVidName(?string $vidName): self
  {
    $this->vidName = $vidName;

    return $this;
  }
}
