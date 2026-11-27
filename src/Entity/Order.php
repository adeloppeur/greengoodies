<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Une commande d'un client.
 *
 * Le panier n'a pas de table à lui : c'est une commande au statut "cart".
 * Au clic sur "Valider la commande", elle passe au statut "validated"
 * et reçoit sa date de validation. Son numéro de commande = son id.
 */
#[ORM\Entity(repositoryClass: OrderRepository::class)]
// "order" est un mot réservé en SQL (ORDER BY) : les accents graves forcent Doctrine à l'entourer de guillemets.
#[ORM\Table(name: '`order`')]
class Order
{
    // Les deux statuts possibles, écrits une seule fois ici pour éviter les fautes de frappe ailleurs.
    public const STATUS_CART = 'cart';               // panier en cours
    public const STATUS_VALIDATED = 'validated';     // commande validée

    // Clé primaire, numérotée automatiquement par la base. Sert aussi de numéro de commande.
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Le client (ManyToOne : plusieurs commandes pour un client). Crée la colonne customer_id.
    // nullable: false = pas de commande sans client.
    // onDelete: 'CASCADE' = si le client est supprimé, la base supprime aussi ses commandes.
    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $customer = null;

    // "cart" ou "validated". Une nouvelle commande est toujours un panier.
    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_CART;

    // Date de validation : vide (null) tant que c'est un panier.
    // Immutable : une fois posée, la date ne change plus.
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $validatedAt = null;

    /**
     * Les lignes de la commande (un produit + une quantité par ligne).
     * Côté "inverse" de OrderItem::$parentOrder : la colonne parent_order_id est dans la table order_item.
     * orphanRemoval : une ligne retirée de la commande est supprimée de la base ("Vider le panier").
     *
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'parentOrder', orphanRemoval: true)]
    private Collection $items;

    public function __construct()                       // appelé à chaque "new Order()"
    {
        $this->items = new ArrayCollection();           // liste de lignes vide au départ
    }

    // Getters / setters générés par make:entity : formulaires, Twig et Doctrine passent par eux.

    public function getId(): ?int                       // "donne-moi le numéro de commande"
    {
        return $this->id;                               // pas de setId() : c'est la base qui numérote
    }

    public function getCustomer(): ?User                // "donne-moi le client de cette commande"
    {
        return $this->customer;                         // renvoie l'objet User, pas seulement son numéro
    }

    public function setCustomer(?User $customer): static  // "range ce client"
    {
        $this->customer = $customer;                    // on range le client reçu dans l'objet

        return $this;                                   // renvoie l'objet pour enchaîner
    }

    public function getStatus(): string                 // "donne-moi le statut"
    {
        return $this->status;                           // "cart" ou "validated"
    }

    public function setStatus(string $status): static   // "range ce statut"
    {
        $this->status = $status;                        // on range la valeur reçue dans l'objet

        return $this;                                   // renvoie l'objet pour enchaîner
    }

    public function getValidatedAt(): ?\DateTimeImmutable  // "donne-moi la date de validation"
    {
        return $this->validatedAt;                      // null si c'est encore un panier
    }

    public function setValidatedAt(?\DateTimeImmutable $validatedAt): static  // "range cette date"
    {
        $this->validatedAt = $validatedAt;              // on range la date reçue dans l'objet

        return $this;                                   // renvoie l'objet pour enchaîner
    }

    // Pour une relation, make:entity génère get / add / remove au lieu d'un simple set.

    /**
     * @return Collection<int, OrderItem>
     */
    public function getItems(): Collection              // "donne-moi toutes les lignes" (page panier)
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): static    // "ajoute cette ligne à la commande"
    {
        if (!$this->items->contains($item)) {           // si elle n'y est pas déjà
            $this->items->add($item);                   // on l'ajoute à la liste
            $item->setParentOrder($this);               // et la ligne connaît sa commande (les 2 côtés restent d'accord)
        }

        return $this;
    }

    public function removeItem(OrderItem $item): static // "retire cette ligne de la commande"
    {
        if ($this->items->removeElement($item)) {       // si elle était dans la liste, on l'enlève
            if ($item->getParentOrder() === $this) {    // et si elle pointait encore vers cette commande
                $item->setParentOrder(null);            // on coupe le lien (avec orphanRemoval, elle sera supprimée)
            }
        }

        return $this;
    }

    // Ajouté à la main : le total n'est pas stocké en base, on le calcule à partir des lignes.
    // Utilisé par le panier et par la liste des commandes de "Mon compte".
    public function getTotal(): float                   // "combien coûte toute la commande ?"
    {
        $total = 0;
        foreach ($this->items as $item) {               // pour chaque ligne
            $total += $item->getTotal();                // on ajoute son sous-total
        }

        return $total;
    }
}
