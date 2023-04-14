<?php

namespace App\Entity;

use App\Entity\Trait\SlugTrait;
use App\Repository\FigureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

  #[ORM\Column]
  private ?\DateTimeImmutable $created_at = null;

  #[ORM\Column]
  private ?\DateTimeImmutable $modified_at = null;

  #[ORM\OneToMany(mappedBy: 'idFigure', targetEntity: Comment::class, orphanRemoval: true)]
  private Collection $comments;

  #[ORM\ManyToOne(inversedBy: 'figures')]
  #[ORM\JoinColumn(nullable: false)]
  private ?User $userId = null;


  #[ORM\ManyToOne(inversedBy: 'figures')]
  private ?Groupe $groupe = null;

  #[ORM\OneToMany(mappedBy: 'figure', targetEntity: Images::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
  private Collection $image;

  public function __construct()
  {
      $this->comments = new ArrayCollection();
      $this->image = new ArrayCollection();
  }

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

  public function getCreatedAt(): ?\DateTimeImmutable
  {
      return $this->created_at;
  }

  public function setCreatedAt(\DateTimeImmutable $created_at): self
  {
      $this->created_at = $created_at;

      return $this;
  }

  public function getModifiedAt(): ?\DateTimeImmutable
  {
      return $this->modified_at;
  }

  public function setModifiedAt(\DateTimeImmutable $modified_at): self
  {
      $this->modified_at = $modified_at;

      return $this;
  }

  /**
   * @return Collection<int, Comment>
   */
  public function getComments(): Collection
  {
      return $this->comments;
  }

  public function addComment(Comment $comment): self
  {
      if (!$this->comments->contains($comment)) {
          $this->comments->add($comment);
          $comment->setIdFigure($this);
      }

      return $this;
  }

  public function removeComment(Comment $comment): self
  {
      if ($this->comments->removeElement($comment)) {
          // set the owning side to null (unless already changed)
          if ($comment->getIdFigure() === $this) {
              $comment->setIdFigure(null);
          }
      }

      return $this;
  }

  public function getUserId(): ?User
  {
      return $this->userId;
  }

  public function setUserId(?User $userId): self
  {
      $this->userId = $userId;

      return $this;
  }



  public function getGroupe(): ?groupe
  {
      return $this->groupe;
  }

  public function setGroupe(?groupe $groupe): self
  {
      $this->groupe = $groupe;

      return $this;
  }

  /**
   * @return Collection<int, images>
   */
  public function getImage(): Collection
  {
      return $this->image;
  }

  public function addImage(images $image): self
  {
      if (!$this->image->contains($image)) {
          $this->image->add($image);
          $image->setFigure($this);
      }

      return $this;
  }

  public function removeImage(images $image): self
  {
      if ($this->image->removeElement($image)) {
          // set the owning side to null (unless already changed)
          if ($image->getFigure() === $this) {
              $image->setFigure(null);
          }
      }

      return $this;
  }
}
