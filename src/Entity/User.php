<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use App\Entity\BookingTable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 * fields={"email"},
 * message = "Cet email est déjà utilisé")
 */
class User implements UserInterface  // implémentation de UserInterface permettant d'utiliser les fonctions pour générer le mot de passe crypter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="Veuillez renseigner un mail valide")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     */
    private $avatar;

      /**
     * @ORM\Column(type="string", length=255)
     */
    private $hash;

    /**
     * Comparaison du champ ci-dessous avec le champ hash
     * 
     * @Assert\EqualTo(propertyPath="hash",message="Les 2 mots de passe ne correspondent pas.")
     */
    public $passwordConfirm;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=30,minMessage="Votre description doit comporter au moins 30 caractères")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class, mappedBy="users")
     */
    private $userRoles;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class, mappedBy="users")
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity=BookingTable::class, mappedBy="users")
     */
    private $users;

    public function getFullName(){
        return "{$this->firstname} {$this->lastname}";
    }

    public function __construct()
    {

        $this->userRoles = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->date = new ArrayCollection();
    
    }


    /**
     * Création d'une fonction pour permettre d'initialiser le slug (avant la persistance et avant la maj)
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initializeSlug(){

        if(empty($this->slug)){
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->firstname. ' '.$this->lastname);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getRoles(){

        $roles = $this->userRoles->map(function($role){
            return $role->getTitle();
        })->toArray();

        $roles[]='ROLE_USER';

        return $roles;
    }

    public function getPassword(){

        return $this->hash;
    }

    public function getSalt(){}

    public function getUsername(){

        return $this->email;
    }

    public function eraseCredentials(){}

    /**
     * @return Collection|Role[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(Role $userRole): self
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles[] = $userRole;
            $userRole->addUser($this);
        }

        return $this;
    }

    public function removeUserRole(Role $userRole): self
    {
        if ($this->userRoles->removeElement($userRole)) {
            $userRole->removeUser($this);
        }

        return $this;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
            $role->addUser($this);
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->roles->removeElement($role)) {
            $role->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|BookingTable[]
     */
    public function getDate(): Collection
    {
        return $this->date;
    }

    public function addDate(BookingTable $date): self
    {
        if (!$this->date->contains($date)) {
            $this->date[] = $date;
            $date->setUsers($this);
        }

        return $this;
    }

    public function removeDate(BookingTable $date): self
    {
        if ($this->date->removeElement($date)) {
            // set the owning side to null (unless already changed)
            if ($date->getUsers() === $this) {
                $date->setUsers(null);
            }
        }

        return $this;
    }

     /**
     * @return BookingTable
     *
     */
    public function getUsers()
    {
        return $this->users;
    }
}