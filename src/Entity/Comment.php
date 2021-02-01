<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Comment
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
    private $rating;

    /**
     * @ORM\Column(type="text")
     */
    private $content;


    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="comments")
     */
    private $comments;


    /**
     * @ORM\Column(type="datetime")
     */
    private $createdat;

    /**
     * @ORM\ManyToOne(targetEntity=Meal::class, inversedBy="comments")
     */
    private $meal;

      /**
     * Callback appelé à chaque fois qu'on crée un commentaire
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return Response
     */
    public function prePersist(){
        if(empty($this->createdat)){
            $this->createdat = new \DateTime();
        }

       
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }



    public function getComments(): ?Order
    {
        return $this->comments;
    }

    public function setComments(?Order $comments): self
    {
        $this->comments = $comments;

        return $this;
    }


    public function getCreatedat(): ?\DateTimeInterface
    {
        return $this->createdat;
    }

    public function setCreatedat(\DateTimeInterface $createdat): self
    {
        $this->createdat = $createdat;

        return $this;
    }

    public function getMeal(): ?Meal
    {
        return $this->meal;
    }

    public function setMeal(?Meal $meal): self
    {
        $this->meal = $meal;

        return $this;
    }
}
