<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

// Annotation pour indiquer que cette classe est une entité Doctrine et spécifier le repository associé
#[ORM\Entity(repositoryClass: UserRepository::class)]
// Annotation pour spécifier la table dans la base de données
#[ORM\Table(name: '`user`')]
// Définir une contrainte unique sur le champ email
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // Clé primaire de l'entité
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Champ pour le prénom de l'utilisateur
    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    // Champ pour le nom de famille de l'utilisateur
    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    // Champ pour l'email de l'utilisateur
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    // Tableau pour stocker les rôles de l'utilisateur
    #[ORM\Column]
    private array $roles = [];

    // Champ pour stocker le mot de passe haché de l'utilisateur
    #[ORM\Column]
    private ?string $password = null;

    // Relation OneToMany avec l'entité Address
    #[ORM\OneToMany(targetEntity: Address::class, mappedBy: 'user')]
    private Collection $addresses;

    // Relation OneToMany avec l'entité Order
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'user')]
    private Collection $orders;

    // Champ pour stocker un jeton (par exemple, pour la récupération de mot de passe)
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    // Champ pour stocker la date d'expiration du jeton
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $tokenExpireAt = null;

    // Champ pour stocker la dernière date de connexion de l'utilisateur
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastloginAt = null;

    // Relation ManyToMany avec l'entité Product pour la liste de souhaits de l'utilisateur
    #[ORM\ManyToMany(targetEntity: Product::class)]
    private Collection $wishlists;

    // Constructeur pour initialiser les collections
    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->wishlists = new ArrayCollection();
    }

    // Méthode __toString pour retourner une représentation textuelle de l'utilisateur
    public function __toString()
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

    // Getter pour l'id
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter et Setter pour l'email
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    // Méthode pour obtenir l'identifiant de l'utilisateur (obligatoire pour UserInterface)
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    // Getter et Setter pour les rôles
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // Ajouter le rôle utilisateur par défaut
        return array_unique($roles); // Éliminer les doublons
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    // Getter et Setter pour le mot de passe
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    // Méthode pour effacer les informations sensibles de l'utilisateur (obligatoire pour UserInterface)
    public function eraseCredentials(): void
    {
        // Si des données sensibles sont stockées temporairement, les effacer ici
        // $this->plainPassword = null;
    }

    // Getter et Setter pour le prénom
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;
        return $this;
    }

    // Getter et Setter pour le nom de famille
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;
        return $this;
    }

    // Méthodes pour gérer la relation OneToMany avec Address
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): static
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setUser($this);
        }
        return $this;
    }

    public function removeAddress(Address $address): static
    {
        if ($this->addresses->removeElement($address)) {
            // Si l'adresse était associée à cet utilisateur, dissocier l'adresse
            if ($address->getUser() === $this) {
                $address->setUser(null);
            }
        }
        return $this;
    }

    // Méthodes pour gérer la relation OneToMany avec Order
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUser($this);
        }
        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // Si la commande était associée à cet utilisateur, dissocier la commande
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }
        return $this;
    }

    // Getter et Setter pour le jeton
    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;
        return $this;
    }

    // Getter et Setter pour la date d'expiration du jeton
    public function getTokenExpireAt(): ?\DateTimeInterface
    {
        return $this->tokenExpireAt;
    }

    public function setTokenExpireAt(?\DateTimeInterface $tokenExpireAt): static
    {
        $this->tokenExpireAt = $tokenExpireAt;
        return $this;
    }

    // Getter et Setter pour la dernière date de connexion
    public function getLastloginAt(): ?\DateTimeInterface
    {
        return $this->lastloginAt;
    }

    public function setLastloginAt(?\DateTimeInterface $lastloginAt): static
    {
        $this->lastloginAt = $lastloginAt;
        return $this;
    }

    // Méthodes pour gérer la relation ManyToMany avec Product (liste de souhaits)
    public function getWishlists(): Collection
    {
        return $this->wishlists;
    }

    public function addWishlist(Product $wishlist): static
    {
        if (!$this->wishlists->contains($wishlist)) {
            $this->wishlists->add($wishlist);
        }
        return $this;
    }

    public function removeWishlist(Product $wishlist): static
    {
        $this->wishlists->removeElement($wishlist);
        return $this;
    }
}
