<?php

namespace App\Entity;

use App\Repository\StoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StoryRepository::class)]
class Story
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $story = null;

    #[ORM\Column(nullable: true)]
    private ?int $likes = null;

    #[ORM\Column(nullable: true)]
    private ?int $dislikes = null;

    #[ORM\OneToMany(mappedBy: 'refstory', targetEntity: Comments::class, orphanRemoval: true)]
    private Collection $storie;

    #[ORM\Column(length: 20)]
    private ?string $author = null;

    public function __construct()
    {
        $this->story = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(?int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

    public function getDislikes(): ?int
    {
        return $this->dislikes;
    }

    public function setDislikes(?int $dislikes): self
    {
        $this->dislikes = $dislikes;

        return $this;
    }

    public function setstorie(?string $storie): self
    {
        $this->story = $storie;

        return $this;
    }

    public function getstorie(): ?string
    {
        return $this->story;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getStory(): Collection
    {
        return $this->story;
    }

    public function addStory(Comments $story): self
    {
        if (!$this->story->contains($story)) {
            $this->story->add($story);
            $story->setRefstory($this);
        }

        return $this;
    }

    public function removeStory(Comments $story): self
    {
        if ($this->story->removeElement($story)) {
            // set the owning side to null (unless already changed)
            if ($story->getRefstory() === $this) {
                $story->setRefstory(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }
}
