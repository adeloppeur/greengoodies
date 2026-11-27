<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Un client de GreenGoodies.
 *
 * Créée avec make:user (email, rôles, mot de passe) puis complétée avec
 * make:entity User (prénom, nom, accès API).
 * Les deux interfaces permettent au composant Security de connecter
 * l'utilisateur et de vérifier son mot de passe.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
// "user" est un mot réservé dans PostgreSQL : les accents graves forcent Doctrine à l'entourer de guillemets.
#[ORM\Table(name: '`user`')]
// Deux comptes ne peuvent pas avoir le même email (contrôle fait aussi par la base).
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // Clé primaire, numérotée automatiquement par la base.
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Identifiant de connexion (voir getUserIdentifier et le provider de security.yaml).
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * Rôles en plus de ROLE_USER, stockés en JSON. Vide pour un client normal.
     *
     * @var list<string>
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * Mot de passe haché (bcrypt), jamais le mot de passe en clair.
     * Le hachage est fait par UserPasswordHasherInterface à l'inscription.
     *
     * @var string
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    // Accès à l'API : désactivé à l'inscription, le client l'active depuis "Mon compte".
    // Sans cet accès, POST /api/login répond 403.
    #[ORM\Column(options: ['default' => false])]
    private bool $apiActive = false;

    /**
     * Les commandes du client (panier compris). Côté "inverse" de Order::$customer :
     * la colonne customer_id est dans la table order, pas ici.
     * orphanRemoval : supprimer le client supprime aussi ses commandes ("Supprimer mon compte").
     *
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'customer', orphanRemoval: true)]
    private Collection $orders;

    public function __construct()                       // appelé à chaque "new User()"
    {
        $this->orders = new ArrayCollection();          // liste de commandes vide au départ
    }

    // Les propriétés sont private : getters (lire) et setters (écrire) sont les seules portes d'accès.
    // ?string = un texte ou null. static + return $this = on peut enchaîner les set...().

    public function getId(): ?int                       // "donne-moi le numéro du client"
    {
        return $this->id;                               // pas de setId() : c'est la base qui numérote
    }

    public function getEmail(): ?string                 // "donne-moi l'email"
    {
        return $this->email;                            // renvoie la valeur rangée dans l'objet
    }

    public function setEmail(string $email): static     // "range cet email"
    {
        $this->email = $email;                          // on range la valeur reçue dans l'objet

        return $this;                                   // renvoie l'objet pour enchaîner
    }

    /**
     * Ce que Symfony utilise pour reconnaître l'utilisateur connecté : ici l'email.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string         // "qui est connecté ?"
    {
        return (string) $this->email;                   // l'email sert d'identifiant
    }

    /**
     * Ajoute toujours ROLE_USER : tout compte connecté a au moins ce rôle,
     * même si la colonne roles est vide. array_unique évite les doublons.
     *
     * @see UserInterface
     */
    public function getRoles(): array                   // "quels droits a ce client ?"
    {
        $roles = $this->roles;                          // les rôles enregistrés en base
        $roles[] = 'ROLE_USER';                         // + ROLE_USER, toujours

        return array_unique($roles);                    // sans doublon
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static      // "range ces rôles"
    {
        $this->roles = $roles;                          // on range la liste reçue dans l'objet

        return $this;                                   // renvoie l'objet pour enchaîner
    }

    /**
     * Renvoie le hash : le firewall le compare au mot de passe tapé.
     *
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string              // "donne-moi le mot de passe haché"
    {
        return $this->password;                         // le hash, jamais le mot de passe en clair
    }

    public function setPassword(string $password): static  // "range ce hash"
    {
        $this->password = $password;                    // on reçoit un mot de passe DÉJÀ haché

        return $this;                                   // renvoie l'objet pour enchaîner
    }

    /**
     * Appelée quand l'utilisateur est mis en session : on y range une empreinte
     * courte (crc32c) du hash à la place du vrai hash. Ajouté par make:user depuis Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
    }

    // Getters / setters générés par make:entity : formulaires, Twig et Doctrine passent par eux.
    public function getFirstname(): ?string             // "donne-moi le prénom"
    {
        return $this->firstname;                        // renvoie la valeur rangée dans l'objet
    }

    public function setFirstname(string $firstname): static  // "range ce prénom"
    {
        $this->firstname = $firstname;                  // on range la valeur reçue dans l'objet

        return $this;                                   // renvoie l'objet pour enchaîner
    }

    public function getLastname(): ?string              // "donne-moi le nom"
    {
        return $this->lastname;                         // renvoie la valeur rangée dans l'objet
    }

    public function setLastname(string $lastname): static  // "range ce nom"
    {
        $this->lastname = $lastname;                    // on range la valeur reçue dans l'objet

        return $this;                                   // renvoie l'objet pour enchaîner
    }

    public function isApiActive(): bool                 // "l'accès API est-il activé ?" (is... pour un oui/non)
    {
        return $this->apiActive;                        // true = activé, false = désactivé
    }

    // Utilisé par le bouton "Activer / Désactiver mon accès API" de la page Mon compte.
    public function setApiActive(bool $apiActive): static  // "active ou désactive l'accès API"
    {
        $this->apiActive = $apiActive;                  // on range true ou false dans l'objet

        return $this;                                   // renvoie l'objet pour enchaîner
    }

    // Pour une relation, make:entity génère get / add / remove au lieu d'un simple set.

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection             // "donne-moi toutes ses commandes" (page Mon compte)
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static      // "ajoute cette commande au client"
    {
        if (!$this->orders->contains($order)) {         // si elle n'y est pas déjà
            $this->orders->add($order);                 // on l'ajoute à la liste
            $order->setCustomer($this);                 // et la commande connaît son client (les 2 côtés restent d'accord)
        }

        return $this;
    }

    public function removeOrder(Order $order): static   // "retire cette commande au client"
    {
        if ($this->orders->removeElement($order)) {     // si elle était dans la liste, on l'enlève
            if ($order->getCustomer() === $this) {      // et si elle pointait encore vers ce client
                $order->setCustomer(null);              // on coupe le lien (avec orphanRemoval, elle sera supprimée)
            }
        }

        return $this;
    }
}
