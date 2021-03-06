<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Comment;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MealRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MealRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Meal
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
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $coverImage;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="orders")
     */
    private $orders;


    /**
     * @ORM\OneToMany(targetEntity=Meal::class, mappedBy="author", orphanRemoval=true)
     */
    private $meals;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="meals")
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="meal")
     */
    private $comments;

    

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->meals = new ArrayCollection();
    }

    /**
     * Création d'une fonction pour permettre d'initialiser le slug (avant la persistance et avant la maj)
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initializeSlug(){

        if(empty($this->slug)){
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->title);
        }
    }

     /**
     * Permet de récupérer le commentaire d'un auteur par rapport à un menu
     *
     * @param User $author
     * @return Comment|null
     */
    public function getCommentFromAuthor(User $author){
        foreach($this->comments as $comment){
            if($comment->getAuthor() === $author) return $comment;
           
        }
        return null;
    }

    public function getAverageRatings(){
        // calcul de la somme des notes

        $sum = array_reduce($this->comments->toArray(),function($total,$comment){

            // on retourne le total + la note de chaque commentaire

            return $total + $comment->getRating();
        },0);

        // diviser le total par le nombre de notes

        if(count($this->comments) > 0) return $sum / count($this->comments);
            return 0;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

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

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }


    /**
     * @return Collection|self[]
     */
    public function getMeals(): Collection
    {
        return $this->meals;
    }

    public function addMeal(self $meal): self
    {
        if (!$this->meals->contains($meal)) {
            $this->meals[] = $meal;
            $meal->setAuthor($this);
        }

        return $this;
    }

    public function removeMeal(self $meal): self
    {
        if ($this->meals->removeElement($meal)) {
            // set the owning side to null (unless already changed)
            if ($meal->getAuthor() === $this) {
                $meal->setAuthor(null);
            }
        }

        return $this;
    }

      /**
     * @return Order
     *
     */
    public function getOrders()
    {
        return $this->orders;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setMeal($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getMeal() === $this) {
                $comment->setMeal(null);
            }
        }

        return $this;
    }



   
}
